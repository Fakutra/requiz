{{-- resources/views/admin/applicant/seleksi/index.blade.php --}}
<x-app-admin>
  {{-- Header bar ala figma --}}
  <div class="bg-white rounded-lg shadow-sm border p-4">
    <div class="grid grid-cols-3 items-center">
      {{-- Kiri: BATCH + dropdown kecil --}}
      <div class="flex items-center gap-2">
        <span class="text-sm font-semibold text-gray-700 tracking-wide">BATCH :</span>
        <select id="batchSelect" class="h-8 text-sm border rounded px-2 w-20">
          @php $hasBatch = !empty($currentBatchId); @endphp
          @if(($batches ?? collect())->isEmpty())
            <option value="">-</option>
          @else
            @foreach ($batches as $b)
              {{-- Tampilkan angka (mis. 1, 2, 3) namun value tetap id batch --}}
              @php
                $label = is_numeric($b->name ?? null) ? $b->name : ($loop->iteration);
              @endphp
              <option value="{{ $b->id }}" {{ (string)$currentBatchId === (string)$b->id ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          @endif
        </select>
      </div>

      {{-- Tengah: Judul --}}
      <div class="text-center">
        <h2 class="text-sm md:text-base font-semibold text-gray-700">UPDATE SELEKSI TAD</h2>
      </div>

      {{-- Kanan: Jumlah pelamar --}}
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

  {{-- Tabel rekap tahap --}}
  <div class="bg-white rounded-lg shadow-sm border">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <tbody class="divide-y">
          @php
            $map = [
              'Seleksi Administrasi' => 'admin.applicant.seleksi.administrasi',
              'Tes Tulis'            => 'admin.applicant.seleksi.tes_tulis',
              'Technical Test'       => 'admin.applicant.seleksi.technical_test',
              'Interview'            => 'admin.applicant.seleksi.interview',
              'Offering'             => 'admin.applicant.seleksi.offering',
            ];
          @endphp

          @forelse ($rekap as $row)
            @php
              $label = $row['label'] ?? '-';
              $lolos = (int)($row['lolos'] ?? 0);
              $gagal = (int)($row['gagal'] ?? 0);
              $rn    = $row['route_name'] ?? ($map[$label] ?? null);
            @endphp
            <tr class="hover:bg-gray-50">
              {{-- Tahap (kiri) --}}
              <td class="px-6 py-4 w-[28%] text-gray-700">{{ $label }}</td>

              {{-- Jumlah Lolos (tengah kiri) --}}
              <td class="px-6 py-4 w-[28%]">
                <span class="text-green-600">Peserta Lolos: {{ $lolos }}</span>
              </td>

              {{-- Jumlah Gagal (tengah kanan) --}}
              <td class="px-6 py-4 w-[28%]">
                <span class="text-red-600">Peserta Gagal: {{ $gagal }}</span>
              </td>

              {{-- Aksi (kanan) --}}
              <td class="px-6 py-4 w-[16%] text-right">
                @if($rn)
                  <a href="{{ route($rn, ['batch' => $currentBatchId]) }}"
                    class="inline-flex items-center gap-2 rounded bg-blue-800 text-white px-4 py-1.5 hover:bg-blue-700 transition">          
                    Proses
                  </a>
                @else
                  <span class="text-gray-400">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-6 py-10 text-center text-gray-500">
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
