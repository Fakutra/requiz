<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-4 mb-5" x-data="skApp()">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">Syarat & Ketentuan Register</h2>
      <button @click="openCreate"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold shadow flex items-center gap-2">
        <i class="fa fa-plus"></i> Tambah
      </button>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
      <table class="w-full text-sm border-collapse">
        {{-- HEAD --}}
        <thead class="bg-gray-100 text-gray-700">
          <tr class="text-left">
            <th class="border px-3 py-2">No</th>
            <th class="border px-3 py-2">Tipe</th>
            <th class="border px-3 py-2">Judul</th>
            <th class="border px-3 py-2">Deskripsi</th>
            <th class="border px-3 py-2 text-center">Aksi</th>
          </tr>
        </thead>

        {{-- BODY --}}
        <tbody>
          <template x-for="(item, index) in items" :key="item.id">
            <tr class="hover:bg-gray-50">
              <td class="border px-3 py-2" x-text="index + 1"></td>
              <td class="border px-3 py-2 capitalize font-medium" x-text="item.content"></td>
              <td class="border px-3 py-2" x-text="item.title ?? '-'"></td>
              <td class="border px-3 py-2 text-xs"
                  x-text="item.description.length > 60 ? item.description.substring(0,60) + '...' : item.description">
              </td>
              <td class="border px-3 py-2 text-center space-x-2">
                <button @click="openEdit(item)" class="text-blue-600 hover:text-blue-800">
                  <i class="fa fa-pencil"></i>
                </button>
                <button @click="remove(item)" class="text-red-600 hover:text-red-800">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
          </template>

          <tr x-show="items.length === 0">
            <td colspan="5" class="text-center py-4 text-gray-400">Belum ada data SK</td>
          </tr>
        </tbody>
      </table>
    </div>


    {{-- MODAL ADD / EDIT --}}
    <div x-show="showModal" x-cloak
      class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">

      <div @click.away="closeModal"
        class="bg-white w-full max-w-lg rounded-lg shadow p-5 space-y-4">

        <h3 class="text-md font-semibold" x-text="modalTitle"></h3>

        <form @submit.prevent="submit">

          {{-- TIPE --}}
          <div class="mb-3">
            <label class="text-sm font-medium">Tipe</label>
            <select x-model="form.content" class="border rounded px-3 py-2 w-full text-sm">
              <option value="">-- Pilih --</option>
              <option value="judul">Judul</option>
              <option value="list">List</option>
            </select>
            <p x-text="errors.content" class="text-red-500 text-xs mt-1"></p>
          </div>

          {{-- TITLE (muncul jika list) --}}
          <div class="mb-3" x-show="form.content === 'list'">
            <label class="text-sm font-medium">Judul Poin</label>
            <input type="text" x-model="form.title" class="border rounded px-3 py-2 w-full text-sm">
            <p x-text="errors.title" class="text-red-500 text-xs mt-1"></p>
          </div>

          {{-- DESKRIPSI --}}
          <div class="mb-3" x-show="form.content">
            <label class="text-sm font-medium">Deskripsi</label>
            <textarea x-model="form.description" class="border rounded px-3 py-2 w-full text-sm" rows="4"></textarea>
            <p x-text="errors.description" class="text-red-500 text-xs mt-1"></p>
          </div>

          {{-- FOOTER BUTTON --}}
          <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeModal" class="text-sm border px-3 py-1 rounded">Batal</button>
            <button type="submit" class="text-sm bg-blue-600 text-white px-3 py-1 rounded" x-text="submitText"></button>
          </div>

        </form>
      </div>
    </div>

  </div>


  {{-- SCRIPT --}}
  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function skApp() {
      return {
        items: @json($items),
        showModal: false,
        modalTitle: '',
        submitText: '',
        errors: {},

        form: { id: '', content: '', title: '', description: '' },

        openCreate() {
          this.reset()
          this.modalTitle = 'Tambah SK'
          this.submitText = 'Simpan'
          this.showModal = true
        },

        openEdit(item) {
          this.reset()
          this.modalTitle = 'Edit SK'
          this.submitText = 'Update'
          this.form = { id: item.id, content: item.content, title: item.title, description: item.description }
          this.showModal = true
        },

        closeModal() {
          this.showModal = false
        },

        reset() {
          this.form = { id: '', content: '', title: '', description: '' }
          this.errors = {}
        },

        async submit() {
          this.errors = {}
          const isEdit = !!this.form.id
          const url = isEdit ? `/admin/skregis/${this.form.id}` : '/admin/skregis'
          const method = isEdit ? 'PUT' : 'POST'

          const res = await fetch(url, {
            method,
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(this.form)
          })

          const result = await res.json()

          if (!res.ok) {
            if (res.status === 422) {
              for (const key in result.errors) {
                this.errors[key] = result.errors[key][0]
              }
            }
            return
          }

          if (isEdit) {
            this.items = this.items.map(i => i.id === result.data.id ? result.data : i)
          } else {
            this.items.push(result.data)
          }

          this.closeModal()

          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: result.message,
            showConfirmButton: false,
            timer: 1500
          });
        },

        async remove(item) {
          const confirmDelete = await Swal.fire({
            title: 'Yakin nih?',
            text: "Data ini bakal kehapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yap, hapus',
            cancelButtonText: 'Batal'
          });

          if (!confirmDelete.isConfirmed) return;

          const res = await fetch(`/admin/skregis/${item.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
          })

          const result = await res.json()
          if (!res.ok) return

          this.items = this.items.filter(i => i.id !== item.id)

          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: result.message,
            showConfirmButton: false,
            timer: 1500
          });
        }
      }
    }
  </script>
  @endpush

</x-app-admin>
