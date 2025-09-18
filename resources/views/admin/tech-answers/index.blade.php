{{-- resources/views/admin/tech-answers/index.blade.php --}}
<x-app-admin>
  {{-- HAPUS alert ini:
  @if (session('success'))
    <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-2 text-sm">
      {{ session('success') }}
    </div>
  @endif
  --}}

  <div x-data="scoreModal()" x-init="init()" x-cloak>
    <h1 class="text-2xl font-bold text-blue-950 mb-6">Penilaian Technical Test</h1>

    {{-- Filter: Batch + Position + Search --}}
    <form method="GET" class="bg-white border rounded-lg p-4 mb-5">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
          <label class="text-sm text-gray-600">Batch</label>
          <select name="batch_id" class="w-full border rounded px-3 py-2">
            <option value="">— Semua —</option>
            @foreach ($batches as $b)
              <option value="{{ $b->id }}" @selected(request('batch_id')==$b->id)>
                {{ $b->name ?? ('Batch #'.$b->id) }}
                @if(!empty($b->start_date))
                  — {{ \Illuminate\Support\Carbon::parse($b->start_date)->format('d M Y') }}
                @endif
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
          <label class="text-sm text-gray-600">Cari peserta (nama/email)</label>
          <input type="text" name="q" value="{{ request('q') }}" class="w-full border rounded px-3 py-2" placeholder="Ketik nama atau email...">
        </div>
      </div>
      <div class="mt-3 flex items-center gap-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Terapkan</button>
        <a href="{{ route('tech-answers.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Reset</a>
      </div>
    </form>

    {{-- Tabel --}}
    <div class="bg-white border rounded-lg">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 text-left text-gray-700">
            <tr>
              <th class="px-4 py-2">Peserta</th>
              <th class="px-4 py-2">Posisi</th>
              <th class="px-4 py-2">Submitted</th>
              <th class="px-4 py-2">Screen Record</th>
              <th class="px-4 py-2">PDF</th>
              <th class="px-4 py-2">Skor</th>
              <th class="px-4 py-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($answers as $a)
              @php
                $app = $a->applicant;
                $pos = $app?->position;
                $sch = $a->schedule;
                $pdfUrl = $a->answer_url;
              @endphp
              <tr class="border-t">
                <td class="px-4 py-2">
                  <div class="font-medium">{{ $app->name ?? '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $app->email ?? '' }}</div>
                </td>
                <td class="px-4 py-2">{{ $pos->name ?? '—' }}</td>
                <td class="px-4 py-2">
                  {{ optional($a->submitted_at)->format('d M Y H:i') ?? '—' }}
                </td>
                <td class="px-4 py-2">
                  @if($a->screen_record_url)
                    <a href="{{ $a->screen_record_url }}" target="_blank" class="text-blue-600 hover:underline">Buka</a>
                  @else
                    <span class="text-gray-400">—</span>
                  @endif
                </td>
                <td class="px-4 py-2">
                  @if($pdfUrl)
                    <a href="{{ $pdfUrl }}" target="_blank" class="text-blue-600 hover:underline">Lihat PDF</a>
                  @else
                    <span class="text-gray-400">—</span>
                  @endif
                </td>
                <td class="px-4 py-2">
                  @if(!is_null($a->score))
                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                      {{ rtrim(rtrim(number_format($a->score, 2, '.', ''), '0'), '.') }}
                    </span>
                  @else
                    <span class="text-amber-600">Belum</span>
                  @endif
                </td>
                <td class="px-4 py-2">
                  <button type="button"
                          class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700"
                          @click="openWith({
                            action: '{{ route('tech-answers.update', $a) }}',
                            name: @js($app->name ?? '—'),
                            email: @js($app->email ?? ''),
                            posisi: @js($pos->name ?? '—'),
                            schedule: @js(($sch->title ?? ($sch?->id ? 'Schedule #'.$sch->id : null))),
                            scheduleDate: @js(!empty($sch?->schedule_date) ? \Illuminate\Support\Carbon::parse($sch->schedule_date)->format('d M Y H:i') : null),
                            pdfUrl: @js($pdfUrl),
                            screenUrl: @js($a->screen_record_url),
                            score: @js(!is_null($a->score) ? (string)$a->score : ''),
                            keterangan: @js($a->keterangan ?? '')
                          })">
                    Nilai
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada jawaban.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="p-4">
        {{ $answers->links() }}
      </div>
    </div>

    {{-- === MODAL PENILAIAN === --}}
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-40 bg-black/40"></div>

    <div x-show="open" x-trap.inert.noscroll="open"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div @click.outside="open=false"
           class="w-full max-w-6xl bg-white rounded-2xl shadow-lg border overflow-hidden">

        <div class="flex items-center justify-between px-5 py-3 border-b">
          <h3 class="text-lg font-semibold">Penilaian Technical Test</h3>
          <button class="p-2 rounded hover:bg-gray-100" @click="open=false" aria-label="Tutup">&times;</button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
          {{-- Kiri: PDF --}}
          <div class="lg:col-span-2 border-r">
            <template x-if="pdfUrl">
              <iframe :src="pdfUrl" class="w-full h-[75vh]" title="Jawaban PDF"></iframe>
            </template>
            <template x-if="!pdfUrl">
              <div class="h-[75vh] flex items-center justify-center text-gray-500">Peserta belum mengunggah PDF.</div>
            </template>
          </div>

          {{-- Kanan: Detail + Form --}}
          <div class="p-5">
            <div class="mb-4 space-y-1 text-sm">
              <div><span class="text-gray-500">Nama:</span> <span class="font-medium" x-text="name"></span></div>
              <div x-show="email"><span class="text-gray-500">Email:</span> <span class="font-medium" x-text="email"></span></div>
              <div><span class="text-gray-500">Posisi:</span> <span class="font-medium" x-text="posisi"></span></div>
              <template x-if="schedule">
                <div><span class="text-gray-500">Schedule:</span> <span class="font-medium" x-text="schedule"></span></div>
              </template>
              <template x-if="scheduleDate">
                <div><span class="text-gray-500">Tanggal:</span> <span class="font-medium" x-text="scheduleDate"></span></div>
              </template>
              <div class="mt-2">
                <span class="text-gray-500">Screen Record:</span>
                <template x-if="screenUrl">
                  <a :href="screenUrl" target="_blank" class="text-blue-600 hover:underline break-all" x-text="screenUrl"></a>
                </template>
                <template x-if="!screenUrl">
                  <span class="text-gray-400">—</span>
                </template>
              </div>
            </div>

            <form :action="action" method="POST" class="space-y-3">
              @csrf
              @method('PATCH')

              <div>
                <label class="text-sm text-gray-600">Nilai (0–100)</label>
                <input type="number" step="0.01" min="0" max="100" name="score"
                       x-model="score"
                       class="w-full border rounded px-3 py-2">
                @error('score')
                  <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror

                <div class="flex flex-wrap gap-2 mt-2">
                  @foreach ([0,60,70,80,90,100] as $q)
                    <button type="button"
                            @click="score='{{ $q }}'"
                            class="px-2 py-1 border rounded text-sm hover:bg-gray-50">
                      {{ $q }}
                    </button>
                  @endforeach
                </div>
              </div>

              <div>
                <label class="text-sm text-gray-600">Catatan/Keterangan (opsional)</label>
                <textarea name="keterangan" rows="5" x-model="keterangan" class="w-full border rounded px-3 py-2"></textarea>
                @error('keterangan')
                  <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="pt-1 flex items-center gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Nilai</button>
                <button type="button" class="px-4 py-2 border rounded hover:bg-gray-50" @click="open=false">Batal</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>

    {{-- === MODAL SUKSES (BARU) – seragam dengan Essay === --}}
    <div x-show="successOpen" x-transition.opacity class="fixed inset-0 z-[60] bg-black/40"></div>

    <div x-show="successOpen" x-trap.inert.noscroll="successOpen"
         x-transition
         class="fixed inset-0 z-[70] flex items-center justify-center p-4">
      <div @click.outside="successOpen=false"
           role="dialog" aria-modal="true"
           class="w-full max-w-md bg-white rounded-2xl shadow-lg border overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b">
          <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.172 7.707 8.879a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <h3 class="text-lg font-semibold">Berhasil</h3>
          </div>
          <button class="p-2 rounded hover:bg-gray-100" @click="successOpen=false" aria-label="Tutup">&times;</button>
        </div>

        <div class="p-5">
          <p class="text-sm text-gray-700" x-text="successMessage || 'Nilai technical test berhasil disimpan.'"></p>
        </div>

        <div class="px-5 pb-4">
          <button @click="successOpen=false"
                  class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Oke
          </button>
        </div>
      </div>
    </div>

  </div> {{-- x-data --}}
</x-app-admin>

{{-- Alpine helper --}}
<script>
  function scoreModal() {
    return {
      // Data modal penilaian
      open: false,
      action: '',
      name: '',
      email: '',
      posisi: '',
      schedule: '',
      scheduleDate: '',
      pdfUrl: '',
      screenUrl: '',
      score: '',
      keterangan: '',

      // Modal sukses (flash)
      successOpen: false,
      successMessage: '',

      init() {
        // Controller tech test umumnya pakai session('success')
        this.successMessage = @js(session('success'));
        if (this.successMessage) this.successOpen = true;
      },

      openWith(data) {
        Object.assign(this, data);
        this.open = true;
      }
    }
  }
</script>
