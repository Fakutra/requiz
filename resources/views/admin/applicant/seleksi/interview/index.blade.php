  <x-app-admin>
    <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
      <div class="relative flex items-center gap-2 mb-4">
        <a href="{{ route('admin.applicant.seleksi.index') }}" 
          class="text-gray-600 hover:text-gray-900 flex items-center">
          <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
        </a>

        <h2 class="text-lg font-semibold leading-none m-0">Interview</h2>
      </div>

      {{-- Toolbar --}}
      <div class="flex w-full mb-2 items-end gap-2">
        <form method="GET" class="flex-1 min-w-[220px]">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">
          <div class="relative flex items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                  placeholder="Nama / Email / Jurusan / Posisi..."
                  class="w-full h-10 pl-3 pr-9 border rounded text-sm focus:ring focus:border-blue-500">
            <span class="absolute right-3 text-gray-500">
              <x-search-button/>
            </span>
          </div>
        </form>

      <div class="flex gap-2">
        {{-- Filter --}}
        <button type="button"
                onclick="document.getElementById('filterModal').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-gray-600 text-white flex items-center justify-center"
                title="Filter">
          <i class="fas fa-filter"></i>
        </button>

        {{-- Email --}}
        <button type="button"
                onclick="document.getElementById('emailModalInterview').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center"
                title="Kirim Email">
          <i class="fas fa-envelope"></i>
        </button>

        {{-- Export --}}
        <a href="{{ route('admin.applicant.seleksi.interview.export', request()->query()) }}"
          class="px-3 py-2 border rounded bg-green-600 text-white flex items-center justify-center"
          title="Export">
          <i class="fas fa-file-export"></i>
        </a>

        {{-- Lolos --}}
        {{-- Lolos --}}
        <button type="button"
                onclick="openConfirmModal('lolos')"
                class="px-3 py-2 rounded bg-blue-600 text-white">
          Lolos
        </button>

        {{-- Gagal --}}
        <button type="button"
                onclick="openConfirmModal('tidak_lolos')"
                class="px-3 py-2 rounded bg-red-600 text-white">
          Gagal
        </button>
      </div>
    </div>

    {{-- Table --}}
    <form id="bulkActionForm" method="POST" action="{{ route('admin.applicant.seleksi.interview.bulkMark') }}">
      @csrf
      <div class="overflow-x-auto">
        <table class="table-auto text-sm border w-full">
          <thead class="bg-gray-100 text-gray-800">
            <tr>
              <th class="px-3 py-2"><input type="checkbox" id="checkAll"></th>
              {{-- Nama Peserta --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'name',
                    'direction' => (request('sort') === 'name' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Nama Peserta
                  <svg class="w-4 h-4 transform {{ request('sort') === 'name' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              {{-- Universitas --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'universitas',
                    'direction' => (request('sort') === 'universitas' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Universitas
                  <svg class="w-4 h-4 transform {{ request('sort') === 'universitas' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              {{-- Jurusan --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'jurusan',
                    'direction' => (request('sort') === 'jurusan' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Jurusan
                  <svg class="w-4 h-4 transform {{ request('sort') === 'jurusan' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              {{-- Posisi --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'posisi',
                    'direction' => (request('sort') === 'posisi' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Posisi
                  <svg class="w-4 h-4 transform {{ request('sort') === 'posisi' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              {{-- Ekspektasi Gaji --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'ekspektasi_gaji',
                    'direction' => (request('sort') === 'ekspektasi_gaji' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Ekspektasi Gaji
                  <svg class="w-4 h-4 transform {{ request('sort') === 'ekspektasi_gaji' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              {{-- Dokumen --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'dokumen',
                    'direction' => (request('sort') === 'dokumen' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Dokumen
                  <svg class="w-4 h-4 transform {{ request('sort') === 'dokumen' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              {{-- Score Quiz --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'score_quiz',
                    'direction' => (request('sort') === 'score_quiz' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Score Quiz
                  <svg class="w-4 h-4 transform {{ request('sort') === 'score_quiz' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              {{-- Score Praktik --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'score_praktik',
                    'direction' => (request('sort') === 'score_praktik' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Score Praktik
                  <svg class="w-4 h-4 transform {{ request('sort') === 'score_praktik' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              {{-- Score Interview --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'score_interview',
                    'direction' => (request('sort') === 'score_interview' && request('direction','asc') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Score Interview
                  <svg class="w-4 h-4 transform {{ request('sort') === 'score_interview' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
              </th>

              <th class="px-3 py-2 text-left whitespace-nowrap">Potential By</th>
              <th class="px-3 py-2 text-left whitespace-nowrap">Status Email</th>
              <th class="px-3 py-2 text-left whitespace-nowrap">Status</th>
              <th class="px-3 py-2 text-left whitespace-nowrap">Note</th>
              <th class="px-3 py-2 text-left whitespace-nowrap">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($applicants as $a)
              @php
                  $myResult = \App\Models\InterviewResult::where('applicant_id', $a->id)
                                ->where('user_id', auth()->id())
                                ->first();
              @endphp
              <tr>
                <td class="px-3 py-2 whitespace-nowrap"><input type="checkbox" name="ids[]" value="{{ $a->id }}"></td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->name }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->universitas ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->jurusan }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->position->name ?? '-' }}</td>
                <td class="px-3 py-2 text-left">
                  {{ $a->ekspektasi_gaji_formatted ?? '-' }}
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  @if($a->cv_document)
                    <a href="{{ asset('storage/'.$a->cv_document) }}" target="_blank" class="text-blue-600 hover:underline">
                      <i class="fas fa-file-pdf"></i>
                    </a>
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  {{ $a->quiz_final ? $a->quiz_final.' / '.$a->quiz_max : '-' }}
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  {{ $a->praktik_final ? $a->praktik_final.' / '.$a->praktik_max : '-' }}
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  {{ $a->interview_final ? number_format($a->interview_final, 2).' / '.$a->interview_max : '-' }}
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                  {{ $a->potential_by ? implode(', ', $a->potential_by) : '-' }}
                </td>

                {{-- Email Status --}}
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  @php
                    $log = $a->latestEmailLog;
                    if ($log && $log->stage !== 'Interview') {
                        $log = null;
                    }
                  @endphp
                  @if($log)
                    @if($log->success)
                      <i class="fas fa-check-circle text-green-500" title="Terkirim"></i>
                    @else
                      <i class="fas fa-times-circle text-red-500" title="Gagal: {{ $log->error }}"></i>
                    @endif
                  @else
                    <i class="fas fa-minus-circle text-gray-400" title="Belum dikirim"></i>
                  @endif
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                  @php
                    // Mapping status untuk tampilan di Seleksi Interview
                    $lolosInterviewStatuses = [
                        'Offering',
                        'Menerima Offering',
                        'Menolak Offering',
                    ];

                    if (in_array($a->status, $lolosInterviewStatuses)) {
                        $displayStatus = 'Lolos Interview';
                    } elseif ($a->status === 'Tidak Lolos Interview') {
                        $displayStatus = 'Tidak Lolos Interview';
                    } elseif ($a->status === 'Interview') {
                        $displayStatus = 'Interview';
                    } else {
                        $displayStatus = $a->status;
                    }

                    // Tentukan warna badge
                    $isLolos = \Illuminate\Support\Str::startsWith($displayStatus, 'Lolos');
                    $isTidak = \Illuminate\Support\Str::startsWith($displayStatus, 'Tidak Lolos');

                    $badgeClass = $isLolos
                        ? 'bg-[#69FFA0] text-[#2C6C44]'
                        : ($isTidak ? 'bg-[#FFDDDD] text-[#FF2525]' : 'bg-yellow-100 text-yellow-700');
                @endphp

                <span class="px-2 py-1 text-xs rounded {{ $badgeClass }}">
                    {{ $displayStatus }}
                </span>

                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                  @if($a->interviewResults && $a->interviewResults->count() > 0)
                    @foreach($a->interviewResults as $r)
                      @if(!empty($r->note))
                        <div class="mb-1">
                          <span class="font-semibold text-sm text-gray-700">{{ $r->user->name ?? 'Tanpa Nama' }}:</span>
                          <span class="text-sm text-gray-800">{{ $r->note }}</span>
                        </div>
                      @endif
                    @endforeach
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>

                {{-- Aksi --}}
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  <i class="fas fa-pen text-blue-600 cursor-pointer hover:text-blue-800"
                    title="Input Penilaian Interview"
                    onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.remove('hidden')"></i>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="12" class="text-center text-gray-500 py-5">Tidak ada data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </form>

    <div class="mt-3">{{ $applicants->links() }}</div>
  </div>

  {{-- Modal Input Score Interview --}}
  @foreach($applicants as $a)
  @php
      $myResult = \App\Models\InterviewResult::where('applicant_id', $a->id)
                    ->where('user_id', auth()->id())
                    ->first();
  @endphp
  <div id="scoreModal-{{ $a->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h3 class="text-lg font-semibold">Input Score Interview</h3>
        <button type="button"
                onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700">&times;</button>
      </div>

      <form method="POST" action="{{ route('admin.applicant.seleksi.interview.storeScore') }}">
        @csrf
        <input type="hidden" name="applicant_id" value="{{ $a->id }}">

        <div class="mb-3">
          <label class="block text-sm">Poin Kepribadian</label>
          <input type="number" name="poin_kepribadian"
                value="{{ $myResult->poin_kepribadian ?? '' }}"
                class="border rounded w-full px-2 py-1" 
                placeholder="1 - 100" 
                min="1"
                max="100"
                required>
        </div>

        <div class="mb-3">
          <label class="block text-sm">Poin Wawasan</label>
          <input type="number" name="poin_wawasan"
                value="{{ $myResult->poin_wawasan ?? '' }}"
                class="border rounded w-full px-2 py-1" 
                placeholder="1 - 100"
                min="1"
                max="100"
                required>
        </div>

        <div class="mb-3">
          <label class="block text-sm">Poin Gestur</label>
          <input type="number" name="poin_gestur"
                 value="{{ $myResult->poin_gestur ?? '' }}"
                 class="border rounded w-full px-2 py-1" 
                 placeholder="1 - 100"
                 min="1"
                 max="100"
                 required>
        </div>

        <div class="mb-3">
          <label class="block text-sm">Poin Cara Bicara</label>
          <input type="number" name="poin_cara_bicara"
                 value="{{ $myResult->poin_cara_bicara ?? '' }}"
                 class="border rounded w-full px-2 py-1" 
                 placeholder="1 - 100"
                 min="1"
                 max="100"
                 required>
        </div>

        <div class="mb-3">
          <label class="block text-sm">Catatan</label>
          <textarea name="note" class="border rounded w-full px-2 py-1">{{ $myResult->note ?? '' }}</textarea>
        </div>

        <div class="mb-3 flex items-center gap-2">
          <input type="checkbox" name="potencial"
                 {{ ($myResult && $myResult->potencial) ? 'checked' : '' }}
                 class="rounded">
          <label class="text-sm">Potencial</label>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" 
                  onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.add('hidden')"
                  class="px-3 py-1 border rounded">Batal</button>
          <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  @endforeach

  {{-- ✅ Modal Filter Interview --}}
  <div id="filterModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
      {{-- Header --}}
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-lg font-semibold">Filter Data Interview</h3>
        <button type="button"
                onclick="document.getElementById('filterModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      {{-- Content --}}
      <form method="GET" action="{{ route('admin.applicant.seleksi.interview.index') }}" class="space-y-4">
        {{-- Batch --}}
        <div>
          <label class="block text-sm font-medium">Batch</label>
          <select name="batch" class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Semua Batch</option>
            @foreach($batches as $b)
              <option value="{{ $b->id }}" {{ (string)$batchId === (string)$b->id ? 'selected' : '' }}>
                {{ $b->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Posisi --}}
        <div>
          <label class="block text-sm font-medium">Posisi</label>
          <select name="position" class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Semua Posisi</option>
            @foreach($positions as $p)
              <option value="{{ $p->id }}" {{ $positionId == $p->id ? 'selected' : '' }}>
                {{ $p->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Status Seleksi --}}
        <div>
          <label class="block text-sm font-medium">Status Seleksi</label>
          <select name="status" class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Semua Status</option>
            <option value="Interview" {{ request('status') === 'Interview' ? 'selected' : '' }}>Interview</option>
            <option value="Lolos Interview" {{ request('status') === 'Lolos Interview' ? 'selected' : '' }}>Lolos Interview</option>
            <option value="Tidak Lolos Interview" {{ request('status') === 'Tidak Lolos Interview' ? 'selected' : '' }}>Tidak Lolos Interview</option>
          </select>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2">
          <button type="button"
                  onclick="document.getElementById('filterModal').classList.add('hidden')"
                  class="px-3 py-1 border rounded">Batal</button>
          <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Terapkan</button>
        </div>
      </form>
    </div>
  </div>


  {{-- Modal Konfirmasi --}}
  <div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-lg font-semibold">Konfirmasi Aksi</h3>
        <button type="button"
                onclick="document.getElementById('confirmModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>
      <p id="confirmMessage" class="text-gray-700 mb-6">Apakah Anda yakin?</p>
      <div class="flex justify-end gap-2">
        <button type="button"
                onclick="document.getElementById('confirmModal').classList.add('hidden')"
                class="px-4 py-2 border rounded">Batal</button>
        <button type="button" id="confirmYesBtn"
                class="px-4 py-2 bg-blue-600 text-white rounded">Ya, Lanjutkan</button>
      </div>
    </div>
  </div>

  {{-- ✅ Modal Email Interview --}}
  <div id="emailModalInterview" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-5">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h3 class="text-lg font-semibold">Kirim Email Peserta Seleksi Interview</h3>
        <button type="button" onclick="document.getElementById('emailModalInterview').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      {{-- Tabs --}}
      <div class="border-b mb-4 flex">
        <button type="button" data-tab="tabLolosInterview"
                class="tab-btn-int px-4 py-2 border-b-2 border-blue-600 text-blue-600">Lolos</button>
        <button type="button" data-tab="tabTidakLolosInterview"
                class="tab-btn-int px-4 py-2 border-b-2 border-transparent">Tidak Lolos</button>
        <button type="button" data-tab="tabTerpilihInterview"
                class="tab-btn-int px-4 py-2 border-b-2 border-transparent">Terpilih</button>
      </div>

      {{-- Tab Lolos --}}
      <div id="tabLolosInterview" class="tab-content-int">
        <form method="POST" action="{{ route('admin.applicant.seleksi.interview.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateLolosInterview" class="rounded">
            <label for="useTemplateLolosInterview" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectLolosInterview" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="border rounded w-full h-64 overflow-y-auto">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageLolosInterview" type="hidden" name="message">
            <trix-editor input="messageLolosInterview" class="trix-content border rounded w-full h-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModalInterview').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Tidak Lolos --}}
      <div id="tabTidakLolosInterview" class="tab-content-int hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.interview.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="tidak_lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateTidakLolosInterview" class="rounded">
            <label for="useTemplateTidakLolosInterview" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectTidakLolosInterview" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageTidakLolosInterview" type="hidden" name="message">
            <trix-editor input="messageTidakLolosInterview" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModalInterview').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Terpilih --}}
      <div id="tabTerpilihInterview" class="tab-content-int hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.interview.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="selected">
          <input type="hidden" name="ids" id="selectedIdsInterview">

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageSelectedInterview" type="hidden" name="message">
            <trix-editor input="messageSelectedInterview" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModalInterview').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" onclick="setSelectedIdsInterview()" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  {{-- Script untuk Modal Email Interview --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.umd.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.min.css"/>

  <script>
    // Template Lolos Interview
    document.getElementById('useTemplateLolosInterview').addEventListener('change', function() {
      if (this.checked) {
        document.getElementById('subjectLolosInterview').value = "INFORMASI HASIL SELEKSI INTERVIEW - PLN ICON PLUS";
        document.querySelector("trix-editor[input=messageLolosInterview]").editor.loadHTML(
          `Selamat! Anda dinyatakan <strong>LOLOS</strong> pada tahap 'Interview' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
          Tahap selanjutnya akan diinformasikan melalui portal seleksi.<br><br>
          Terima kasih atas partisipasi Anda.`
        );
      } else {
        document.getElementById('subjectLolosInterview').value = "";
        document.querySelector("trix-editor[input=messageLolosInterview]").editor.loadHTML("");
      }
    });

    // Template Tidak Lolos Interview
    document.getElementById('useTemplateTidakLolosInterview').addEventListener('change', function() {
      if (this.checked) {
        document.getElementById('subjectTidakLolosInterview').value = "INFORMASI HASIL SELEKSI INTERVIEW - PLN ICON PLUS";
        document.querySelector("trix-editor[input=messageTidakLolosInterview]").editor.loadHTML(
          `Mohon maaf, Anda dinyatakan <strong>TIDAK LOLOS</strong> pada tahap 'Interview' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
          Terima kasih telah berpartisipasi.<br><br>
          Hormat Kami,<br>
          Recruitment Team`
        );
      } else {
        document.getElementById('subjectTidakLolosInterview').value = "";
        document.querySelector("trix-editor[input=messageTidakLolosInterview]").editor.loadHTML("");
      }
    });

    // Tab switcher
    document.querySelectorAll('.tab-btn-int').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn-int').forEach(b => b.classList.remove('border-blue-600','text-blue-600'));
        this.classList.add('border-blue-600','text-blue-600');
        const target = this.dataset.tab;
        document.querySelectorAll('.tab-content-int').forEach(c => c.classList.add('hidden'));
        document.getElementById(target).classList.remove('hidden');
      });
    });

    // Confirm Modal
    let selectedAction = null;
    function openConfirmModal(action) {
      selectedAction = action;
      const msg = action === 'lolos'
        ? "Apakah Anda yakin ingin meloloskan peserta yang dipilih?"
        : "Apakah Anda yakin ingin menggagalkan peserta yang dipilih?";
      document.getElementById('confirmMessage').innerText = msg;
      document.getElementById('confirmModal').classList.remove('hidden');
    }
    document.getElementById('confirmYesBtn').addEventListener('click', function() {
      if (selectedAction) {
        const form = document.getElementById('bulkActionForm');
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'bulk_action';
        input.value = selectedAction;
        form.appendChild(input);
        form.submit();
      }
    });

    // Check All
    document.getElementById('checkAll').addEventListener('change', function(e){
      document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = e.target.checked);
    });

    // Set selected IDs for "Terpilih"
    function setSelectedIdsInterview() {
      let ids = [];
      document.querySelectorAll('input[name="ids[]"]:checked').forEach(cb => ids.push(cb.value));
      if (ids.length === 0) {
        alert("Silakan pilih peserta terlebih dahulu.");
        event.preventDefault();
        return false;
      }
      document.getElementById('selectedIdsInterview').value = ids.join(',');
    }
  </script>


  {{-- Font Awesome --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</x-app-admin>
