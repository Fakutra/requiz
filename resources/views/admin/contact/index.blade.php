<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-5">
    {{-- Header + Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
      <div>
        <h2 class="text-xl font-semibold text-gray-800">Informasi Kontak</h2>
        <p class="text-xs text-gray-500">
          Maksimal 3 kontak boleh aktif. Footer hanya menampilkan hingga 3 kontak aktif.
        </p>
      </div>
      <div class="flex items-center gap-2">
        <form method="GET" class="hidden md:block">
          <input name="q" value="{{ request('q') }}" placeholder="Cari email / PIC…"
                 class="w-64 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring" />
        </form>
        <button type="button" data-bs-toggle="modal" data-bs-target="#createContact"
                class="inline-flex items-center rounded-lg px-3 py-2 text-sm bg-blue-600 text-white hover:bg-blue-700">
          + Tambah Kontak
        </button>
      </div>
    </div>

    {{-- Flash & Errors --}}
    @if(session('ok'))
      <div class="mb-3 p-3 rounded bg-green-50 text-green-700 text-sm">{{ session('ok') }}</div>
    @endif
    @if(session('err'))
      <div class="mb-3 p-3 rounded bg-red-50 text-red-700 text-sm">{{ session('err') }}</div>
    @endif
    @if($errors->any())
      <div class="mb-3 p-3 rounded bg-red-50 text-red-700 text-sm">
        <ul class="list-disc pl-5 space-y-1">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-gray-500">
            <th class="text-left font-medium px-4 py-2">PIC</th>
            <th class="text-left font-medium px-4 py-2">Email</th>
            <th class="text-left font-medium px-4 py-2">Telepon/WA</th>
            <th class="text-left font-medium px-4 py-2">Jam Operasional</th>
            <th class="text-left font-medium px-4 py-2">Status</th>
            <th class="text-right font-medium px-4 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($contacts as $c)
            <tr class="{{ $c->is_active ? 'bg-green-50' : '' }}">
              <td class="px-4 py-3">
                <div class="font-medium text-gray-800">{{ $c->narahubung ?: '—' }}</div>
              </td>
              <td class="px-4 py-3 text-gray-800">{{ $c->email ?: '—' }}</td>
              <td class="px-4 py-3">{{ $c->phone ?: '—' }}</td>
              <td class="px-4 py-3">{{ $c->jam_operasional ?: '—' }}</td>
              <td class="px-4 py-3">
                @if($c->is_active)
                  <span class="inline-flex items-center gap-1 rounded-full bg-green-100 text-green-700 px-2 py-0.5">
                    ✅ Aktif
                  </span>
                @else
                  <span class="inline-flex rounded-full bg-gray-100 text-gray-600 px-2 py-0.5">Nonaktif</span>
                @endif
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-2">
                  {{-- Tombol toggle aktif/nonaktif --}}
                  @if($c->is_active)
                    {{-- Tombol Nonaktifkan --}}
                    <form action="{{ route('admin.contact.update', $c) }}" method="POST"
                          onsubmit="return confirm('Nonaktifkan kontak ini?')">
                      @csrf @method('PUT')
                      <input type="hidden" name="is_active" value="0">
                      <button class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">
                        Nonaktifkan
                      </button>
                    </form>
                  @else
                    {{-- Tombol Jadikan Aktif --}}
                    <form action="{{ route('admin.contact.set-active', $c) }}" method="POST">
                      @csrf
                      <button class="px-3 py-1.5 rounded-lg border text-gray-700 hover:bg-gray-50"
                              onclick="return confirm('Aktifkan kontak ini? (Maksimal 3 aktif)')">
                        Jadikan Aktif
                      </button>
                    </form>
                  @endif

                  {{-- Edit --}}
                  <button type="button" data-bs-toggle="modal" data-bs-target="#editContact{{ $c->id }}"
                          class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Edit</button>

                  {{-- Hapus --}}
                  <form action="{{ route('admin.contact.destroy', $c) }}" method="POST"
                        onsubmit="return confirm('Hapus kontak ini?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg border text-red-600 hover:bg-red-50">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>

            {{-- === Edit Modal === --}}
            <div class="modal fade" id="editContact{{ $c->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form action="{{ route('admin.contact.update', $c) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="text-sm">PIC / Narahubung</label>
                        <input name="narahubung" value="{{ old('narahubung',$c->narahubung) }}"
                               class="w-full border rounded-lg px-3 py-2">
                      </div>
                      <div>
                        <label class="text-sm">Email</label>
                        <input type="email" name="email" value="{{ old('email',$c->email) }}"
                               class="w-full border rounded-lg px-3 py-2">
                      </div>
                      <div>
                        <label class="text-sm">Telepon / WA</label>
                        <input name="phone" value="{{ old('phone',$c->phone) }}"
                               class="w-full border rounded-lg px-3 py-2">
                      </div>
                      <div>
                        <label class="text-sm">Jam Operasional</label>
                        <input name="jam_operasional" value="{{ old('jam_operasional',$c->jam_operasional) }}"
                               class="w-full border rounded-lg px-3 py-2" placeholder="Senin–Jumat, 09.00–17.00">
                      </div>
                      <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-2">
                          <input type="checkbox" name="is_active" value="1" {{ old('is_active',$c->is_active) ? 'checked' : '' }}>
                          <span>Aktifkan kontak ini</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Maksimal 3 kontak aktif diperbolehkan.</p>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                      <button class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada kontak.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $contacts->withQueryString()->links() }}</div>
  </div>

  {{-- === Create Modal === --}}
  <div class="modal fade" id="createContact" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Kontak</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('admin.contact.store') }}" method="POST">
          @csrf
          <div class="modal-body grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-sm">PIC / Narahubung</label>
              <input name="narahubung" value="{{ old('narahubung') }}"
                     class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="text-sm">Email</label>
              <input type="email" name="email" value="{{ old('email') }}"
                     class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="text-sm">Telepon / WA</label>
              <input name="phone" value="{{ old('phone') }}"
                     class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="text-sm">Jam Operasional</label>
              <input name="jam_operasional" value="{{ old('jam_operasional') }}"
                     class="w-full border rounded-lg px-3 py-2" placeholder="Senin–Jumat, 09.00–17.00">
            </div>
            <div class="md:col-span-2">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                <span>Jadikan aktif</span>
              </label>
              <p class="text-xs text-gray-500 mt-1">Maksimal 3 kontak aktif diperbolehkan.</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-admin>
