{{-- resources/views/admin/applicant/seleksi/administrasi/index.blade.php --}}
<x-app-admin>
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Seleksi Administrasi</h2>
    @if($batchId)
      <a href="{{ route('admin.applicant.seleksi.index', ['batch'=>$batchId]) }}" class="text-sm text-blue-600">← Kembali ke Rekap</a>
    @endif
  </div>

  <form method="GET" class="mb-4 flex items-center gap-2">
    <input type="hidden" name="batch" value="{{ $batchId }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email/jurusan"
           class="border rounded px-3 py-1">
    <button class="px-3 py-1 border rounded">Filter</button>
  </form>

  <div class="bg-white border rounded">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">Nama</th>
          <th class="px-4 py-2 text-left">Email</th>
          <th class="px-4 py-2 text-left">Jurusan</th>
          <th class="px-4 py-2 text-left">Status</th>
          <th class="px-4 py-2 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($applicants as $a)
          <tr>
            <td class="px-4 py-2">{{ $a->name }}</td>
            <td class="px-4 py-2">{{ $a->email }}</td>
            <td class="px-4 py-2">{{ $a->jurusan }}</td>
            <td class="px-4 py-2">
              <span class="px-2 py-0.5 border rounded text-xs">{{ $a->status }}</span>
            </td>
            <td class="px-4 py-2 text-right">
              <form method="POST" action="{{ route('admin.applicant.seleksi.mark') }}" class="inline">
                @csrf
                <input type="hidden" name="applicant_id" value="{{ $a->id }}">
                <input type="hidden" name="stage" value="Seleksi Administrasi">
                <input type="hidden" name="update_status" value="auto">
                @if($batchId)
                  <input type="hidden" name="_redirect" value="{{ request()->fullUrl() }}">
                @endif

                <button name="result" value="lolos" class="px-3 py-1 rounded bg-green-600 text-white">Lolos</button>
                <button name="result" value="tidak_lolos" class="px-3 py-1 rounded bg-red-600 text-white ml-1">Gagal</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $applicants->links() }}</div>
</x-app-admin>
