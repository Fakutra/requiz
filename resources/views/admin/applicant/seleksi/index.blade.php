<x-app-admin>
  {{-- Header bar --}}
  <div class="bg-white rounded-lg shadow-sm border p-4 mb-5">
    <div class="grid grid-cols-3 items-center">
      <div class="flex items-center gap-2">
        <span class="text-sm font-semibold text-gray-700 tracking-wide">BATCH :</span>
        <select id="batchSelect" class="h-8 text-sm border rounded px-2 w-24">
          @if(($batches ?? collect())->isEmpty())
            <option value="">-</option>
          @else
            @foreach ($batches as $b)
              @php $label = is_numeric($b->name ?? null) ? $b->name : ($loop->iteration); @endphp
              <option value="{{ $b->id }}" {{ (string)$currentBatchId === (string)$b->id ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          @endif
        </select>
      </div>

      <div class="text-center">
        <h2 class="text-sm md:text-base font-semibold text-gray-700">REKAP SELEKSI</h2>
      </div>

      <div class="text-right text-sm text-gray-700">
        <span>Jumlah Pelamar : </span>
        <strong>{{ $totalApplicants ?? 0 }}</strong>
      </div>
    </div>
  </div>

  {{-- Flash + validation --}}
  @if (session('success'))
    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
      <strong>Gagal:</strong>
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Tabel rekap dengan kedua metrik --}}
  <div class="bg-white rounded-lg shadow-sm border">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <tbody class="divide-y">
          @forelse ($rekap as $row)
            <tr class="hover:bg-gray-50">
              {{-- Tahap --}}
              <td class="px-6 py-4 w-[28%] text-gray-700">{{ $row['label'] }}</td>

              {{-- Jumlah Peserta: expected + processed --}}
              <td class="px-6 py-4 w-[28%]">
                <div class="text-slate-700">
                  Jumlah Peserta : <strong>{{ $row['participants_expected'] }}</strong>
                </div>
                <div class="text-xs text-slate-400 mt-1">
                  Processed: <strong>{{ $row['participants_processed'] }}</strong>
                </div>
              </td>

              {{-- Jumlah Lolos --}}
              <td class="px-6 py-4 w-[22%]">
                <span class="text-green-600">Jumlah Peserta Lolos: {{ $row['lolos'] }}</span>
              </td>

              {{-- Aksi (kanan) --}}
              <td class="px-6 py-4 w-[16%] text-right">
                @if($rn)
                  <a href="{{ route($rn, ['batch' => $currentBatchId]) }}"
                    class="inline-flex items-center gap-2 rounded bg-blue-800 text-white px-4 py-1.5 hover:bg-blue-700 transition">          
              {{-- Jumlah Gagal --}}
              <td class="px-6 py-4 w-[12%]">
                <span class="text-red-600">Jumlah Peserta Gagal: {{ $row['gagal'] }}</span>
              </td>

              {{-- Aksi --}}
              <td class="px-6 py-4 w-[10%] text-right">
                @if(!empty($row['route_name']))
                  <a href="{{ route($row['route_name'], ['batch' => $currentBatchId]) }}"
                    class="inline-flex items-center gap-2 rounded bg-blue-700 text-white px-4 py-1.5 hover:bg-blue-800 transition">
                    Proses
                  </a>
                @else
                  <span class="text-gray-400">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                Belum ada data untuk batch ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Auto-redirect saat batch berubah --}}
  <script>
    (function () {
      const sel = document.getElementById('batchSelect');
      if (!sel) return;
      sel.addEventListener('change', function () {
        const base = @json(route('admin.applicant.seleksi.index'));
        const val  = this.value;
        const url  = val ? `${base}?batch=${encodeURIComponent(val)}` : base;
        window.location.href = url;
      });
    })();
  </script>
</x-app-admin>
