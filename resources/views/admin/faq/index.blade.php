<x-app-admin>
    <div class="flex justify-between items-center mb-5">
      <h2 class="text-xl font-semibold text-gray-800">FAQ</h2>

      <div class="overflow-x-auto">
        <table class="w-full border text-sm rounded-lg overflow-hidden">
          <thead class="bg-gray-100 text-gray-700">
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
                <td class="px-3 py-2 border text-center flex justify-center gap-3">
                  {{-- Edit --}}
                  <i class="fas fa-edit cursor-pointer text-yellow-600 hover:text-yellow-800"
                    onclick="openEditModal({{ $rule->id }}, {{ $rule->min_percentage }}, {{ $rule->max_percentage ?? 'null' }}, {{ $rule->score_value }})"></i>

                  {{-- Delete --}}
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
    @endif
  </div>
</x-app-admin>