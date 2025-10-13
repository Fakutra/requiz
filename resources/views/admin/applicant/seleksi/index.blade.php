<x-app-admin>
  {{-- Header --}}
  <div class="bg-white rounded-lg shadow-sm border p-4 mb-5">
    <div class="grid grid-cols-3 items-center">
      <div class="flex items-center gap-2">
        <span class="text-sm font-semibold text-gray-700 tracking-wide">BATCH :</span>
        <select id="batchSelect" class="h-8 text-sm border rounded px-2 w-24">
          @foreach ($batches as $b)
            @php $label = is_numeric($b->name ?? null) ? $b->name : ($loop->iteration); @endphp
            <option value="{{ $b->id }}" {{ (string)$currentBatchId === (string)$b->id ? 'selected' : '' }}>
              {{ $label }}
            </option>
          @endforeach
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

  {{-- Tabel --}}
  <div class="bg-white rounded-lg shadow-sm border">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <tbody class="divide-y">
          
          {{-- Seleksi Administrasi --}}
          <tr>
            <td class="px-6 py-4">Seleksi Administrasi</td>
            <td class="px-6 py-4 text-[#0026E5]">Jumlah Peserta: <strong>{{ $administrasi['expected'] }}</strong></td>
            <td class="px-6 py-4 text-[#01D93B]">Lolos: {{ $administrasi['lolos'] }}</td>
            <td class="px-6 py-4 text-[#EB0000]">Gagal: {{ $administrasi['gagal'] }}</td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('admin.applicant.seleksi.administrasi.index',['batch'=>$currentBatchId]) }}" 
                 class="bg-blue-600 text-white px-4 py-1 rounded">Proses</a>
            </td>
          </tr>

          {{-- Tes Tulis --}}
          <tr>
            <td class="px-6 py-4">Tes Tulis</td>
            <td class="px-6 py-4 text-[#0026E5]">Jumlah Peserta: <strong>{{ $tesTulis['expected'] }}</strong></td>
            <td class="px-6 py-4 text-[#01D93B]">Lolos: {{ $tesTulis['lolos'] }}</td>
            <td class="px-6 py-4 text-[#EB0000]">Gagal: {{ $tesTulis['gagal'] }}</td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('admin.applicant.seleksi.tes_tulis.index',['batch'=>$currentBatchId]) }}" 
                 class="bg-blue-600 text-white px-4 py-1 rounded">Proses</a>
            </td>
          </tr>

          {{-- Technical Test --}}
          <tr>
            <td class="px-6 py-4">Technical Test</td>
            <td class="px-6 py-4 text-[#0026E5]">Jumlah Peserta: <strong>{{ $technical['expected'] }}</strong></td>
            <td class="px-6 py-4 text-[#01D93B]">Lolos: {{ $technical['lolos'] }}</td>
            <td class="px-6 py-4 text-[#EB0000]">Gagal: {{ $technical['gagal'] }}</td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('admin.applicant.seleksi.technical_test.index',['batch'=>$currentBatchId]) }}" 
                 class="bg-blue-600 text-white px-4 py-1 rounded">Proses</a>
            </td>
          </tr>

          {{-- Interview --}}
          <tr>
            <td class="px-6 py-4">Interview</td>
            <td class="px-6 py-4 text-[#0026E5]">Jumlah Peserta: <strong>{{ $interview['expected'] }}</strong></td>
            <td class="px-6 py-4 text-[#01D93B]">Lolos: {{ $interview['lolos'] }}</td>
            <td class="px-6 py-4 text-[#EB0000]">Gagal: {{ $interview['gagal'] }}</td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('admin.applicant.seleksi.interview.index',['batch'=>$currentBatchId]) }}" 
                 class="bg-blue-600 text-white px-4 py-1 rounded">Proses</a>
            </td>
          </tr>

          {{-- Offering --}}
          <tr>
            <td class="px-6 py-4">Offering</td>
            <td class="px-6 py-4 text-[#0026E5]">Jumlah Peserta: <strong>{{ $offering['expected'] }}</strong></td>
            <td class="px-6 py-4 text-[#01D93B]">Lolos: {{ $offering['lolos'] }}</td>
            <td class="px-6 py-4 text-[#EB0000]">Gagal: {{ $offering['gagal'] }}</td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('admin.applicant.seleksi.offering.index',['batch'=>$currentBatchId]) }}" 
                 class="bg-blue-600 text-white px-4 py-1 rounded">Proses</a>
            </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>

  {{-- Auto redirect batch --}}
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
