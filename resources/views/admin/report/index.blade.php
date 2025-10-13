<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">Report</h2>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.report.index') }}" class="space-y-4 mb-4">
        {{-- 🔹 Baris 1: Filter Batch --}}
        <div class="flex items-center">
            <label class="text-sm font-medium mr-2">Batch:</label>
            <select name="batch" onchange="this.form.submit()" 
                    class="border rounded px-2 py-1 text-sm w-40 cursor-pointer">
            <option value="">Semua</option>
            @foreach($batches as $b)
                <option value="{{ $b->id }}" {{ $batchId == $b->id ? 'selected' : '' }}>
                {{ $b->id }}
                </option>
            @endforeach
            </select>
        </div>

        {{-- 🔹 Baris 2: Search (kiri) + Export (kanan) --}}
        <div class="flex justify-between items-center">
            {{-- 🔍 Search input --}}
            <div class="relative w-1/2">
            <input type="text" name="search" value="{{ $search }}" 
                    placeholder="Cari posisi..."
                    class="border rounded px-4 py-2 pr-10 text-sm w-full focus:ring focus:ring-blue-100 focus:border-blue-400">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                <i class="fa fa-search"></i>
            </button>
            </div>

            {{-- 🟩 Export button --}}
            <a href="{{ route('admin.report.export', request()->query()) }}"
            class="flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-semibold shadow">
            <i class="fa fa-file-export"></i> Export
            </a>
        </div>
    </form>


    {{-- Tabel Report --}}
    <div class="overflow-x-auto">
      <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-100 text-gray-700">
          <tr class="text-left">
            <th class="border px-3 py-2">Batch</th>
            <th class="border px-3 py-2">Posisi</th>
            <th class="border px-3 py-2">Tanggal Dibuat</th>
            <th class="border px-3 py-2 text-center">Pendaftar</th>
            <th class="border px-3 py-2 text-center">Lolos Administrasi</th>
            <th class="border px-3 py-2 text-center">Lolos Tes Tulis</th>
            <th class="border px-3 py-2 text-center">Lolos Technical Test</th>
            <th class="border px-3 py-2 text-center">Lolos Interview</th>
          </tr>
        </thead>
        <tbody>
          @forelse($positions as $p)
            @php
              $total = max(1, $p->total_pendaftar); // hindari div by zero
              $percent = fn($val) => number_format(($val / $total) * 100, 1) . '%';
            @endphp
            <tr class="hover:bg-gray-50">
              <td class="border px-3 py-2">{{ $p->batch_id }}</td>
              <td class="border px-3 py-2">{{ $p->name }}</td>
              <td class="border px-3 py-2">{{ $p->created_at->format('d F Y, H:i') }}</td>
              <td class="border px-3 py-2 text-center font-medium">{{ $p->total_pendaftar }}</td>
              <td class="border px-3 py-2 text-center">
                {{ $p->lolos_administrasi }} <span class="text-xs text-gray-500">({{ $percent($p->lolos_administrasi) }})</span>
              </td>
              <td class="border px-3 py-2 text-center">
                {{ $p->lolos_tes_tulis }} <span class="text-xs text-gray-500">({{ $percent($p->lolos_tes_tulis) }})</span>
              </td>
              <td class="border px-3 py-2 text-center">
                {{ $p->lolos_technical }} <span class="text-xs text-gray-500">({{ $percent($p->lolos_technical) }})</span>
              </td>
              <td class="border px-3 py-2 text-center">
                {{ $p->lolos_interview }} <span class="text-xs text-gray-500">({{ $percent($p->lolos_interview) }})</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-3 text-gray-500">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-app-admin>
