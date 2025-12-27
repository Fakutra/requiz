<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
    {{-- Header --}}
    <div class="relative flex items-center gap-2 mb-4">
        <a href="{{ route('admin.applicant.seleksi.index') }}" 
          class="text-gray-600 hover:text-gray-900 flex items-center">
          <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
        </a>

        <h2 class="text-lg font-semibold leading-none m-0">Offering</h2>
      </div>

    {{-- Toolbar --}}
    <div class="flex justify-between mb-3">
      <form method="GET" action="{{ route('admin.applicant.seleksi.offering.index') }}" class="flex gap-2 flex-1">
        <input type="hidden" name="batch" value="{{ request('batch') }}">
        <input type="hidden" name="position" value="{{ request('position') }}">
        <input type="hidden" name="status" value="{{ request('status') }}">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama, email, posisi, jabatan, bidang, sub bidang, atau seksi..."
               class="border rounded px-3 py-2 flex-1 text-sm"
               title="Cari berdasarkan: Nama, Email, Posisi, Jabatan, Bidang, Sub Bidang, atau Seksi">
      </form>

      <div class="flex gap-2">
        {{-- 🔄 Sinkronisasi Expired --}}
        <form method="POST" action="{{ route('admin.applicant.seleksi.offering.sync-expired') }}"
              onsubmit="return confirm('Sinkronkan semua offering yang sudah melewati deadline?')">
          @csrf
          <button
            type="submit"
            class="px-3 py-2 border rounded bg-amber-500 hover:bg-amber-600 text-white"
            title="Sinkronkan Offering Expired">
            <i class="fas fa-sync-alt"></i>
          </button>
        </form>

        {{-- Filter --}}
        <button type="button"
                onclick="document.getElementById('filterModalOffering').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-gray-600 text-white"
                title="Filter">
          <i class="fas fa-filter"></i>
        </button>

        {{-- Email --}}
        <button type="button"
                onclick="document.getElementById('emailModalOffering').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-yellow-500 hover:bg-yellow-600 text-white"
                title="Kirim Email">
          <i class="fas fa-envelope"></i>
        </button>

        {{-- Export --}}
        <a href="{{ route('admin.applicant.seleksi.offering.export', request()->query()) }}"
           class="px-3 py-2 border rounded bg-green-600 text-white"
           title="Export">
          <i class="fas fa-file-export"></i>
        </a>

        {{-- Accepted / Decline --}}
        <button type="button"
                onclick="openConfirmModal('accepted')"
                class="px-3 py-2 rounded bg-blue-600 text-white">
          Accepted
        </button>

        <button type="button"
                onclick="openConfirmModal('decline')"
                class="px-3 py-2 rounded bg-red-600 text-white">
          Decline
        </button>
      </div>
    </div>

    {{-- Table --}}
    <form id="bulkActionForm" method="POST" action="{{ route('admin.applicant.seleksi.offering.bulkMark') }}">
      @csrf
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-3 py-2"><input type="checkbox" id="checkAll"></th>
              {{-- Nama --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'name',
                    'direction' => (request('sort') === 'name' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Nama
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

              {{-- Posisi --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'posisi',
                    'direction' => (request('sort') === 'posisi' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Posisi yang Dilamar
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'posisi' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              {{-- Jabatan --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'jabatan',
                    'direction' => (request('sort') === 'jabatan' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Jabatan
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'jabatan' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              {{-- Bidang --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'bidang',
                    'direction' => (request('sort') === 'bidang' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Bidang
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'bidang' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              {{-- Sub Bidang --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'subbidang',
                    'direction' => (request('sort') === 'subbidang' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Sub Bidang
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'subbidang' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              {{-- Seksi --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'seksi',
                    'direction' => (request('sort') === 'seksi' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Seksi
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'seksi' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              {{-- Status --}}
              <th class="px-3 py-2 text-left whitespace-nowrap">
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'status',
                    'direction' => (request('sort') === 'status' && request('direction') === 'asc') ? 'desc' : 'asc'
                ]) }}" 
                  class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                  Status
                  <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'status' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}" 
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </a>
              </th>

              <th class="px-3 py-2 text-left whitespace-nowrap">
                  By
              </th>

              <th class="px-3 py-2 text-left whitespace-nowrap">
                Deadline Offering
              </th>

              <th class="px-3 py-2 text-left">Status Email</th>
              <th class="px-3 py-2 text-left">Action</th>
            </tr>
          </thead>

          <tbody>
            @forelse($applicants as $a)
              <tr>
                <td class="px-3 py-2">
                  @php
                    $offering = $a->offering;

                    // FINAL hanya jika TIDAK ingin diubah lagi sama sekali
                    // (misalnya sudah masuk tahap onboarding, kontrak ditandatangani, dll)
                    $isFinal = false;
                  @endphp

                  <input type="checkbox"
                        name="ids[]"
                        value="{{ $a->id }}"
                        data-status="{{ $a->status }}"
                        {{ $isFinal ? 'disabled' : '' }}>
                </td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->name }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->email }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ $a->position->name ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ optional(optional($a->offering)->job)->name ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ optional(optional($a->offering)->field)->name ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ optional(optional($a->offering)->subfield)->name ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">{{ optional(optional($a->offering)->seksi)->name ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    @php
                        $isExpiredDecline =
                            $a->status === 'Menolak Offering'
                            && $a->offering
                            && $a->offering->decision === 'declined'
                            && $a->offering->decision_reason === 'expired';
                    @endphp

                    @if ($isExpiredDecline)
                        <span class="text-gray-900">
                            Menolak Offering
                            <span class="text-red-600 font-medium">(Expired)</span>
                        </span>
                    @else
                        {{ $a->status ?? '-' }}
                    @endif
                </td>

                <td class="px-3 py-2 whitespace-nowrap text-sm">
                    @php
                        $by = $a->offering?->decision_by;
                    @endphp

                    @if (!$by)
                        <span class="text-gray-400">-</span>
                    @elseif ($by === 'system')
                        <span class="text-gray-500 italic">System</span>
                    @elseif ($by === 'user')
                        <span class="text-sky-700 font-medium">Pelamar</span>
                    @elseif (in_array($by, ['admin','vendor']))
                        <span class="text-indigo-700 font-medium text-capitalize">
                            {{ ucfirst($by) }}
                        </span>
                    @else
                        <span class="text-gray-700">{{ ucfirst($by) }}</span>
                    @endif
                </td>

                {{-- Deadline Offering --}}
                <td class="px-3 py-2 whitespace-nowrap">
                  @if($a->offering && $a->offering->response_deadline)
                    <span class="text-sm text-gray-800">
                      {{ $a->offering->response_deadline->format('d M Y H:i') }}
                    </span>
                  @else
                    <span class="text-gray-400 text-sm">-</span>
                  @endif
                </td>

                {{-- Status Email --}}
                <td class="px-3 py-2 text-center">
                  @php
                    $log = $a->latestEmailLog;
                    if ($log && $log->stage !== 'Offering') $log = null;
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
                  <i class="fas fa-pen text-blue-600 cursor-pointer hover:text-blue-800"
                     onclick="
                      const modal = document.getElementById('offeringModal-{{ $a->id }}');
                      modal.classList.remove('hidden');
                      modal.querySelector('[x-data]').__x.$data.reset();
                    ">
                  </i>
                </td>
              </tr>  
            @empty
              <tr><td colspan="11" class="text-center text-gray-500 py-5">Data masih kosong</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </form>

    {{-- Semua Modal Offering dipindah ke luar form bulk --}}
    @foreach($applicants as $a)
      {{-- Modal offering untuk setiap applicant --}}
      <div id="offeringModal-{{ $a->id }}"
          class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

          {{-- PERBAIKAN BESAR: Gunakan x-data dengan init yang benar --}}
          <div x-data="offeringForm()"
              x-init="
                  initData(
                      {{ $a->offering->field_id ?? 'null' }},
                      {{ $a->offering->sub_field_id ?? 'null' }},
                      {{ $a->offering->seksi_id ?? 'null' }},
                      {{ json_encode($fieldsArray) }},
                      {{ json_encode($subfieldsArray) }},
                      {{ json_encode($seksisArray) }}
                  )"
              class="bg-white rounded-lg shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto p-6">

              {{-- DEBUG: Tampilkan data untuk testing --}}
              <div class="hidden">
                  <p>Field ID: {{ $a->offering->field_id ?? 'null' }}</p>
                  <p>Sub Field ID: {{ $a->offering->sub_field_id ?? 'null' }}</p>
                  <p>Seksi ID: {{ $a->offering->seksi_id ?? 'null' }}</p>
                  <p>Fields Count: {{ count($fieldsArray) }}</p>
                  <p>Subfields Count: {{ count($subfieldsArray) }}</p>
                  <p>Seksis Count: {{ count($seksisArray) }}</p>
              </div>
              
              <div class="flex justify-between items-center border-b pb-2 mb-4">
                  <h3 class="text-lg font-semibold">{{ $a->offering ? 'Edit' : 'Tambah' }} Offering - {{ $a->name }}</h3>
                  <button type="button"
                          onclick="document.getElementById('offeringModal-{{ $a->id }}').classList.add('hidden')"
                          class="text-gray-500 hover:text-gray-700">&times;</button>
              </div>

              <form method="POST" action="{{ route('admin.applicant.seleksi.offering.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="applicant_id" value="{{ $a->id }}">

                  {{-- Picked By (Readonly) --}}
                  <div>
                      <label class="block text-sm font-medium mb-1">Picked By</label>
                      <input type="text" 
                            class="border rounded w-full px-3 py-2 bg-gray-100 text-gray-700 cursor-not-allowed"
                            value="{{ optional($a->pickedBy)->name ?? '-' }}"
                            readonly>
                  </div>

                  {{-- Posisi (Readonly) --}}
                  <div>
                      <label class="block text-sm font-medium mb-1">Posisi yang Diharapkan</label>
                      <input type="text"
                            class="border rounded w-full px-3 py-2 bg-gray-100 text-gray-700 cursor-not-allowed"
                            value="{{ $a->position->name ?? '-' }}"
                            readonly>
                  </div>

                  {{-- BIDANG --}}
                  <div>
                      <label class="block text-sm font-medium mb-1">Bidang <span class="text-red-500">*</span></label>
                      <select
                          name="field_id"
                          x-model="selectedFieldId"
                          @change="onFieldChange"
                          class="border rounded w-full px-3 py-2"
                          required>
                          <option value="">-- Pilih Bidang --</option>
                          <template x-for="field in fields" :key="field.id">
                              <option :value="field.id" x-text="field.name" 
                                      :selected="field.id == selectedFieldId"></option>
                          </template>
                      </select>
                  </div>
                  
                  {{-- SUB BIDANG --}}
                  <div>
                      <label class="block text-sm font-medium mb-1">Sub Bidang <span class="text-red-500">*</span></label>
                      <select
                          name="sub_field_id"
                          x-model="selectedSubFieldId"
                          @change="onSubFieldChange"
                          :disabled="!selectedFieldId || filteredSubFields.length === 0"
                          class="border rounded w-full px-3 py-2 disabled:bg-gray-100"
                          required>
                          <option value="">-- Pilih Sub Bidang --</option>
                          <template x-for="subField in filteredSubFields" :key="subField.id">
                              <option :value="subField.id" x-text="subField.name"
                                      :selected="subField.id == selectedSubFieldId"></option>
                          </template>
                      </select>
                      
                      <div x-show="selectedFieldId && filteredSubFields.length === 0" 
                          class="mt-1 text-sm text-amber-600">
                          Tidak ada Sub Bidang untuk bidang ini
                      </div>
                  </div>
                  
                  {{-- SEKSI --}}
                  <div>
                      <label class="block text-sm font-medium mb-1">Seksi <span class="text-red-500">*</span></label>
                      <select
                          name="seksi_id"
                          x-model="selectedSeksiId"
                          :disabled="!selectedSubFieldId || filteredSeksis.length === 0"
                          class="border rounded w-full px-3 py-2 disabled:bg-gray-100"
                          required>
                          <option value="">-- Pilih Seksi --</option>
                          <template x-for="seksi in filteredSeksis" :key="seksi.id">
                              <option :value="seksi.id" x-text="seksi.name"
                                      :selected="seksi.id == selectedSeksiId"></option>
                          </template>
                      </select>
                      
                      <div x-show="selectedSubFieldId && filteredSeksis.length === 0" 
                          class="mt-1 text-sm text-amber-600">
                          Tidak ada Seksi untuk sub bidang ini
                      </div>
                  </div>
                  
                  {{-- JABATAN (tetap sama) --}}
                  <div>
                      <label class="block text-sm font-medium mb-1">Jabatan <span class="text-red-500">*</span></label>
                      <select name="job_id" class="border rounded w-full px-3 py-2" required>
                          <option value="">-- Pilih Jabatan --</option>
                          @foreach($jobs as $job)
                              <option value="{{ $job->id }}" {{ optional($a->offering)->job_id == $job->id ? 'selected' : '' }}>
                                  {{ $job->name }}
                              </option>
                          @endforeach
                      </select>
                  </div>
                  
                  {{-- Gaji & Tanggal --}}
                  <div class="grid grid-cols-2 gap-3">
                      <div>
                          <label class="block text-sm font-medium mb-1">Gaji <span class="text-red-500">*</span></label>
                          <input type="number" 
                                name="gaji" 
                                class="border rounded w-full px-3 py-2"
                                value="{{ old('gaji', $a->offering->gaji ?? '') }}" 
                                min="0"
                                required>
                      </div>
                      <div>
                          <label class="block text-sm font-medium mb-1">Uang Makan <span class="text-red-500">*</span></label>
                          <input type="number" 
                                name="uang_makan" 
                                class="border rounded w-full px-3 py-2"
                                value="{{ old('uang_makan', $a->offering->uang_makan ?? '') }}" 
                                min="0"
                                required>
                      </div>
                      <div>
                          <label class="block text-sm font-medium mb-1">Uang Transport <span class="text-red-500">*</span></label>
                          <input type="number" 
                                name="uang_transport" 
                                class="border rounded w-full px-3 py-2"
                                value="{{ old('uang_transport', $a->offering->uang_transport ?? '') }}" 
                                min="0"
                                required>
                      </div>
                      <div>
                          <label class="block text-sm font-medium mb-1">Tanggal Kontrak Mulai <span class="text-red-500">*</span></label>
                          <input type="date" 
                                name="kontrak_mulai" 
                                class="border rounded w-full px-3 py-2"
                                value="{{ old('kontrak_mulai', optional(optional($a->offering)->kontrak_mulai)->format('Y-m-d')) }}" 
                                required>
                      </div>
                      <div>
                          <label class="block text-sm font-medium mb-1">Tanggal Kontrak Selesai <span class="text-red-500">*</span></label>
                          <input type="date" 
                                name="kontrak_selesai" 
                                class="border rounded w-full px-3 py-2"
                                value="{{ old('kontrak_selesai', optional(optional($a->offering)->kontrak_selesai)->format('Y-m-d')) }}" 
                                required>
                      </div>
                  </div>

                  {{-- Link --}}
                  <div>
                      <label class="block text-sm font-medium mb-1">Link PKWT <span class="text-red-500">*</span></label>
                      <input type="text" 
                            name="link_pkwt" 
                            class="border rounded w-full px-3 py-2"
                            value="{{ old('link_pkwt', $a->offering->link_pkwt ?? '') }}" 
                            placeholder="https://..."
                            required>
                  </div>
                  <div>
                      <label class="block text-sm font-medium mb-1">Link Berkas <span class="text-red-500">*</span></label>
                      <input type="url" 
                            name="link_berkas" 
                            class="border rounded w-full px-3 py-2"
                            value="{{ old('link_berkas', $a->offering->link_berkas ?? '') }}" 
                            placeholder="https://..."
                            required>
                  </div>
                  <div>
                      <label class="block text-sm font-medium mb-1">Link Form Pelamar <span class="text-red-500">*</span></label>
                      <input type="url" 
                            name="link_form_pelamar" 
                            class="border rounded w-full px-3 py-2"
                            value="{{ old('link_form_pelamar', $a->offering->link_form_pelamar ?? '') }}" 
                            placeholder="https://..."
                            required>
                  </div>

                  {{-- Deadline Response --}}
                  <div>
                      <label class="block text-sm font-medium mb-1">
                          Batas Waktu Respon Offering <span class="text-red-500">*</span>
                      </label>
                      <input
                          type="datetime-local"
                          name="response_deadline"
                          class="border rounded w-full px-3 py-2"
                          value="{{ old('response_deadline', $a->offering?->response_deadline?->format('Y-m-d\TH:i')) }}"
                          
                          required
                      >
                      <p class="text-xs text-gray-500 mt-1">
                          <i class="fas fa-info-circle text-blue-500"></i>
                          Jika peserta tidak merespons sampai waktu ini, maka dianggap <strong>menolak offering</strong>.
                      </p>
                  </div>

                  {{-- Action Buttons --}}
                  <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                      <button type="button"
                              onclick="document.getElementById('offeringModal-{{ $a->id }}').classList.add('hidden')"
                              class="px-4 py-2 border rounded hover:bg-gray-50 transition">
                          Batal
                      </button>
                      <button type="submit" 
                              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                          <i class="fas fa-save mr-1"></i> Simpan Offering
                      </button>
                  </div>
              </form>
          </div>
      </div>
    @endforeach


    <div class="mt-4">{{ $applicants->links() }}</div>
  </div>
{{-- ✅ Modal Email Offering --}}
<div id="emailModalOffering" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-5">
    <div class="flex justify-between items-center border-b pb-2 mb-4">
      <h3 class="text-lg font-semibold">Kirim Email Offering</h3>
      <button type="button" onclick="document.getElementById('emailModalOffering').classList.add('hidden')"
              class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
    </div>

    {{-- Tabs --}}
    <div class="border-b mb-4 flex">
      <button type="button" data-tab="tabOffering"
              class="tab-btn-off px-4 py-2 border-b-2 border-blue-600 text-blue-600">Offering</button>
      <button type="button" data-tab="tabTerpilihOffering"
              class="tab-btn-off px-4 py-2 border-b-2 border-transparent">Terpilih</button>
    </div>

    {{-- Tab Offering --}}
    <div id="tabOffering" class="tab-content-off">
      <form method="POST" action="{{ route('admin.applicant.seleksi.offering.sendEmail') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="offering">
        <input type="hidden" name="batch" value="{{ request('batch') }}">
        <input type="hidden" name="position" value="{{ request('position') }}">

        <div class="mb-3 flex items-center gap-2">
          <input type="checkbox" id="useTemplateOffering" class="rounded">
          <label for="useTemplateOffering" class="text-sm font-medium">Gunakan template</label>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium">Subjek</label>
          <input type="text" name="subject" id="subjectOffering" class="border rounded w-full px-2 py-1" required>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium">Isi Email</label>
          <input id="messageOffering" type="hidden" name="message">
          <div class="border rounded w-full h-64 overflow-y-auto">
            <trix-editor input="messageOffering" class="trix-content w-full h-full"></trix-editor>
          </div>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium">Lampiran</label>
          <input type="file" name="attachments[]" multiple>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" onclick="document.getElementById('emailModalOffering').classList.add('hidden')"
                  class="px-3 py-1 border rounded">Batal</button>
          <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
        </div>
      </form>
    </div>

    {{-- Tab Terpilih --}}
    <div id="tabTerpilihOffering" class="tab-content-off hidden">
      <form method="POST" action="{{ route('admin.applicant.seleksi.offering.sendEmail') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="selected">
        <input type="hidden" name="ids" id="selectedIdsOffering">

        <div class="mb-3">
          <label class="block text-sm font-medium">Subjek</label>
          <input type="text" name="subject" class="border rounded w-full px-2 py-1" required>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium">Isi Email</label>
          <input id="messageSelectedOffering" type="hidden" name="message">
          <trix-editor input="messageSelectedOffering" class="trix-content border rounded w-full"></trix-editor>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium">Lampiran</label>
          <input type="file" name="attachments[]" multiple>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" onclick="document.getElementById('emailModalOffering').classList.add('hidden')"
                  class="px-3 py-1 border rounded">Batal</button>
          <button type="submit" onclick="setSelectedIdsOffering()" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
        </div>
      </form>
    </div>
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

  {{-- ✅ Modal Filter Offering --}}
  <div id="filterModalOffering" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
          <div class="flex justify-between items-center border-b pb-3 mb-4">
              <h3 class="text-lg font-semibold">Filter Data Offering</h3>
              <button type="button"
                      onclick="document.getElementById('filterModalOffering').classList.add('hidden')"
                      class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
          </div>

          <form method="GET" action="{{ route('admin.applicant.seleksi.offering.index') }}" class="space-y-4">
              {{-- Filter Batch --}}
              <div>
                  <label class="block text-sm font-medium mb-1">Batch</label>
                  <select name="batch" class="border rounded w-full px-2 py-1 text-sm">
                      <option value="">Semua Batch</option>
                      @foreach($batches as $b)
                          <option value="{{ $b->id }}" {{ request('batch') == $b->id ? 'selected' : '' }}>
                              {{ $b->name }}
                          </option>
                      @endforeach
                  </select>
              </div>

              {{-- Filter Posisi --}}
              <div>
                  <label class="block text-sm font-medium mb-1">Posisi</label>
                  <select name="position" class="border rounded w-full px-2 py-1 text-sm">
                      <option value="">Semua Posisi</option>
                      @foreach($positions as $p)
                          <option value="{{ $p->id }}" {{ request('position') == $p->id ? 'selected' : '' }}>
                              {{ $p->name }}
                          </option>
                      @endforeach
                  </select>
              </div>

              {{-- Filter Status (Mengganti Filter Jurusan) --}}
              <div>
                  <label class="block text-sm font-medium mb-1">Status</label>
                  <select name="status" class="border rounded w-full px-2 py-1 text-sm">
                      <option value="">Semua Status</option>
                      <option value="Offering" {{ request('status') == 'Offering' ? 'selected' : '' }}>
                          Offering
                      </option>
                      <option value="Menerima Offering" {{ request('status') == 'Menerima Offering' ? 'selected' : '' }}>
                          Menerima Offering
                      </option>
                      <option value="Menolak Offering" {{ request('status') == 'Menolak Offering' ? 'selected' : '' }}>
                          Menolak Offering
                      </option>
                  </select>
              </div>

              <div class="flex justify-end gap-2 pt-4">
                  <button type="button"
                          onclick="document.getElementById('filterModalOffering').classList.add('hidden')"
                          class="px-3 py-1 border rounded hover:bg-gray-50">Batal</button>
                  <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                      Terapkan Filter
                  </button>
              </div>
          </form>
      </div>
  </div>

</x-app-admin>

{{-- ✅ Script --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.umd.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.min.css"/>

@verbatim
<script>
  document.getElementById('useTemplateOffering')?.addEventListener('change', function() {
    const trix = document.querySelector("trix-editor[input=messageOffering]");
    const subjectInput = document.getElementById('subjectOffering'); // ✅ tambahkan baris ini

    if (this.checked) {
      subjectInput.value = "INFORMASI OFFERING - PLN ICON PLUS";
      trix.editor.loadHTML(`
        Selamat! Anda <strong>dinyatakan terpilih untuk menerima penawaran kerja (Offering)</strong>
        dari <strong>PLN ICON PLUS</strong> untuk posisi
        <strong>{{job}}</strong> pada
        <strong>Bidang {{field}}</strong>,
        <strong>Sub Bidang {{subfield}}</strong>,
        dan ditempatkan pada
        <strong>Seksi {{seksi}}</strong>.<br><br>

        Berikut adalah rincian penawaran yang kami sampaikan:<br>
        - Gaji Pokok: Rp{{gaji}}<br>
        - Uang Makan: Rp{{uang_makan}}<br>
        - Uang Transport: Rp{{uang_transport}}<br>
        - Periode Kontrak: {{periode_kontrak}}<br>
        - Tanggal Kontrak: {{kontrak_mulai}} hingga {{kontrak_selesai}}<br><br>

        Mohon untuk mempelajari dokumen berikut terlebih dahulu:<br>
        <a href="{{link_pkwt}}" target="_blank">Surat Perjanjian Kerja Waktu Tertentu (PKWT)</a><br>

        Jika Anda bersedia menerima penawaran ini, silakan mengisi formulir konfirmasi berikut:<br>
        <a href="{{link_berkas}}" target="_blank">Form Berkas</a><br>
        <a href="{{link_form_pelamar}}" target="_blank">Form Konfirmasi Penerimaan Penawaran</a><br><br>

        Terima kasih atas waktu dan dedikasi Anda selama proses seleksi.<br>
        Kami menantikan konfirmasi dari Anda.<br><br>

        Salam hangat,<br>
        <strong>Tim Recruitment PLN ICON PLUS</strong>
      `);
    } else {
      subjectInput.value = "";
      trix.editor.loadHTML("");
    }
  });

  document.querySelectorAll('.tab-btn-off').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.tab-btn-off').forEach(b => b.classList.remove('border-blue-600','text-blue-600'));
      this.classList.add('border-blue-600','text-blue-600');
      document.querySelectorAll('.tab-content-off').forEach(c => c.classList.add('hidden'));
      document.getElementById(this.dataset.tab).classList.remove('hidden');
    });
  });

  function offeringForm() {
      return {
          // Data collections
          fields: [],
          subfields: [],
          seksis: [],
          
          // Selected IDs
          selectedFieldId: null,
          selectedSubFieldId: null,
          selectedSeksiId: null,
          
          // Computed properties
          get filteredSubFields() {
              if (!this.selectedFieldId) return [];
              return this.subfields.filter(
                  subfield => subfield.field_id == this.selectedFieldId
              );
          },
          
          get filteredSeksis() {
              if (!this.selectedSubFieldId) return [];
              return this.seksis.filter(
                  seksi => seksi.sub_field_id == this.selectedSubFieldId
              );
          },
          
          // Initialize data
          initData(initFieldId, initSubFieldId, initSeksiId, fieldsData, subfieldsData, seksisData) {
              console.log('Initializing with:', {
                  initFieldId, initSubFieldId, initSeksiId,
                  fieldsCount: fieldsData.length,
                  subfieldsCount: subfieldsData.length,
                  seksisCount: seksisData.length
              });
              
              // Set data collections
              this.fields = fieldsData || [];
              this.subfields = subfieldsData || [];
              this.seksis = seksisData || [];
              
              // Set initial values if provided
              if (initFieldId) {
                  this.selectedFieldId = parseInt(initFieldId);
                  
                  // Cek apakah subfield valid untuk field ini
                  if (initSubFieldId) {
                      const subfieldValid = this.subfields.some(
                          sf => sf.id == initSubFieldId && sf.field_id == this.selectedFieldId
                      );
                      
                      if (subfieldValid) {
                          this.selectedSubFieldId = parseInt(initSubFieldId);
                          
                          // Cek apakah seksi valid untuk subfield ini
                          if (initSeksiId) {
                              const seksiValid = this.seksis.some(
                                  s => s.id == initSeksiId && s.sub_field_id == this.selectedSubFieldId
                              );
                              
                              if (seksiValid) {
                                  this.selectedSeksiId = parseInt(initSeksiId);
                              }
                          }
                      }
                  }
              }
              
              console.log('After initialization:', {
                  selectedFieldId: this.selectedFieldId,
                  selectedSubFieldId: this.selectedSubFieldId,
                  selectedSeksiId: this.selectedSeksiId,
                  filteredSubFields: this.filteredSubFields.length,
                  filteredSeksis: this.filteredSeksis.length
              });
          },
          
          // Event handlers
          onFieldChange() {
              console.log('Field changed to:', this.selectedFieldId);
              
              // Reset dependent fields
              this.selectedSubFieldId = null;
              this.selectedSeksiId = null;
              
              // Jika ada subfield sebelumnya yang masih valid, keep it
              if (this.selectedFieldId && this.initSubFieldId) {
                  const stillValid = this.subfields.some(
                      sf => sf.id == this.initSubFieldId && sf.field_id == this.selectedFieldId
                  );
                  if (stillValid) {
                      this.selectedSubFieldId = this.initSubFieldId;
                  }
              }
          },
          
          onSubFieldChange() {
              console.log('SubField changed to:', this.selectedSubFieldId);
              
              // Reset seksi
              this.selectedSeksiId = null;
              
              // Jika ada seksi sebelumnya yang masih valid, keep it
              if (this.selectedSubFieldId && this.initSeksiId) {
                  const stillValid = this.seksis.some(
                      s => s.id == this.initSeksiId && s.sub_field_id == this.selectedSubFieldId
                  );
                  if (stillValid) {
                      this.selectedSeksiId = this.initSeksiId;
                  }
              }
          },
          
          // Debug function
          debug() {
              console.log('Current state:', {
                  fields: this.fields,
                  selectedFieldId: this.selectedFieldId,
                  selectedSubFieldId: this.selectedSubFieldId,
                  selectedSeksiId: this.selectedSeksiId,
                  filteredSubFields: this.filteredSubFields,
                  filteredSeksis: this.filteredSeksis
              });
          }
      }
  }

  // Confirm Modal
    let selectedAction = null;

    function getSelectedIds() {
      return document.querySelectorAll('input[name="ids[]"]:checked');
    }

    function openConfirmModal(action) {
      const selected = getSelectedIds();

      if (selected.length === 0) {
        alert('Pilih peserta terlebih dahulu.');
        return;
      }

      selectedAction = action;

      let msg = '';
      if (action === 'accepted') {
        msg = "Apakah Anda yakin ingin MENERIMA offering untuk peserta yang dipilih?";
      } else if (action === 'decline') {
        msg = "Apakah Anda yakin ingin MENOLAK offering untuk peserta yang dipilih?";
      }

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
    
    document.getElementById('confirmYesBtn')?.addEventListener('click', function () {
      const selected = getSelectedIds();

      if (selected.length === 0) {
        alert('Tidak ada peserta yang dipilih.');
        return;
      }

      const form = document.getElementById('bulkActionForm');

      // hapus input lama jika ada
      form.querySelectorAll('input[name="bulk_action"]').forEach(el => el.remove());

      let input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'bulk_action';
      input.value = selectedAction;

      form.appendChild(input);
      form.submit();
    });

  function setSelectedIdsOffering() {
    let ids = [];
    let hasUnfinalized = false;

    document.querySelectorAll('input[name="ids[]"]:checked').forEach(cb => {
      ids.push(cb.value);

      // 🔴 RULE OFFERING
      if (
        cb.dataset.status !== 'Menerima Offering' &&
        cb.dataset.status !== 'Menolak Offering'
      ) {
        hasUnfinalized = true;
      }
    });

    if (!ids.length) {
      alert("Silakan pilih peserta terlebih dahulu.");
      event.preventDefault();
      return false;
    }

    if (hasUnfinalized) {
      alert("Terdapat peserta yang belum menerima atau menolak offering.");
      event.preventDefault();
      return false;
    }

    document.getElementById('selectedIdsOffering').value = ids.join(',');
    return true;
  }

  document.getElementById('checkAll')?.addEventListener('change', function () {
    document.querySelectorAll('input[name="ids[]"]:not(:disabled)')
      .forEach(cb => cb.checked = this.checked);
  });
</script>
@endverbatim