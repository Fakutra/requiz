<x-app-layout>
  <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Detail Tawaran Kerja</h1>

    <p><strong>Posisi:</strong> {{ $offering->position }}</p>
    <p><strong>Divisi:</strong> {{ $offering->division }}</p>
    <p><strong>Jabatan:</strong> {{ $offering->jabatan }}</p>
    <p><strong>Penempatan:</strong> {{ $offering->penempatan }}</p>
    <p><strong>Gaji:</strong> Rp {{ number_format($offering->gaji,0,',','.') }}</p>

    <h2 class="mt-4 font-semibold">Lengkapi Berkas:</h2>
    <ul class="list-disc ml-6">
      @if($offering->link_pkwt)
        <li><a href="{{ $offering->link_pkwt }}" target="_blank" class="text-blue-600 underline">Form PKWT</a></li>
      @endif
      @if($offering->link_berkas)
        <li><a href="{{ $offering->link_berkas }}" target="_blank" class="text-blue-600 underline">Kelengkapan Berkas</a></li>
      @endif
      @if($offering->link_form_pelamar)
        <li><a href="{{ $offering->link_form_pelamar }}" target="_blank" class="text-blue-600 underline">Form Pelamar</a></li>
      @endif
    </ul>

    <form method="POST" action="{{ route('offering.response',$offering->token) }}" class="mt-6 flex gap-4">
      @csrf
      <button type="submit" name="action" value="accept" class="px-4 py-2 bg-green-600 text-white rounded">Saya Menerima & Sudah Mengisi Berkas</button>
      <button type="submit" name="action" value="decline" class="px-4 py-2 bg-red-600 text-white rounded">Saya Menolak Tawaran</button>
    </form>
  </div>
</x-app-layout>
