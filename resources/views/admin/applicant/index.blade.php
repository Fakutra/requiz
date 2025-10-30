{{-- resources/views/admin/applicant/index.blade.php --}}
<x-app-admin>
  <div x-data="applicantPage()" x-init="init()" class="space-y-6">

    

    <div class="bg-white border rounded-lg shadow-sm p-4">
      <h1 class="text-2xl font-bold text-blue-950 mb-4">Data Applicant</h1>
       {{-- Search selalu tampil --}}
      <div class="flex w-full mb-2 items-end gap-2">
        {{-- Search --}}
        <form method="GET" action="{{ route('admin.applicant.index') }}" class="flex-1 min-w-[220px]">
          <div class="relative flex items-center">
            <input type="text" name="search" value="{{ request('search') }}"
              placeholder="Cari Nama / Email / Jurusan / Posisi..."
              class="w-full h-10 pl-3 pr-9 rounded text-sm 
              border-[1px] border-[#8B8B8B] 
              focus:outline-none focus:border-[#A0A0A0] focus:ring-1 focus:ring-[#A0A0A0]">
            <span class="absolute right-3 text-gray-500">
              <x-search-button/>
            </span>
          </div>
        </form>

        {{-- Tombol Filter --}}
        <button type="button"
                @click="showFilter=true"
                class="h-10 px-3 py-2 border rounded bg-gray-600 text-white flex items-center justify-center">
          <i class="fas fa-filter"></i>
        </button>

        {{-- Tombol Export --}}
        <a href="{{ route('admin.applicant.export', request()->query()) }}"
          class="h-10 flex items-center gap-2 px-3 border rounded bg-[#1FD33A] text-white text-sm">
          <x-export-button/>
          Export
        </a>
      </div>

      </form>
        <div class="w-full overflow-x-auto">
          <table class="table-auto w-auto text-sm border-collapse">
            <thead class="bg-gray-50 text-left text-gray-700">
              <tr>
                <th class="px-4 py-2 text-left whitespace-nowrap">No.</th>

                {{-- Nama --}}
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    <a href="{{ request()->fullUrlWithQuery([
                        'sort' => 'name',
                        'direction' => (request('sort') === 'name' && request('direction') === 'asc') ? 'desc' : 'asc'
                    ]) }}"
                    class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                        Nama
                        <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'name' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </th>

                {{-- Email --}}
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    <a href="{{ request()->fullUrlWithQuery([
                        'sort' => 'email',
                        'direction' => (request('sort') === 'email' && request('direction') === 'asc') ? 'desc' : 'asc'
                    ]) }}"
                    class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                        Email
                        <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'email' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </th>

                {{-- Posisi --}}
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    <a href="{{ request()->fullUrlWithQuery([
                        'sort' => 'position_id',
                        'direction' => (request('sort') === 'position_id' && request('direction') === 'asc') ? 'desc' : 'asc'
                    ]) }}"
                    class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                        Posisi
                        <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'position_id' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </th>

                {{-- Ekspektasi Gaji --}}
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    <a href="{{ request()->fullUrlWithQuery([
                        'sort' => 'ekspektasi_gaji',
                        'direction' => (request('sort') === 'ekspektasi_gaji' && request('direction') === 'asc') ? 'desc' : 'asc'
                    ]) }}"
                    class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                        Ekspektasi Gaji
                        <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'ekspektasi_gaji' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </th>

                {{-- Umur --}}
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    <a href="{{ request()->fullUrlWithQuery([
                        'sort' => 'umur',
                        'direction' => (request('sort') === 'umur' && request('direction') === 'asc') ? 'desc' : 'asc'
                    ]) }}"
                    class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                        Umur
                        <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'umur' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </th>

                {{-- Pendidikan --}}
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    <a href="{{ request()->fullUrlWithQuery([
                        'sort' => 'pendidikan',
                        'direction' => (request('sort') === 'pendidikan' && request('direction') === 'asc') ? 'desc' : 'asc'
                    ]) }}"
                    class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                        Pendidikan
                        <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'pendidikan' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </th>

                {{-- Jurusan --}}
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    <a href="{{ request()->fullUrlWithQuery([
                        'sort' => 'jurusan',
                        'direction' => (request('sort') === 'jurusan' && request('direction') === 'asc') ? 'desc' : 'asc'
                    ]) }}"
                    class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                        Jurusan
                        <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'jurusan' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </th>

                {{-- Batch --}}
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    <a href="{{ request()->fullUrlWithQuery([
                        'sort' => 'batch_id',
                        'direction' => (request('sort') === 'batch_id' && request('direction') === 'asc') ? 'desc' : 'asc'
                    ]) }}"
                    class="flex items-center gap-1 font-semibold text-gray-800 no-underline hover:text-gray-900">
                        Batch
                        <svg class="w-4 h-4 ml-1 transform {{ request('sort') === 'batch_id' && request('direction','asc') === 'desc' ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </th>

                <th class="px-4 py-2 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($applicants as $a)
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-2 w-auto">{{ ($applicants->currentPage()-1)*$applicants->perPage() + $loop->iteration }}</td>
                  <td class="px-4 py-2 w-auto">{{ $a->name }}</td>
                  <td class="px-4 py-2 w-auto">{{ $a->email }}</td>
                  <td class="px-4 py-2 w-auto">{{ $a->position->name ?? '-' }}</td>
                  <td class="px-4 py-2 w-auto">{{ $a->ekspektasi_gaji_formatted ?? '-' }}</td>
                  <td class="px-4 py-2 w-auto">{{ $a->age }} tahun</td>
                  <td class="px-4 py-2 w-auto">{{ $a->pendidikan }} - {{ $a->universitas }}</td>
                  <td class="px-4 py-2 w-auto">{{ $a->jurusan }}</td>
                  <td class="px-4 py-2 w-auto">{{ $a->batch->name ?? $a->batch_id ?? '-' }}</td>

                  <td class="px-4 py-2 text-center">
                    <div class="flex justify-center items-center gap-2">

                      {{-- Lihat (eye) --}}
                      <button type="button"
                              class="p-2 rounded hover:bg-gray-100"
                              title="Lihat Detail"
                              @click="openDetail(@js([
                                'id'          => $a->id,
                                'name'        => $a->name,
                                'email'       => $a->email,
                                'nik'         => $a->nik,
                                'no_telp'     => $a->no_telp,
                                'tpt_lahir'   => $a->tpt_lahir,
                                'tgl_lahir'   => optional($a->tgl_lahir)->format('Y-m-d'),
                                'alamat'      => $a->alamat,
                                'pendidikan'  => $a->pendidikan,
                                'universitas' => $a->universitas,
                                'jurusan'     => $a->jurusan,
                                'thn_lulus'   => $a->thn_lulus,
                                'position_id' => $a->position_id,
                                'position'    => $a->position->name ?? null,
                                'batch_id'    => $a->batch_id,
                                'batch'       => $a->batch->name ?? null,
                                'status'      => $a->status,
                                'skills'      => $a->skills,
                                'cv_document' => $a->cv_document,
                                'age'         => $a->age,
                                'ekspektasi_gaji' => $a->ekspektasi_gaji,
                                'ekspektasi_gaji_formatted' => $a->ekspektasi_gaji_formatted,
                              ]))">
                        <x-view-button/>
                      </button>

                      {{-- Edit (pencil) --}}
                      <button type="button"
                              class="p-2 rounded hover:bg-gray-100"
                              title="Edit"
                              @click="openEdit(@js([
                                'id'          => $a->id,
                                'name'        => $a->name,
                                'email'       => $a->email,
                                'nik'         => $a->nik,
                                'no_telp'     => $a->no_telp,
                                'tpt_lahir'   => $a->tpt_lahir,
                                'tgl_lahir'   => optional($a->tgl_lahir)->format('Y-m-d'),
                                'alamat'      => $a->alamat,
                                'pendidikan'  => $a->pendidikan,
                                'universitas' => $a->universitas,
                                'jurusan'     => $a->jurusan,
                                'thn_lulus'   => $a->thn_lulus,
                                'position_id' => $a->position_id,
                                'batch_id'    => $a->batch_id,
                                'status'      => $a->status,
                                'skills'      => $a->skills,
                                'cv_document' => $a->cv_document,
                                'age'         => $a->age,
                                'ekspektasi_gaji' => $a->ekspektasi_gaji,
                              ]))">
                        <x-edit-button/>
                      </button>

                      {{-- Hapus (trash) --}}
                      <button type="button"
                              class="p-2 rounded hover:bg-gray-100"
                              title="Hapus"
                              @click="openDelete({ id: {{ $a->id }}, name: @js($a->name) })">
                        <x-delete-button/>
                      </button>

                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="px-4 py-6 text-center text-gray-500">Tidak ada data.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">{{ $applicants->withQueryString()->links() }}</div>

    {{-- ======================= MODALS ======================= --}}
    
    {{-- Filter Modal --}}
    <div x-cloak x-show="showFilter"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
  <div @click.outside="showFilter=false"
       class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold">Filter Data</h2>
      <button class="text-gray-500 hover:text-gray-700 text-xl"
              @click="showFilter=false">&times;</button>
    </div>

    <form method="GET" action="{{ route('admin.applicant.index') }}" class="space-y-4">
      {{-- Batch --}}
      <div>
        <label class="block text-xs text-gray-500 mb-1">Batch</label>
        <select name="batch" class="w-full border rounded px-3 py-2 text-sm">
          <option value="">— Semua Batch —</option>
          @foreach($batches as $b)
            <option value="{{ $b->id }}" @selected(request('batch') == $b->id)>
              {{ $b->name ?? $b->id }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Position --}}
      <div>
        <label class="block text-xs text-gray-500 mb-1">Posisi</label>
        <select name="position" class="w-full border rounded px-3 py-2 text-sm">
          <option value="">— Semua Posisi —</option>
          @foreach($positions as $pos)
            <option value="{{ $pos->id }}" @selected(request('position') == $pos->id)>
              {{ $pos->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <a href="{{ route('admin.applicant.index') }}"
           class="px-4 py-2 bg-gray-100 rounded text-sm hover:bg-gray-200">
          Reset
        </a>
        <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
          Terapkan
        </button>
      </div>
    </form>
  </div>
</div>

    {{-- Detail Modal --}}
    <div x-cloak x-show="showDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div @click.outside="closeDetail()" class="bg-white w-full max-w-3xl rounded-lg shadow-lg p-6 overflow-y-auto max-h-[92vh]">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Detail Applicant</h2>
          <button class="text-gray-500 hover:text-gray-700 text-xl" @click="closeDetail()">&times;</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div><div class="text-xs text-gray-500">Nama</div><div class="font-medium" x-text="view.name"></div></div>
          <div><div class="text-xs text-gray-500">Email</div><div class="font-medium" x-text="view.email"></div></div>
          <div><div class="text-xs text-gray-500">Posisi</div><div class="font-medium" x-text="view.position ?? '-'"></div></div>
          <div>
            <div class="text-xs text-gray-500">Ekspektasi Gaji</div>
            <div class="font-medium" x-text="view.ekspektasi_gaji_formatted ?? '-'"></div>
          </div>
          <div><div class="text-xs text-gray-500">Batch</div><div class="font-medium" x-text="view.batch ?? (view.batch_id ?? '-')"></div></div>
          <div><div class="text-xs text-gray-500">Status</div><div class="font-medium" x-text="view.status ?? '-'"></div></div>
          <div><div class="text-xs text-gray-500">No. Telp</div><div class="font-medium" x-text="view.no_telp ?? '-'"></div></div>
          <div><div class="text-xs text-gray-500">NIK</div><div class="font-medium" x-text="view.nik ?? '-'"></div></div>
          <div><div class="text-xs text-gray-500">TTL</div><div class="font-medium" x-text="`${view.tpt_lahir ?? '-'}, ${view.tgl_lahir ?? '-'}`"></div></div>
          <div class="md:col-span-2"><div class="text-xs text-gray-500">Alamat</div><div class="font-medium" x-text="view.alamat ?? '-'"></div></div>
          <div><div class="text-xs text-gray-500">Pendidikan</div><div class="font-medium" x-text="view.pendidikan ?? '-'"></div></div>
          <div><div class="text-xs text-gray-500">Universitas</div><div class="font-medium" x-text="view.universitas ?? '-'"></div></div>
          <div><div class="text-xs text-gray-500">Jurusan</div><div class="font-medium" x-text="view.jurusan ?? '-'"></div></div>
          <div><div class="text-xs text-gray-500">Tahun Lulus</div><div class="font-medium" x-text="view.thn_lulus ?? '-'"></div></div>
          

          <div class="md:col-span-2">
            <div class="text-xs text-gray-500">CV</div>
            <template x-if="view.cv_document">
              <a :href="storageUrl(view.cv_document)" target="_blank"
                 class="inline-flex items-center gap-2 px-3 py-2 mt-1 rounded bg-gray-100 hover:bg-gray-200 text-sm">
                Lihat CV (PDF)
              </a>
            </template>
            <template x-if="!view.cv_document">
              <div class="font-medium">-</div>
            </template>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm" @click="closeDetail()">Tutup</button>
        </div>
      </div>
    </div>

    {{-- Edit Modal --}}
    <div x-cloak x-show="showEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div @click.outside="closeEdit()" class="bg-white w-full max-w-3xl rounded-lg shadow-lg p-6 overflow-y-auto max-h-[92vh]">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Edit Applicant</h2>
          <button class="text-gray-500 hover:text-gray-700 text-xl" @click="closeEdit()">&times;</button>
        </div>

        <form :action="updateUrl()" method="POST" enctype="multipart/form-data" class="space-y-4">
          @csrf @method('PUT')

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium">Nama</label>
              <input type="text" name="name" x-model="form.name" class="w-full mt-1 border rounded px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium">Email</label>
              <input type="email" name="email" x-model="form.email" class="w-full mt-1 border rounded px-3 py-2 text-sm">
            </div>

            <div>
              <label class="block text-sm font-medium">NIK</label>
              <input type="text" name="nik" x-model="form.nik" class="w-full mt-1 border rounded px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium">No. Telepon</label>
              <input type="text" name="no_telp" x-model="form.no_telp" class="w-full mt-1 border rounded px-3 py-2 text-sm">
            </div>

            <div>
              <label class="block text-sm font-medium">Tempat Lahir</label>
              <input type="text" name="tpt_lahir" x-model="form.tpt_lahir" class="w-full mt-1 border rounded px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium">Tanggal Lahir</label>
              <input type="date" name="tgl_lahir" x-model="form.tgl_lahir" class="w-full mt-1 border rounded px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium">Alamat</label>
              <textarea name="alamat" x-model="form.alamat" class="w-full mt-1 border rounded px-3 py-2 text-sm"></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium">Pendidikan</label>
              <select name="pendidikan" x-model="form.pendidikan" class="w-full mt-1 border rounded px-3 py-2 text-sm">
                <option value="">— Pilih —</option>
                <template x-for="opt in ['SMA/Sederajat','Diploma','S1','S2','S3']" :key="opt">
                  <option :value="opt" x-text="opt"></option>
                </template>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium">Universitas</label>
              <input type="text" name="universitas" x-model="form.universitas" class="w-full mt-1 border rounded px-3 py-2 text-sm">
            </div>

            <div>
              <label class="block text-sm font-medium">Jurusan</label>
              <input type="text" name="jurusan" x-model="form.jurusan" class="w-full mt-1 border rounded px-3 py-2 text-sm">
            </div>

            <div>
              <label class="block text-sm font-medium">Tahun Lulus</label>
              <input type="text" name="thn_lulus" x-model="form.thn_lulus" class="w-full mt-1 border rounded px-3 py-2 text-sm" placeholder="YYYY">
            </div>

            <div>
              <label class="block text-sm font-medium">Posisi</label>
              <select name="position_id" x-model.number="form.position_id" class="w-full mt-1 border rounded px-3 py-2 text-sm">
                <option value="">— Pilih Posisi —</option>
                @foreach($positions as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium">Ekspektasi Gaji (Rp)</label>
              <input type="number" name="ekspektasi_gaji"
                    x-model="form.ekspektasi_gaji"
                    class="w-full mt-1 border rounded px-3 py-2 text-sm"
                    placeholder="Contoh: 5500000" min="0">
            </div>

            <div>
              <label class="block text-sm font-medium">Batch</label>
              <select name="batch_id" x-model.number="form.batch_id" class="w-full mt-1 border rounded px-3 py-2 text-sm">
                <option value="">— Pilih Batch —</option>
                @foreach($batches as $b)
                  <option value="{{ $b->id }}">{{ $b->name ?? $b->id }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium">Status</label>
              <select name="status" x-model="form.status" class="w-full mt-1 border rounded px-3 py-2 text-sm">
                <optgroup label="Tahap">
                  <option>Seleksi Administrasi</option>
                  <option>Tes Tulis</option>
                  <option>Technical Test</option>
                  <option>Interview</option>
                  <option>Offering</option>
                </optgroup>
                <optgroup label="Tidak Lolos">
                  <option>Tidak Lolos Seleksi Administrasi</option>
                  <option>Tidak Lolos Seleksi Tes Tulis</option>
                  <option>Tidak Lolos Technical Test</option>
                  <option>Tidak Lolos Interview</option>
                </optgroup>
                <optgroup label="Keputusan Offering">
                  <option>Menerima Offering</option>
                  <option>Menolak Offering</option>
                </optgroup>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium">Skills</label>
              <textarea name="skills" x-model="form.skills" class="w-full mt-1 border rounded px-3 py-2 text-sm"></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium">CV (PDF, maks 3MB)</label>
              <input type="file" name="cv_document" accept="application/pdf"
                     class="w-full mt-1 border rounded px-3 py-2 text-sm">
              <template x-if="form.cv_document">
                <p class="mt-2 text-xs">
                  CV saat ini:
                  <a :href="storageUrl(form.cv_document)" target="_blank" class="text-blue-600 underline">Lihat</a>
                </p>
              </template>
            </div>
          </div>

          <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm" @click="closeEdit()">Batal</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Delete Confirm Modal --}}
    <div x-cloak x-show="showDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div @click.outside="closeDelete()" class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold mb-2">Hapus Applicant</h2>
        <p class="text-sm text-gray-700">Anda yakin ingin menghapus <b x-text="del.name"></b>? </p>

        <form :action="destroyUrl()" method="POST" class="mt-6 flex justify-end gap-2">
          @csrf @method('DELETE')
          <button type="button" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-sm" @click="closeDelete()">Batal</button>
          <button class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm">Hapus</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Alpine Component --}}
  <script>
    function applicantPage() {
      const emptyForm = {
        id:null, name:'', email:'',
        nik:'', no_telp:'',
        tpt_lahir:'', tgl_lahir:'',
        alamat:'',
        pendidikan:'', universitas:'', jurusan:'', thn_lulus:'',
        position_id:'', batch_id:'',
        status:'', skills:'',
        cv_document:null,
      };

      return {
        // modal flags
        showDetail:false,
        showEdit:false,
        showDelete:false,
        showFlash:false,
        showFilter:false,

        // data states
        view: { ...emptyForm, position:null, batch:null },
        form: { ...emptyForm },
        del:  { id:null, name:'' },

        // flash
        flash: { type:'success', message:'' },

        // helpers
        baseUpdateUrl:  @json(route('admin.applicant.update', ['applicant' => '__ID__'])),
        baseDestroyUrl: @json(route('admin.applicant.destroy', ['applicant' => '__ID__'])),
        storageBase:    @json(asset('storage')),

        init() {
          // Auto-open flash modal if session has message
          @if(session('success'))
            this.flash = { type:'success', message: @json(session('success')) };
            this.showFlash = true;
          @elseif(session('error'))
            this.flash = { type:'error', message: @json(session('error')) };
            this.showFlash = true;
          @endif
        },

        // actions
        openDetail(data){ this.view = { ...emptyForm, ...data }; this.showDetail = true; },
        closeDetail(){ this.showDetail = false; this.view = { ...emptyForm, position:null, batch:null }; },

        openEdit(data){ this.form = { ...emptyForm, ...data }; this.showEdit = true; },
        closeEdit(){ this.showEdit = false; this.form = { ...emptyForm }; },

        openDelete(data){ this.del = { id:data.id, name:data.name }; this.showDelete = true; },
        closeDelete(){ this.showDelete = false; this.del = { id:null, name:'' }; },

        updateUrl(){ return this.baseUpdateUrl.replace('__ID__', this.form.id ?? ''); },
        destroyUrl(){ return this.baseDestroyUrl.replace('__ID__', this.del.id ?? ''); },
        storageUrl(p){ return `${this.storageBase}/${p}`; },
      }
    }
  </script>
</x-app-admin>