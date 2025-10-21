<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-5">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">Personality Rules</h2>
      <button onclick="document.getElementById('addModal').classList.remove('hidden')"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
        + Tambah Rule
      </button>
    </div>

    {{-- @if(session('success'))
      <div class="mb-3 p-2 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif --}}

    <div class="overflow-x-auto">
      <table class="w-full border text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 border">Min (%)</th>
            <th class="px-3 py-2 border">Max (%)</th>
            <th class="px-3 py-2 border">Nilai</th>
            <th class="px-3 py-2 border text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($rules as $rule)
            <tr>
              <td class="px-3 py-2 border">{{ $rule->min_percentage }}</td>
              <td class="px-3 py-2 border">{{ $rule->max_percentage ?? '∞' }}</td>
              <td class="px-3 py-2 border font-semibold">{{ $rule->score_value }}</td>
              <td class="px-3 py-2 border text-center flex justify-center gap-2">

                {{-- Edit --}}
                <i class="fas fa-edit cursor-pointer text-yellow-600 hover:text-yellow-800"
                  onclick="openEditModal({{ $rule->id }}, {{ $rule->min_percentage }}, {{ $rule->max_percentage ?? 'null' }}, {{ $rule->score_value }})">
                </i>

                {{-- Hapus --}}
                <form method="POST" action="{{ route('admin.personality-rules.destroy', $rule->id) }}"
                  onsubmit="return confirm('Yakin ingin menghapus rule ini?')">
                  @csrf @method('DELETE')
                  <button class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>

              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center p-3 text-gray-500">Belum ada data</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- MODAL: ADD RULE --}}
  <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-5">
      <h3 class="text-lg font-semibold mb-3">Tambah Rule</h3>

      <form method="POST" action="{{ route('admin.personality-rules.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="text-sm">Min Percentage</label>
          <input type="number" step="0.01" name="min_percentage" class="w-full border rounded px-2 py-1" required>
        </div>
        <div>
          <label class="text-sm">Max Percentage (optional)</label>
          <input type="number" step="0.01" name="max_percentage" class="w-full border rounded px-2 py-1">
        </div>
        <div>
          <label class="text-sm">Nilai</label>
          <input type="number" name="score_value" class="w-full border rounded px-2 py-1" required>
        </div>

        <div class="flex justify-end gap-2 mt-2">
          <button type="button"
            onclick="document.getElementById('addModal').classList.add('hidden')"
            class="px-3 py-1 border rounded">Batal</button>

          <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL: EDIT RULE --}}
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-5">
      <h3 class="text-lg font-semibold mb-3">Edit Rule</h3>

      <form method="POST" id="editForm" class="space-y-3">
        @csrf
        <div>
          <label class="text-sm">Min Percentage</label>
          <input id="editMin" type="number" step="0.01" name="min_percentage" class="w-full border rounded px-2 py-1" required>
        </div>
        <div>
          <label class="text-sm">Max Percentage (optional)</label>
          <input id="editMax" type="number" step="0.01" name="max_percentage" class="w-full border rounded px-2 py-1">
        </div>
        <div>
          <label class="text-sm">Nilai</label>
          <input id="editScore" type="number" name="score_value" class="w-full border rounded px-2 py-1" required>
        </div>

        <div class="flex justify-end gap-2 mt-2">
          <button type="button"
            onclick="document.getElementById('editModal').classList.add('hidden')"
            class="px-3 py-1 border rounded">Batal</button>

          <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Update</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openEditModal(id, minP, maxP, score) {
      document.getElementById('editMin').value = minP;
      document.getElementById('editMax').value = (maxP === null ? '' : maxP);
      document.getElementById('editScore').value = score;

      document.getElementById('editForm').action = "/admin/personality-rules/" + id;
      document.getElementById('editModal').classList.remove('hidden');
    }
  </script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</x-app-admin>
