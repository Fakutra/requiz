<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
    <div class="relative flex items-center gap-2 mb-4">
      <a href="{{ route('admin.applicant.seleksi.index') }}" 
        class="text-gray-600 hover:text-gray-900 flex items-center">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </a>

      <h2 class="text-lg font-semibold leading-none m-0">Seleksi Technical Test</h2>
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
                class="px-3 py-2 border rounded bg-gray-600 text-white"
                title="Filter">
          <i class="fas fa-filter"></i>
        </button>

        {{-- Email --}}
        <button type="button"
                onclick="document.getElementById('emailModal').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-yellow-500 hover:bg-yellow-600 text-white"
                title="Kirim Email">
          <i class="fas fa-envelope"></i>
        </button>

        {{-- Export --}}
        <a href="{{ route('admin.applicant.seleksi.technical_test.export', request()->query()) }}"
           class="px-3 py-2 border rounded bg-green-600 text-white"
           title="Export">
          <i class="fas fa-file-export"></i>
        </a>

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
    <form id="bulkActionForm" method="POST" action="{{ route('admin.applicant.seleksi.technical_test.bulkMark') }}">
      @csrf
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-3 py-2"><input type="checkbox" id="checkAll"></th>

              {{-- Nama Peserta --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'name',
                    'direction' => (request('sort') === 'name' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 hover:text-gray-900 no-underline">
                  Nama Peserta
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort','name') === 'name' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </a>
              </th>

              {{-- Email --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'email',
                    'direction' => (request('sort') === 'email' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 hover:text-gray-900 no-underline">
                  Email
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'email' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </a>
              </th>

              {{-- Jawaban --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'pdf',
                    'direction' => (request('sort') === 'pdf' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 hover:text-gray-900 no-underline">
                  Jawaban (PDF)
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'pdf' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </a>
              </th>

              {{-- Keterangan --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'keterangan',
                    'direction' => (request('sort') === 'keterangan' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 hover:text-gray-900 no-underline">
                  Keterangan
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'keterangan' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </a>
              </th>

              {{-- Nilai --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'score',
                    'direction' => (request('sort') === 'score' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" class="flex items-center gap-1 font-semibold text-gray-800 hover:text-gray-900 no-underline">
                  Nilai
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'score' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </a>
              </th>

              <th class="px-3 py-2 text-left whitespace-nowrap">Status</th>
              <th class="px-3 py-2 text-left whitespace-nowrap">Status Email</th>
              <th class="px-3 py-2 text-left whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($applicants as $a)
              @php
                /** @var \App\Models\TechnicalTestAnswer|null $ans */
                $ans = $latestAnswers[$a->id] ?? null;
              @endphp
              <tr>
                <td class="px-3 py-2">
                  @php
                    $finalTechStatuses = [
                      'Interview',
                      'Offering',
                      'Menerima Offering',
                      'Tidak Lolos Technical Test',
                      'Tidak Lolos Interview',
                      'Menolak Offering',
                    ];

                    $emailLocked = $a->latestEmailLog
                        && $a->latestEmailLog->stage === 'Technical Test'
                        && $a->latestEmailLog->success;

                    $isLocked = in_array($a->status, $finalTechStatuses, true) && $emailLocked;
                  @endphp

                  <input type="checkbox"
                        name="ids[]"
                        value="{{ $a->id }}"
                        {{ $isLocked ? 'disabled' : '' }}>
                </td>

                <td class="px-3 py-2 whitespace-nowrap">{{ $a->name }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->email }}</td>

                {{-- Jawaban --}}
                <td class="px-3 py-2 whitespace-nowrap">
                  @if($ans && $ans->answer_url)
                    <div class="flex items-center gap-3">
                      <a href="{{ $ans->answer_url }}" target="_blank"
                        class="text-red-600 hover:text-red-800"
                        title="Lihat Jawaban (PDF)">
                        <i class="fas fa-file-pdf fa-lg"></i>
                      </a>

                      @if($ans->screen_record_url)
                        <a href="{{ $ans->screen_record_url }}" target="_blank"
                          class="text-purple-600 hover:text-purple-800"
                          title="Lihat Rekaman Layar">
                          <i class="fas fa-video fa-lg"></i>
                        </a>
                      @endif
                    </div>
                  @else
                    <span class="text-gray-400">Belum upload</span>
                  @endif
                </td>

                {{-- Keterangan --}}
                <td class="px-3 py-2 whitespace-nowrap">{{ $ans?->keterangan ?? '-' }}</td>

                {{-- Nilai --}}
                <td class="px-3 py-2 whitespace-nowrap">{{ is_null($ans?->score) ? '-' : $ans->score }}</td>

                {{-- Status peserta --}}
                <td class="px-3 py-2 whitespace-nowrap">
                  @php
                    $displayStatus = $a->status;
                    $lolosTechStatuses = [
                      'Interview','Offering','Menerima Offering','Tidak Lolos Interview','Menolak Offering',
                    ];
                    if (in_array($a->status, $lolosTechStatuses, true)) {
                      $displayStatus = 'Lolos Technical Test';
                    } elseif ($a->status === 'Tidak Lolos Technical Test') {
                      $displayStatus = 'Tidak Lolos Technical Test';
                    }

                    $isLolos = \Illuminate\Support\Str::startsWith($displayStatus, 'Lolos');
                    $isTidak = \Illuminate\Support\Str::startsWith($displayStatus, 'Tidak Lolos');
                    $badgeClass = $isLolos ? 'bg-[#69FFA0] text-[#2C6C44]'
                                  : ($isTidak ? 'bg-[#FFDDDD] text-[#FF2525]' : 'bg-yellow-100 text-yellow-700');
                  @endphp
                  <span class="px-2 py-1 text-xs rounded {{ $badgeClass }}">
                    {{ $displayStatus }}
                  </span>
                </td>

                {{-- Status email (stage Technical Test) --}}
                <td class="px-3 py-2 text-center">
                  @php
                    $log = $a->latestEmailLog;
                    if ($log && $log->stage !== 'Technical Test') $log = null;
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

                {{-- Action --}}
                <td class="px-3 py-2">
                  <div class="flex items-center justify-center gap-4">
                    {{-- Detail Applicant --}}
                    <i class="fas fa-eye text-blue-600 cursor-pointer hover:text-blue-800"
                      title="Lihat Detail"
                      onclick="document.getElementById('detailModal-{{ $a->id }}').classList.remove('hidden')"></i>

                    {{-- Penilaian (jika ada jawaban) --}}
                    <i class="fas fa-pencil-alt {{ $ans ? 'text-yellow-600 hover:text-yellow-800 cursor-pointer' : 'text-gray-400' }}"
                      title="{{ $ans ? 'Nilai Technical Test' : 'Belum ada jawaban' }}"
                      @if($ans)
                        onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.remove('hidden')"
                      @endif
                    ></i>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-gray-500 py-5">Tidak ada data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </form>

    <div class="mt-3">{{ $applicants->links() }}</div>
  </div>

  {{-- Modal Detail Applicant --}}
  @foreach($applicants as $a)
    <div id="detailModal-{{ $a->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
          <h3 class="text-xl font-semibold text-gray-800">Detail Applicant</h3>
          <button type="button"
                  onclick="document.getElementById('detailModal-{{ $a->id }}').classList.add('hidden')"
                  class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
          <div><p class="text-gray-500 font-medium">Nama</p><p class="text-gray-900">{{ $a->name }}</p></div>
          <div><p class="text-gray-500 font-medium">Email</p><p class="text-gray-900">{{ $a->email }}</p></div>
          <div><p class="text-gray-500 font-medium">Jurusan</p><p class="text-gray-900">{{ $a->jurusan }}</p></div>
          <div><p class="text-gray-500 font-medium">Posisi</p><p class="text-gray-900">{{ $a->position->name ?? '-' }}</p></div>
          <div><p class="text-gray-500 font-medium">Batch</p><p class="text-gray-900">{{ $a->batch->name ?? '-' }}</p></div>
          <div><p class="text-gray-500 font-medium">Umur</p><p class="text-gray-900">{{ $a->age ?? '-' }}</p></div>
          <div>
            <p class="text-gray-500 font-medium">Status Seleksi</p>
            @php
              $statusColor = str_contains($a->status, 'Tidak') ? 'bg-red-100 text-red-700' 
                            : (str_contains($a->status, 'Interview') || str_contains($a->status, 'Offering') || $a->status === 'Menerima Offering'
                               ? 'bg-green-100 text-green-700'
                               : 'bg-yellow-100 text-yellow-700');
            @endphp
            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColor }}">
              {{ $a->status }}
            </span>
          </div>
          <div>
            <p class="text-gray-500 font-medium">CV</p>
            @if($a->cv_document)
              <a href="{{ asset('storage/'.$a->cv_document) }}" target="_blank" class="text-blue-600 hover:underline">
                Lihat CV
              </a>
            @else
              <span class="text-gray-400">Belum upload</span>
            @endif
          </div>
        </div>

        <div class="mt-6 text-right">
          <button type="button"
                  onclick="document.getElementById('detailModal-{{ $a->id }}').classList.add('hidden')"
                  class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Tutup</button>
        </div>
      </div>
    </div>
  @endforeach

  {{-- Modal Penilaian Technical Test --}}
  @foreach($applicants as $a)
    @php $ans = $latestAnswers[$a->id] ?? null; @endphp
    <div id="scoreModal-{{ $a->id }}" 
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-5xl p-6 relative">

        {{-- Header --}}
        <div class="flex justify-between items-center border-b pb-2 mb-4">
          <h3 class="text-lg font-semibold">
            Penilaian Technical Test — {{ $a->name }}
          </h3>
          <button type="button"
                  onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.add('hidden')"
                  class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
        </div>

        @if(!$ans)
          <p class="text-gray-600 text-sm">Belum ada jawaban yang diupload.</p>
          <div class="mt-4 text-right">
            <button type="button" 
                    class="px-3 py-1 border rounded"
                    onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.add('hidden')">
              Tutup
            </button>
          </div>
        @else
          {{-- Layout Dua Kolom --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Kolom Kiri: PDF Preview --}}
            <div class="border rounded overflow-hidden">
              @if($ans->answer_path)
                <iframe src="{{ asset('storage/'.$ans->answer_path) }}" 
                        class="w-full h-[500px]" frameborder="0"></iframe>
              @else
                <div class="p-4 bg-gray-50 text-sm text-gray-600 h-[500px] flex items-center justify-center">
                  Tidak ada file PDF yang bisa ditampilkan.
                </div>
              @endif

              {{-- Optional: Link Rekaman --}}
              @if($ans->screen_record_url)
                <div class="p-2 text-center border-t bg-gray-50">
                  <a href="{{ $ans->screen_record_url }}" target="_blank" 
                    class="text-blue-600 text-sm hover:underline">
                    🔗 Lihat rekaman screen test
                  </a>
                </div>
              @endif
            </div>

            {{-- Kolom Kanan: Form Penilaian --}}
            <div>
              <form method="POST" 
                    action="{{ route('admin.applicant.seleksi.technical_test.updateScore', $ans->id) }}" 
                    class="flex flex-col h-full">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                  <label class="block text-sm font-medium mb-1">Nilai</label>
                  <input type="number" name="score" 
                        value="{{ $ans->score ?? '' }}" 
                        min="0" max="100" step="0.01"
                        class="border rounded w-full px-3 py-2 focus:ring focus:ring-blue-200"
                        placeholder="0-100">
                </div>

                <div class="mb-4 flex-grow">
                  <label class="block text-sm font-medium mb-1">Keterangan</label>
                  <textarea name="keterangan" rows="8"
                            class="border rounded w-full px-3 py-2 focus:ring focus:ring-blue-200">{{ $ans->keterangan ?? '' }}</textarea>
                </div>

                <div class="flex justify-end gap-2 mt-auto border-t pt-3">
                  <button type="button" 
                          onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.add('hidden')"
                          class="px-3 py-1 border rounded hover:bg-gray-50">Batal</button>
                  <button type="submit" 
                          class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                </div>
              </form>
            </div>

          </div>
        @endif
      </div>
    </div>
  @endforeach

  {{-- Modal Filter --}}
  <div id="filterModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-lg font-semibold">Filter Data</h3>
        <button type="button"
                onclick="document.getElementById('filterModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      <form method="GET" action="{{ route('admin.applicant.seleksi.technical_test.index') }}" class="space-y-4">
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

        <div>
          <label class="block text-sm font-medium">Posisi</label>
          <select name="position" class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Semua Posisi</option>
            @foreach($positions as $p)
              <option value="{{ $p->id }}" {{ (string)$positionId === (string)$p->id ? 'selected' : '' }}>
                {{ $p->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium">Status</label>
          <select name="status" class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Semua Status</option>
            <option value="Technical Test" {{ request('status') === 'Technical Test' ? 'selected' : '' }}>Technical Test</option>
            <option value="Interview" {{ request('status') === 'Interview' ? 'selected' : '' }}>Lolos Technical Test</option>
            <option value="Tidak Lolos Technical Test" {{ request('status') === 'Tidak Lolos Technical Test' ? 'selected' : '' }}>Tidak Lolos Technical Test</option>
          </select>
        </div>

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

  {{-- Modal Email --}}
  <div id="emailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-5">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h3 class="text-lg font-semibold">Kirim Email Peserta Technical Test</h3>
        <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      <div class="border-b mb-4 flex">
        <button type="button" data-tab="tabLolos" class="tab-btn px-4 py-2 border-b-2 border-blue-600 text-blue-600">Lolos</button>
        <button type="button" data-tab="tabTidakLolos" class="tab-btn px-4 py-2 border-b-2 border-transparent">Tidak Lolos</button>
        <button type="button" data-tab="tabTerpilih" class="tab-btn px-4 py-2 border-b-2 border-transparent">Terpilih</button>
      </div>

      {{-- Tab Lolos --}}
      <div id="tabLolos" class="tab-content">
        <form method="POST" action="{{ route('admin.applicant.seleksi.technical_test.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateLolosTech" class="rounded">
            <label for="useTemplateLolosTech" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectLolosTech" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="border rounded w-full h-64 overflow-y-auto">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageLolosTech" type="hidden" name="message">
            <trix-editor input="messageLolosTech" class="trix-content border rounded w-full h-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')" class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Tidak Lolos --}}
      <div id="tabTidakLolos" class="tab-content hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.technical_test.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="tidak_lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateTidakLolosTech" class="rounded">
            <label for="useTemplateTidakLolosTech" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectTidakLolosTech" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageTidakLolosTech" type="hidden" name="message">
            <trix-editor input="messageTidakLolosTech" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')" class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Terpilih --}}
      <div id="tabTerpilih" class="tab-content hidden">
        <form method="POST"
              action="{{ route('admin.applicant.seleksi.technical_test.sendEmail') }}"
              enctype="multipart/form-data"
              onsubmit="return setSelectedIds();">
          @csrf
          <input type="hidden" name="type" value="selected">
          <input type="hidden" name="ids" id="selectedIds">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageSelected" type="hidden" name="message">
            <trix-editor input="messageSelected" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button"
                    onclick="document.getElementById('emailModal').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Scripts --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.umd.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <script>
    // Check All
    document.getElementById('checkAll').addEventListener('change', function(e){
      document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
        if (!cb.disabled) cb.checked = e.target.checked;
      });
    });

    // Selected IDs (tab Terpilih)
    function setSelectedIds() {
      let ids = [];
      document.querySelectorAll('input[name="ids[]"]:checked').forEach(cb => ids.push(cb.value));
      if (ids.length === 0) {
        alert("Silakan pilih peserta terlebih dahulu.");
        return false;
      }
      document.getElementById('selectedIds').value = ids.join(',');
      return true;
    }

    // Tab switcher
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('border-blue-600','text-blue-600'));
        this.classList.add('border-blue-600','text-blue-600');
        const target = this.dataset.tab;
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById(target).classList.remove('hidden');
      });
    });

    // Confirm Modal
    let selectedAction = null;

    function getSelectedIds() {
      return document.querySelectorAll('input[name="ids[]"]:checked');
    }

    function openConfirmModal(action) {
      const selected = getSelectedIds();

      // 🚫 TIDAK ADA YANG DICENTANG
      if (selected.length === 0) {
        alert('Pilih peserta terlebih dahulu.');
        return;
      }

      // ⛔ cegah kalau ada checkbox disabled ikut kepilih (defensive)
      if ([...selected].some(cb => cb.disabled)) {
        alert('Ada peserta yang sudah final dan tidak bisa diproses ulang.');
        return;
      }

      // ✅ lanjut normal
      selectedAction = action;

      const msg = action === 'lolos'
        ? "Apakah Anda yakin ingin meloloskan peserta yang dipilih?"
        : "Apakah Anda yakin ingin menggagalkan peserta yang dipilih?";

      document.getElementById('confirmMessage').innerText = msg;
      document.getElementById('confirmModal').classList.remove('hidden');
    }

    document.getElementById('confirmYesBtn')?.addEventListener('click', function () {
      const selected = getSelectedIds();

      // 🔐 pengaman tambahan
      if (selected.length === 0) {
        alert('Tidak ada peserta yang dipilih.');
        return;
      }

      const form = document.getElementById('bulkActionForm');

      let input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'bulk_action';
      input.value = selectedAction;

      form.appendChild(input);
      form.submit();
    });

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

    // Template email (LOLOS)
    const useTemplateLolosTech = document.getElementById('useTemplateLolosTech');
    if (useTemplateLolosTech) {
      useTemplateLolosTech.addEventListener('change', function() {
        if (this.checked) {
          document.getElementById('subjectLolosTech').value = "INFORMASI HASIL TECHNICAL TEST - PLN ICON PLUS";
          document.querySelector("trix-editor[input=messageLolosTech]").editor.loadHTML(
            `Selamat! Anda dinyatakan <strong>LOLOS</strong> pada tahap 'Technical Test' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
            Untuk tahap selanjutnya, mohon untuk mempersiapkan diri mengikuti <strong>Interview</strong>.<br>
            Jadwal Interview akan disampaikan pada laman Proses Seleksi.<br>
            Silakan cek secara berkala.<br><br>
            Terima kasih dan semoga sukses.`
          );
        } else {
          document.getElementById('subjectLolosTech').value = "";
          document.querySelector("trix-editor[input=messageLolosTech]").editor.loadHTML("");
        }
      });
    }

    // Template email (TIDAK LOLOS)
    const useTemplateTidakLolosTech = document.getElementById('useTemplateTidakLolosTech');
    if (useTemplateTidakLolosTech) {
      useTemplateTidakLolosTech.addEventListener('change', function() {
        if (this.checked) {
          document.getElementById('subjectTidakLolosTech').value = "INFORMASI HASIL TECHNICAL TEST - PLN ICON PLUS";
          document.querySelector("trix-editor[input=messageTidakLolosTech]").editor.loadHTML(
            `Mohon maaf, Anda dinyatakan <strong>TIDAK LOLOS</strong> pada tahap 'Technical Test' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
            Kami berterima kasih atas partisipasi Anda dalam proses seleksi ini.<br>
            Semoga sukses di kesempatan berikutnya.<br><br>
            Hormat kami,<br>
            Recruitment Team`
          );
        } else {
          document.getElementById('subjectTidakLolosTech').value = "";
          document.querySelector("trix-editor[input=messageTidakLolosTech]").editor.loadHTML("");
        }
      });
    }
  </script>
</x-app-admin>
