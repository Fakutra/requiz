<x-app-admin>
  <div x-data="faqPage" class="bg-white rounded-lg shadow p-6 space-y-6">

    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold text-gray-800">Frequently Asked Questions (FAQ)</h2>
      <button @click="openCreate()" class="bg-blue-600 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
        Tambah FAQ
      </button>
    </div>

    <table class="w-full border text-sm rounded-lg overflow-hidden">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
            <th class="px-4 py-2 text-left whitespace-nowrap">No.</th>
            <th class="px-3 py-2 border text-left w-1/3">Pertanyaan</th>
            <th class="px-3 py-2 border text-left">Jawaban</th>
            <th class="px-3 py-2 border text-center w-[120px]">Tampil</th>
            <th class="px-3 py-2 border text-center w-[160px]">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($faqs as $faq)
          <tr>
            <td class="px-4 py-2 border w-auto">{{ ($faqs->currentPage()-1)*$faqs->perPage() + $loop->iteration }}</td>
            <td class="px-3 py-2 border align-top font-medium">{{ $faq->question }}</td>
            <td class="px-3 py-2 border text-gray-700 align-top">{{ $faq->answer }}</td>

            {{-- ✅ Kolom Status Aktif/Tidak --}}
            <td class="px-3 py-2 border text-center align-top">
                @if ($faq->is_active)
                    <i class="fas fa-check text-green-600"></i>
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </td>

            <td class="px-3 py-2 border text-center align-top">
              <div class="flex items-center justify-center gap-2">
                <button
                    type="button"
                    x-on:click="openEdit(@js($faq->only(['id','question','answer','is_active'])))"
                    class="text-yellow-600 hover:text-yellow-800"
                    title="Edit">
                    <i class="fas fa-edit"></i>
                </button>

                <form method="POST" action="{{ route('admin.faq.destroy', $faq) }}"
                      onsubmit="return confirm('Yakin ingin menghapus FAQ ini?')">
                  @csrf @method('DELETE')
                  <button class="text-red-600 hover:text-red-800" title="Hapus">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center p-3 text-gray-500">Belum ada data FAQ</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Modal Tambah --}}
    <div x-show="showCreate" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
      <div @click.outside="showCreate=false" class="bg-white w-full max-w-2xl rounded-lg shadow p-5">
        <h3 class="font-semibold text-lg mb-4">Tambah FAQ</h3>
        <form method="POST" action="{{ route('admin.faq.store') }}" class="space-y-3">
          @csrf
          <div>
            <label class="block text-sm mb-1">Pertanyaan <span class="text-red-500">*</span></label>
            <input type="text" name="question" x-model="createForm.question" class="w-full border rounded px-3 py-2 text-sm" required>
          </div>
          <div>
            <label class="block text-sm mb-1">Jawaban <span class="text-red-500">*</span></label>
            <textarea name="answer" rows="4" x-model="createForm.answer" class="w-full border rounded px-3 py-2 text-sm" required></textarea>
          </div>

          {{-- ✅ Checkbox aktif --}}
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" checked>
            Aktifkan
          </label>

          <div class="pt-2 flex justify-end gap-2">
            <button type="button" @click="showCreate=false" class="px-4 py-2 border rounded text-sm">Batal</button>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal Edit --}}
    <div x-show="showEdit" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
      <div @click.outside="showEdit=false" class="bg-white w-full max-w-2xl rounded-lg shadow p-5">
        <h3 class="font-semibold text-lg mb-4">Edit FAQ</h3>
        <form method="POST" :action="editAction" class="space-y-3">
          @csrf @method('PUT')
          <div>
            <label class="block text-sm mb-1">Pertanyaan <span class="text-red-500">*</span></label>
            <input type="text" name="question" x-model="editForm.question" class="w-full border rounded px-3 py-2 text-sm" required>
          </div>
          <div>
            <label class="block text-sm mb-1">Jawaban <span class="text-red-500">*</span></label>
            <textarea name="answer" rows="4" x-model="editForm.answer" class="w-full border rounded px-3 py-2 text-sm" required></textarea>
          </div>

          {{-- ✅ Checkbox aktif --}}
          <label class="inline-flex items-center gap-2 text-sm">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                x-bind:checked="editForm.is_active === true || editForm.is_active === 1 || editForm.is_active === '1'"
                class="mr-2"
                />
            Aktifkan
          </label>

          <div class="pt-2 flex justify-end gap-2">
            <button type="button" @click="showEdit=false" class="px-4 py-2 border rounded text-sm">Batal</button>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">Simpan</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</x-app-admin>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('faqPage', () => ({
    showEdit: false,
    editAction: '',
    editForm: { id: null, question: '', answer: '', is_active: true },

    openEdit(faq) {
      // pastikan nilai boolean:
      const isActive =
        faq.is_active === true || faq.is_active === 1 || faq.is_active === '1';

      this.editForm = {
        id: faq.id,
        question: faq.question ?? '',
        answer: faq.answer ?? '',
        is_active: isActive,
      };

      // set action form
      this.editAction = "{{ route('admin.faq.update', ':id') }}".replace(':id', faq.id);

      this.showEdit = true;
    },
  }));
});
</script>