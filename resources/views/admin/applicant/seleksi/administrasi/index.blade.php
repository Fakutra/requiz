<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
    <div class="relative flex items-center gap-2 mb-4">
      <a href="{{ route('admin.applicant.seleksi.index') }}" 
        class="text-gray-600 hover:text-gray-900 flex items-center">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </a>

      <h2 class="text-lg font-semibold leading-none m-0">Seleksi Administrasi</h2>
    </div>


    {{-- Toolbar --}}
    <div class="flex w-full mb-2 items-end gap-2">
      <form method="GET" action="{{ route('admin.applicant.seleksi.administrasi.index') }}" class="flex-1 min-w-[220px]">
        <div class="relative flex items-center">
          <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Nama / Email / Jurusan / Posisi..."
                class="w-full h-10 pl-3 pr-9 border rounded text-sm focus:ring focus:border-blue-500">
          <span class="absolute right-3 text-gray-500">
            <x-search-button/>
          </span>
        </div>
      </form>

      {{-- Tombol Aksi --}}
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
                onclick="document.getElementById('emailModal').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center"
                title="Kirim Email">
          <i class="fas fa-envelope"></i>
        </button>

        {{-- Export --}}
        <a href="{{ route('admin.applicant.seleksi.administrasi.export', request()->query()) }}"
          class="px-3 py-2 border rounded bg-green-600 text-white flex items-center justify-center"
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
    <form id="bulkActionForm" method="POST" action="{{ route('admin.applicant.seleksi.administrasi.bulkMark') }}">
      @csrf
      <div class="overflow-x-auto">
        <table class="table-auto text-sm border w-full">
          <thead class="bg-gray-100 text-gray-800">
            <tr>
              <th class="px-3 py-2">
                <input type="checkbox" id="checkAll">
              </th>

              {{-- Nama Peserta --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'name',
                    'direction' => (request('sort') === 'name' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Nama Peserta
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort','name') === 'name' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              {{-- Email --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'email',
                    'direction' => (request('sort') === 'email' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Email
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'email' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              {{-- Jurusan --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'jurusan',
                    'direction' => (request('sort') === 'jurusan' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Jurusan
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'jurusan' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              {{-- Posisi --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'position_id',
                    'direction' => (request('sort') === 'position_id' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Posisi
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'position_id' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'age',
                    'direction' => (request('sort') === 'age' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Umur
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'age' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>
              <th class="px-3 py-2 text-left whitespace-nowrap font-semibold text-gray-800">Status</th>
              <th class="px-3 py-2 text-left whitespace-nowrap font-semibold text-gray-800">Status Email</th>
              <th class="px-3 py-2 text-left whitespace-nowrap font-semibold text-gray-800">Action</th>
            </tr>
          </thead>

          <tbody>
            @forelse($applicants as $a)
              <tr>
                <td class="px-3 py-2"><input type="checkbox" name="ids[]" value="{{ $a->id }}"></td>

                <td class="px-3 py-2 whitespace-nowrap">{{ $a->user->name ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->user->email ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->jurusan ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->position->name ?? '-' }}</td>

                <td class="px-3 py-2 whitespace-nowrap">
                  {{ $a->age ? $a->age.' tahun' : '-' }}
                </td>

                {{-- Status --}}
                <td class="px-3 py-2 whitespace-nowrap">
                  @php
                      // Mapping status untuk tampilan di Seleksi Administrasi
                      $lolosAdminStatuses = [
                          'Tes Tulis',
                          'Technical Test',
                          'Interview',
                          'Offering',
                          'Menerima Offering',
                          'Tidak Lolos Tes Tulis',
                          'Tidak Lolos Technical Test',
                          'Tidak Lolos Interview',
                          'Menolak Offering',
                      ];

                      if (in_array($a->status, $lolosAdminStatuses)) {
                          $displayStatus = 'Lolos Seleksi Administrasi';
                      } elseif ($a->status === 'Tidak Lolos Seleksi Administrasi') {
                          $displayStatus = 'Tidak Lolos Seleksi Administrasi';
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

                {{-- Email Status --}}
                <td class="px-3 py-2 text-center">
                  @php
                    $log = $a->latestEmailLog;
                    if ($log && $log->stage !== 'Seleksi Administrasi') {
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

                {{-- Action --}}
                <td class="px-3 py-2 text-center">
                  <i class="fas fa-eye text-blue-600 cursor-pointer hover:text-blue-800"
                    title="Lihat Detail"
                    onclick="document.getElementById('detailModal-{{ $a->id }}').classList.remove('hidden')"></i>
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

  {{-- ✅ Modal Detail per applicant --}}
  @foreach($applicants as $a)
  <div id="detailModal-{{ $a->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl p-6 overflow-y-auto max-h-[90vh]">
      
      {{-- Header --}}
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-xl font-semibold text-gray-800">Detail Applicant</h3>
        <button type="button"
                onclick="document.getElementById('detailModal-{{ $a->id }}').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      {{-- Content --}}
      <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div>
          <p class="text-gray-500 font-medium">Nama</p>
          <p class="text-gray-900">{{ $a->user->name ?? '-' }}</p>
        </div>
        <div>
          <p class="text-gray-500 font-medium">Email</p>
          <p class="text-gray-900">{{ $a->user->email ?? '-' }}</p>
        </div>

        <div>
          <p class="text-gray-500 font-medium">NIK</p>
          <p class="text-gray-900">{{ $a->user->profile->identity_num ?? '-' }}</p>
        </div>
        <div>
          <p class="text-gray-500 font-medium">No. Telepon</p>
          <p class="text-gray-900">{{ $a->user->profile->phone_number ?? '-' }}</p>
        </div>

        <div>
          <p class="text-gray-500 font-medium">Tempat Lahir</p>
          <p class="text-gray-900">{{ $a->user->profile->birthplace ?? '-' }}</p>
        </div>
        <div>
          <p class="text-gray-500 font-medium">Tanggal Lahir</p>
          <p class="text-gray-900">
            {{ optional($a->user->profile->birthdate)->translatedFormat('j F Y') ?? '-' }}
          </p>
        </div>

        <div class="col-span-2">
          <p class="text-gray-500 font-medium">Alamat</p>
          <p class="text-gray-900">{{ $a->user->profile->address ?? '-' }}</p>
        </div>

        <div>
          <p class="text-gray-500 font-medium">Pendidikan</p>
          <p class="text-gray-900">{{ $a->pendidikan }}</p>
        </div>
        <div>
          <p class="text-gray-500 font-medium">Tahun Lulus</p>
          <p class="text-gray-900">{{ $a->thn_lulus ?? '-' }}</p>
        </div>

        <div>
          <p class="text-gray-500 font-medium">Universitas</p>
          <p class="text-gray-900">{{ $a->universitas }}</p>
        </div>
        <div>
          <p class="text-gray-500 font-medium">Jurusan</p>
          <p class="text-gray-900">{{ $a->jurusan }}</p>
        </div>

        <div>
          <p class="text-gray-500 font-medium">Skills</p>
          <p class="text-gray-900">{{ $a->skills ?? '-' }}</p>
        </div>
        <div>
          <p class="text-gray-500 font-medium">Ekpektasi Gaji</p>
          <p class="text-gray-900">{{ $a->ekspektasi_gaji_formatted ?? '-' }}</p>
        </div>

        <div>
          <p class="text-gray-500 font-medium">Batch</p>
          <p class="text-gray-900">{{ $a->batch->name ?? $a->batch_id }}</p>
        </div>
        <div>
          <p class="text-gray-500 font-medium">Posisi</p>
          <p class="text-gray-900">{{ $a->position->name ?? $a->position_id }}</p>
        </div>

        <div>
          <p class="text-gray-500 font-medium">Status Seleksi</p>
          @php
            $statusColor = str_contains($a->status, 'Tidak') ? 'bg-red-100 text-red-700' 
                          : (str_contains($a->status, 'Lolos') || $a->status === 'Menerima Offering' ? 'bg-green-100 text-green-700' 
                          : 'bg-yellow-100 text-yellow-700');
          @endphp
          <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColor }}">
            {{ $a->status }}
          </span>
        </div>
        {{-- Dokumen (CV + Dokumen Tambahan) satu baris --}}
        <div class="col-span-2">
          <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
            <span class="text-gray-500 font-medium">Dokumen:</span>

            {{-- CV --}}
            <div class="flex items-center gap-1">
              <span class="text-gray-500">CV</span>
              @if($a->cv_document)
                <a href="{{ asset('storage/'.$a->cv_document) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
              @else
                <span class="text-gray-400">Belum upload</span>
              @endif
            </div>

            <span class="text-gray-300">|</span>

            {{-- Dokumen Tambahan --}}
            <div class="flex items-center gap-1">
              <span class="text-gray-500">Dokumen Tambahan</span>
              @if($a->doc_tambahan)
                <a href="{{ asset('storage/'.$a->doc_tambahan) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
              @else
                <span class="text-gray-400">Belum upload</span>
              @endif
            </div>
          </div>
        </div>
        <div>
          <p class="text-gray-500 font-medium">Dokumen Tambahan</p>
          @if($a->additional_doc)
            <a href="{{ asset('storage/'.$a->additional_doc) }}" target="_blank"
              class="text-blue-600 hover:underline">
              Lihat Dokumen Tambahan
            </a>
          @else
            <span class="text-gray-400">Belum upload</span>
          @endif
        </div>
      </div>

      {{-- Footer --}}
      <div class="mt-6 text-right">
        <button type="button"
                onclick="document.getElementById('detailModal-{{ $a->id }}').classList.add('hidden')"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
          Tutup
        </button>
      </div>
    </div>
  </div>
  @endforeach

  {{-- ✅ Modal Filter --}}
  <div id="filterModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
      {{-- Header --}}
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-lg font-semibold">Filter Data</h3>
        <button type="button" 
                onclick="document.getElementById('filterModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      {{-- Content --}}
      <form method="GET" action="{{ route('admin.applicant.seleksi.administrasi.index') }}" class="space-y-4">
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
            <option value="Seleksi Administrasi" {{ request('status') === 'Seleksi Administrasi' ? 'selected' : '' }}>
              Seleksi Administrasi
            </option>
            <option value="Tes Tulis" {{ request('status') === 'Tes Tulis' ? 'selected' : '' }}>
              Lolos Seleksi Administrasi
            </option>
            <option value="Tidak Lolos Seleksi Administrasi" {{ request('status') === 'Tidak Lolos Seleksi Administrasi' ? 'selected' : '' }}>
              Tidak Lolos Seleksi Administrasi
            </option>
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

  {{-- ✅ Modal Konfirmasi --}}
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
        <h3 class="text-lg font-semibold">Kirim Email Peserta Seleksi Administrasi</h3>
        <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      {{-- Tabs --}}
      <div class="border-b mb-4 flex">
        <button type="button" data-tab="tabLolos"
                class="tab-btn px-4 py-2 border-b-2 border-blue-600 text-blue-600">Lolos</button>
        <button type="button" data-tab="tabTidakLolos"
                class="tab-btn px-4 py-2 border-b-2 border-transparent">Tidak Lolos</button>
        <button type="button" data-tab="tabTerpilih"
                class="tab-btn px-4 py-2 border-b-2 border-transparent">Terpilih</button>
      </div>

      {{-- Tab Lolos --}}
      <div id="tabLolos" class="tab-content">
        <form method="POST" action="{{ route('admin.applicant.seleksi.administrasi.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateLolos" class="rounded">
            <label for="useTemplateLolos" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectLolos" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="border rounded w-full h-64 overflow-y-auto">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageLolos" type="hidden" name="message">
            <trix-editor input="messageLolos" class="trix-content border rounded w-full h-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Tidak Lolos --}}
      <div id="tabTidakLolos" class="tab-content hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.administrasi.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="tidak_lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateTidakLolos" class="rounded">
            <label for="useTemplateTidakLolos" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectTidakLolos" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageTidakLolos" type="hidden" name="message">
            <trix-editor input="messageTidakLolos" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Terpilih --}}
      <div id="tabTerpilih" class="tab-content hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.administrasi.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="selected">
          <input type="hidden" name="ids" id="selectedIds"> {{-- akan diisi lewat JS --}}

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
            <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" onclick="setSelectedIds()" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

    </div>
  </div>

  {{-- Scripts --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.umd.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.min.css"/>

  <script>
    // Template Lolos
    document.getElementById('useTemplateLolos').addEventListener('change', function() {
      if (this.checked) {
        document.getElementById('subjectLolos').value = "INFORMASI HASIL SELEKSI TAD/OUTSOURCING - PLN ICON PLUS";
        document.querySelector("trix-editor[input=messageLolos]").editor.loadHTML(
          `Selamat! Anda dinyatakan <strong>LOLOS</strong> pada tahap 'Seleksi Administrasi' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
          Untuk tahap selanjutnya, mohon untuk mempersiapkan diri mengikuti Tes Tulis.<br>
          Jadwal dan detail pelaksanaan Tes Tulis akan disampaikan pada laman Proses Seleksi.<br>
          Silahkan dilakukan pengecekan secara berkala pada laman Proses Seleksi.<br><br>
          Demikian Kami sampaikan,<br>
          Terima kasih atas partisipasinya dan semoga sukses.`
        );
      } else {
        document.getElementById('subjectLolos').value = "";
        document.querySelector("trix-editor[input=messageLolos]").editor.loadHTML("");
      }
    });

    // Template Tidak Lolos
    document.getElementById('useTemplateTidakLolos').addEventListener('change', function() {
      if (this.checked) {
        document.getElementById('subjectTidakLolos').value = "INFORMASI HASIL SELEKSI TAD/OUTSOURCING - PLN ICON PLUS";
        document.querySelector("trix-editor[input=messageTidakLolos]").editor.loadHTML(
          `Mohon maaf, Anda dinyatakan <strong>TIDAK LOLOS</strong> pada tahap 'Seleksi Administrasi' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
          Kami berterima kasih atas partisipasi Anda dalam proses seleksi ini.<br>
          Semoga sukses di kesempatan berikutnya.<br><br>
          Hormat kami,<br>
          Recruitment Team`
        );
      } else {
        document.getElementById('subjectTidakLolos').value = "";
        document.querySelector("trix-editor[input=messageTidakLolos]").editor.loadHTML("");
      }
    });

    // Set selected IDs for "Terpilih" tab
    function setSelectedIds() {
      let ids = [];
      document.querySelectorAll('input[name="ids[]"]:checked').forEach(cb => ids.push(cb.value));
      if (ids.length === 0) {
        alert("Silakan pilih peserta terlebih dahulu.");
        event.preventDefault();
        return false;
      }
      document.getElementById('selectedIds').value = ids.join(',');
    }


    // Check All
    document.getElementById('checkAll').addEventListener('change', function(e){
      document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = e.target.checked);
    });

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
        // buat input hidden bulk_action
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'bulk_action';
        input.value = selectedAction;
        form.appendChild(input);
        form.submit();
      }
    });
  </script>

  {{-- Font Awesome --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</x-app-admin>