<x-app-admin>
  <div class="bg-white rounded-lg shadow p-4">
    <h2 class="text-lg font-semibold mb-4">Log Aktivitas</h2>

    {{-- 🔍 Filter Tanggal --}}
    <form method="GET" action="{{ route('admin.logs.index') }}" class="mb-4 flex items-center gap-3">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Pilih Tanggal</label>
        <input type="date" name="date" value="{{ $date }}" class="border rounded px-3 py-1.5">
      </div>
      <button type="submit"
              class="mt-6 px-4 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700">
        Cari
      </button>
    </form>

    @if(!$date)
      <p class="text-gray-500 italic mb-4">Silakan pilih tanggal terlebih dahulu untuk melihat log aktivitas.</p>
    @endif

    @if($logs->isNotEmpty())
      <table class="min-w-full text-sm border">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 border">Waktu</th>
            <th class="px-3 py-2 border">User</th>
            <th class="px-3 py-2 border">Aksi</th>
            <th class="px-3 py-2 border">Modul</th>
            <th class="px-3 py-2 border">Deskripsi</th>
            {{-- <th class="px-3 py-2 border">Target</th> --}}
            {{-- <th class="px-3 py-2 border">IP</th> --}}
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
            <tr class="hover:bg-gray-50">
              <td class="px-3 py-2 border">{{ $log->created_at->format('d M Y H:i:s') }}</td>
              <td class="px-3 py-2 border">{{ $log->user->name ?? 'System' }}</td>
              <td class="px-3 py-2 border">{{ ucfirst($log->action) }}</td>
              <td class="px-3 py-2 border">{{ $log->module }}</td>
              <td class="px-3 py-2 border">{{ $log->description }}</td>
              {{-- <td class="px-3 py-2 border">{{ $log->target }}</td> --}}
              {{-- <td class="px-3 py-2 border">{{ $log->ip_address }}</td> --}}
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-3">{{ $logs->links() }}</div>
    @elseif($date)
      <p class="text-gray-500 text-center py-4">Tidak ada log aktivitas pada tanggal ini.</p>
    @endif
  </div>
</x-app-admin>
