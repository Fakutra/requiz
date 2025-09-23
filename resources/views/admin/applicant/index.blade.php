{{-- resources/views/admin/applicant/index.blade.php --}}
<x-app-admin>
  <div x-data="applicantPage()" x-init="init()" class="space-y-6">

    <h1 class="text-2xl font-bold text-blue-950">Data Applicant</h1>

    <div class="bg-white border rounded-lg shadow-sm p-4">
      {{-- Toolbar 1 baris: Search + Batch + Position + Apply + Reset + Export --}}
      <form method="GET" action="{{ route('admin.applicant.index') }}"
            class="flex flex-wrap items-end gap-2 mb-4">

        {{-- Search (melebar) --}}
        <div class="flex-1 min-w-[220px]">
          <label class="block text-xs text-gray-500 mb-1">Cari</label>
          <input type="text" name="search" value="{{ request('search') }}"
                 placeholder="Nama / Email / Jurusan / Posisi..."
                 class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div class="w-full overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 text-left text-sm font-medium text-gray-700">
                    <tr>
                        <th class="px-4 py-2">No.</th>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Posisi</th>
                        <th class="px-4 py-2">Umur</th>
                        <th class="px-4 py-2">Pendidikan</th>
                        <th class="px-4 py-2">Jurusan</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-800">
                    @forelse ($applicants as $applicant)
                    <tr>
                        <td class="px-4 py-2">{{ ($applicants->currentPage() - 1) * $applicants->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $applicant->name }}</td>
                        <td class="px-4 py-2">{{ $applicant->position->name }}</td>
                        <td class="px-4 py-2">{{ $applicant->age }} tahun</td>
                        <td class="px-4 py-2">{{ $applicant->pendidikan }} - {{ $applicant->universitas }}</td>
                        <td class="px-4 py-2">{{ $applicant->jurusan }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-3">
                                <a @click.prevent="openView({
                                    id: {{ $applicant->id }},
                                    name: '{{ $applicant->name }}',
                                    email: '{{ $applicant->email }}',
                                    nik: '{{ $applicant->nik }}',
                                    no_telp: '{{ $applicant->no_telp }}',
                                    tpt_lahir: '{{ $applicant->tpt_lahir }}',
                                    tgl_lahir: '{{ $applicant->tgl_lahir }}',
                                    alamat: `{{ $applicant->alamat }}`,
                                    pendidikan: '{{ $applicant->pendidikan }}',
                                    universitas: '{{ $applicant->universitas }}',
                                    jurusan: '{{ $applicant->jurusan }}',
                                    thn_lulus: '{{ $applicant->thn_lulus }}',
                                    position_id: '{{ $applicant->position_id }}',
                                    status: '{{ $applicant->status }}',
                                    skills: `{{ $applicant->skills ?? '-' }}`
                                })"
                                    class="text-blue-400">
                                    <x-view-button/>
                                </a>
                                <a @click.prevent="openEdit({
                                    id: {{ $applicant->id }},
                                    name: '{{ $applicant->name }}',
                                    email: '{{ $applicant->email }}',
                                    nik: '{{ $applicant->nik }}',
                                    no_telp: '{{ $applicant->no_telp }}',
                                    tpt_lahir: '{{ $applicant->tpt_lahir }}',
                                    tgl_lahir: '{{ $applicant->tgl_lahir }}',
                                    alamat: `{{ $applicant->alamat }}`,
                                    pendidikan: '{{ $applicant->pendidikan }}',
                                    universitas: '{{ $applicant->universitas }}',
                                    jurusan: '{{ $applicant->jurusan }}',
                                    thn_lulus: '{{ $applicant->thn_lulus }}',
                                    position_id: '{{ $applicant->position_id }}',
                                    status: '{{ $applicant->status }}',
                                    skills: `{{ $applicant->skills ?? '-' }}`
                                })"
                                    class="text-amber-400">
                                    <x-edit-button/>
                                </a>
                                <form action="{{ route('admin.applicant.destroy', $applicant->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('delete')
                                    <button type="submit" class="text-red-600">
                                        <x-delete-button/>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div x-show="showEdit" x-cloak class="fixed inset-0 flex items-center justify-center z-50 bg-black/40 backdrop-blur-md">
                        <div @click.away="showEdit = false" @click.stop
                            class="bg-white w-full max-w-3xl p-6 rounded-lg shadow-lg overflow-y-auto max-h-[90vh]">
                            <h2 class="text-lg font-semibold mb-4">Edit Pelamar</h2>

                            <form :action="`/admin/applicant/${editData.id}`" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium">Nama</label>
                                        <input type="text" name="name" x-model="editData.name"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Email</label>
                                        <input type="email" name="email" x-model="editData.email"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">NIK</label>
                                        <input type="text" name="nik" x-model="editData.nik"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">No. Telepon</label>
                                        <input type="text" name="no_telp" x-model="editData.no_telp"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Tempat Lahir</label>
                                        <input type="text" name="tpt_lahir" x-model="editData.tpt_lahir"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Tanggal Lahir</label>
                                        <input type="date" name="tgl_lahir" x-model="editData.tgl_lahir"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div class="col-span-2">
                                        <label class="text-sm font-medium">Alamat</label>
                                        <textarea name="alamat" x-model="editData.alamat"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Pendidikan</label>
                                        <select name="pendidikan" x-model="editData.pendidikan"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                            <option value="">-- Pilih --</option>
                                            <option value="SMA">SMA</option>
                                            <option value="D3">D3</option>
                                            <option value="S1">S1</option>
                                            <option value="S2">S2</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Universitas</label>
                                        <input type="text" name="universitas" x-model="editData.universitas"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Jurusan</label>
                                        <input type="text" name="jurusan" x-model="editData.jurusan"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Tahun Lulus</label>
                                        <input type="text" name="thn_lulus" x-model="editData.thn_lulus"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Status</label>
                                        <select name="status" x-model="editData.status"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                            <option value="Proses">Proses</option>
                                            <option value="Diterima">Diterima</option>
                                            <option value="Ditolak">Ditolak</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Skills</label>
                                        <textarea name="skills" x-model="editData.skills"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Posisi</label>
                                        <select name="position_id" x-model="editData.position_id"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                            @foreach ($positions as $position)
                                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end gap-2">
                                    <button type="button" @click="showEdit = false"
                                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded">Batal</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center px-4 py-4 text-gray-500">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $applicants->withQueryString()->links() }}
            </div>
        {{-- Filter Batch --}}
        <div class="min-w-[160px]">
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

        {{-- Filter Position --}}
        <div class="min-w-[200px]">
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

        {{-- Actions --}}
        <div class="flex items-end gap-2 flex-none pb-[2px]">
          <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
            Terapkan
          </button>

          <a href="{{ route('admin.applicant.index') }}"
             class="px-4 py-2 bg-gray-100 rounded text-sm hover:bg-gray-200">
            Reset
          </a>

          <a href="{{ route('admin.applicant.export', request()->query()) }}"
             class="px-4 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700 whitespace-nowrap">
            Export
          </a>
        </div>
      </form>

      {{-- Table --}}
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-left text-gray-700">
            <tr>
              <th class="px-4 py-2">No</th>
              <th class="px-4 py-2">Nama</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Posisi</th>
              <th class="px-4 py-2">Batch</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($applicants as $a)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2">
                  {{ ($applicants->currentPage()-1)*$applicants->perPage() + $loop->iteration }}
                </td>
                <td class="px-4 py-2">{{ $a->name }}</td>
                <td class="px-4 py-2">{{ $a->email }}</td>
                <td class="px-4 py-2">{{ $a->position->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $a->batch->name ?? $a->batch_id ?? '-' }}</td>
                <td class="px-4 py-2">{{ $a->status ?? '-' }}</td>

                <td class="px-4 py-2">
                  <div class="flex justify-end items-center gap-2">

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
                            ]))">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.07.21.07.434 0 .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
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
                            ]))">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M16.862 3.487a2.1 2.1 0 112.97 2.97L8.44 17.85l-4.243 1.272 1.272-4.243 12.393-11.392z" />
                      </svg>
                    </button>

                    {{-- Hapus (trash) --}}
                    <button type="button"
                            class="p-2 rounded hover:bg-gray-100"
                            title="Hapus"
                            @click="openDelete({ id: {{ $a->id }}, name: @js($a->name) })">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0V5a2 2 0 012-2h2a2 2 0 012 2v2" />
                      </svg>
                    </button>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">{{ $applicants->withQueryString()->links() }}</div>
    </div>

    {{-- ======================= MODALS ======================= --}}

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

    {{-- Flash Modal (Success / Error) --}}
    <div x-cloak x-show="showFlash" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div @click.outside="showFlash=false" class="bg-white w-full max-w-sm rounded-lg shadow-lg p-6">
        <div class="flex items-start gap-3">
          <div :class="flash.type === 'success' ? 'text-green-600' : 'text-red-600'">
            <!-- icon -->
            <svg x-show="flash.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2l4 -4M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10 -10 10z" />
            </svg>
            <svg x-show="flash.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v4m0 4h.01M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10 -10 10z" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="font-semibold" x-text="flash.type === 'success' ? 'Berhasil' : 'Terjadi Kesalahan'"></h3>
            <p class="text-sm text-gray-700 mt-1" x-text="flash.message"></p>
          </div>
        </div>
        <div class="mt-4 flex justify-end">
          <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm" @click="showFlash=false">OK</button>
        </div>
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
