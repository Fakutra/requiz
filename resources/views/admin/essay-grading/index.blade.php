{{-- resources/views/admin/essay-grading/index.blade.php --}}
<x-app-admin>
  <div x-data="essayModal()" x-cloak>
    <h1 class="text-2xl font-bold text-blue-950 mb-6">Penilaian Essay</h1>

    @if (session('status'))
      <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-2 text-sm">
        {{ session('status') }}
      </div>
    @endif

    {{-- Ringkasan --}}
    {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
      <div class="bg-white border rounded-lg p-4">
        <div class="text-sm text-gray-500">Total Essay</div>
        <div class="text-2xl font-semibold">{{ $counts['total'] }}</div>
      </div>
      <div class="bg-white border rounded-lg p-4">
        <div class="text-sm text-gray-500">Sudah Dinilai</div>
        <div class="text-2xl font-semibold text-green-600">{{ $counts['scored'] }}</div>
      </div>
      <div class="bg-white border rounded-lg p-4">
        <div class="text-sm text-gray-500">Belum Dinilai</div>
        <div class="text-2xl font-semibold text-amber-600">{{ $counts['unscored'] }}</div>
      </div>
    </div> --}}

    {{-- Filter: Batch + Position + Search + Pending only --}}
    <form method="GET" class="bg-white border rounded-lg p-4 mb-5">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
          <label class="text-sm text-gray-600">Batch</label>
          <select name="batch_id" class="w-full border rounded px-3 py-2">
            <option value="">— Semua —</option>
            @foreach ($batches as $b)
              <option value="{{ $b->id }}" @selected(request('batch_id')==$b->id)>
                {{ $b->name ?? ('Batch #'.$b->id) }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm text-gray-600">Posisi</label>
          <select name="position_id" class="w-full border rounded px-3 py-2">
            <option value="">— Semua —</option>
            @foreach ($positions as $p)
              <option value="{{ $p->id }}" @selected(request('position_id')==$p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="text-sm text-gray-600">Cari (nama/email/test)</label>
          <input type="text" name="q" value="{{ request('q') }}"
                 class="w-full border rounded px-3 py-2" placeholder="Ketik nama, email, atau nama test...">
        </div>

        <div class="flex items-end">
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="pending_only" value="1"
                   class="rounded border-gray-300"
                   @checked(request('pending_only'))>
            Pending saja
          </label>
        </div>
      </div>

      <div class="mt-3 flex items-center gap-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Terapkan</button>
        <a href="{{ route('essay_grading.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Reset</a>
      </div>
    </form>

    {{-- Tabel --}}
    <div class="bg-white shadow-sm rounded-2xl p-0 border border-gray-200">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 text-left text-gray-700">
            <tr>
              <th class="px-4 py-2">Nama</th>
              <th class="px-4 py-2">Test</th>
              <th class="px-4 py-2">Section</th>
              <th class="px-4 py-2">Total Essay</th>
              <th class="px-4 py-2">Pending</th>
              <th class="px-4 py-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($results as $r)
            @php
              $sectionsWithEssay = $r->sectionResults->filter(fn($sr) => $sr->answers->count() > 0);

              $totalEssay   = $sectionsWithEssay->sum(fn($sr) => $sr->answers->count());
              $pendingEssay = $sectionsWithEssay->sum(fn($sr) => $sr->answers->whereNull('score')->count());

              $essayItems = $sectionsWithEssay->flatMap(function ($sr) {
                  $sectionName = optional($sr->testSection)->name ?? 'Section';
                  return $sr->answers->map(function ($a) use ($sectionName) {
                      return [
                          'id'       => $a->id,
                          'section'  => $sectionName,
                          'question' => (string) optional($a->question)->question,
                          'answer'   => (string) $a->answer,
                          'score'    => is_null($a->score) ? '' : (string) $a->score,
                      ];
                  });
              })->values();
            @endphp

            <tr class="border-t align-top">
              <td class="px-4 py-2">
                <div class="font-medium">{{ $r->applicant->name ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ $r->applicant->email ?? '' }}</div>
              </td>
              <td class="px-4 py-2">{{ $r->test->name ?? '—' }}</td>
              <td class="px-4 py-2">
                <div class="flex flex-wrap gap-2">
                  @forelse ($sectionsWithEssay as $sr)
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-800 text-xs">
                      {{ optional($sr->testSection)->name ?? 'Section' }}
                    </span>
                  @empty
                    —
                  @endforelse
                </div>
              </td>
              <td class="px-4 py-2">{{ $totalEssay }}</td>
              <td class="px-4 py-2">
                <span class="px-2 py-0.5 rounded-full text-xs
                             {{ $pendingEssay > 0 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                  {{ $pendingEssay }}
                </span>
              </td>
              <td class="px-4 py-2">
                @if ($totalEssay > 0)
                  <button type="button"
                          class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700"
                          @click="openWith({
                            action: '{{ route('essay_grading.update_result', $r->id) }}',
                            name: @js($r->applicant->name ?? '—'),
                            email: @js($r->applicant->email ?? ''),
                            test: @js($r->test->name ?? '—'),
                            essays: @js($essayItems),
                          })">
                    Nilai Essay
                  </button>
                @else
                  —
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-6 text-center text-gray-500" colspan="6">Tidak ada peserta dengan essay.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="p-4">
        {{ $results->links() }}
      </div>
    </div>

    {{-- === MODAL === --}}
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-40 bg-black/40"></div>

    <div x-show="open" x-trap.inert.noscroll="open"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div @click.outside="open=false"
           class="w-full max-w-6xl bg-white rounded-2xl shadow-lg border overflow-hidden">

        <div class="flex items-center justify-between px-5 py-3 border-b">
          <h3 class="text-lg font-semibold">Penilaian Essay</h3>
          <button class="p-2 rounded hover:bg-gray-100" @click="open=false" aria-label="Tutup">&times;</button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
          {{-- Kiri: daftar essay --}}
          <div class="lg:col-span-2 border-r max-h-[75vh] overflow-y-auto p-5 space-y-4">
            <template x-if="essays.length === 0">
              <div class="text-gray-500">Tidak ada jawaban essay.</div>
            </template>

            <template x-for="(it, idx) in essays" :key="it.id">
              <div class="border rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700" x-text="it.section"></span>
                  <span class="text-xs text-gray-500">ID: <span x-text="it.id"></span></span>
                </div>

                <div class="mb-2">
                  <div class="text-xs text-gray-500 mb-1">Soal</div>
                  <div class="whitespace-pre-line font-medium" x-text="it.question"></div>
                </div>

                <div class="mb-3">
                  <div class="text-xs text-gray-500 mb-1">Jawaban Peserta</div>
                  <div class="border rounded px-3 py-2 text-sm whitespace-pre-line max-h-56 overflow-auto bg-gray-50"
                       x-text="it.answer || '—'"></div>
                </div>

                <div>
                  <label class="text-sm text-gray-600">Nilai</label>
                  <div class="flex items-center gap-3 mt-1">
                    <input type="number" min="0" max="100" step="1"
                           class="w-28 border rounded px-3 py-2"
                           :name="'scores['+it.id+']'"
                           x-model="scores[it.id]">
                    
                  </div>
                </div>
              </div>
            </template>
          </div>

          {{-- Kanan: info peserta + aksi --}}
          <div class="p-5">
            <div class="mb-4 space-y-1 text-sm">
              <div><span class="text-gray-500">Nama:</span> <span class="font-medium" x-text="name"></span></div>
              <div x-show="email"><span class="text-gray-500">Email:</span> <span class="font-medium" x-text="email"></span></div>
              <div><span class="text-gray-500">Test:</span> <span class="font-medium" x-text="test"></span></div>
              <div class="text-xs text-gray-500 mt-2">Total soal: <span x-text="essays.length"></span></div>
            </div>

            <form :action="action" method="POST" class="space-y-3">
              @csrf
              @method('PATCH')
              {{-- Input nilai dibuat dinamis per-essay di panel kiri --}}
              <div class="pt-1 flex items-center gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Nilai</button>
                <button type="button" class="px-4 py-2 border rounded hover:bg-gray-50" @click="open=false">Batal</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>

  </div> {{-- x-data --}}
</x-app-admin>

{{-- Alpine helper --}}
<script>
  function essayModal() {
    return {
      open: false,
      action: '',
      name: '',
      email: '',
      test: '',
      essays: [],
      scores: {}, // answerId -> score
      openWith(payload) {
        this.action = payload.action;
        this.name   = payload.name || '';
        this.email  = payload.email || '';
        this.test   = payload.test  || '';
        this.essays = Array.isArray(payload.essays) ? payload.essays : [];
        this.scores = {};
        for (const it of this.essays) this.scores[it.id] = (it.score ?? '');
        this.open = true;
      }
    }
  }
</script>
