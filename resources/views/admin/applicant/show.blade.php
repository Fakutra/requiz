<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Detail Pelamar') }}
      </h2>
  </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-3 col-lg-8">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $applicant->name }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $applicant->email }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control" id="nik" name="nik" inputmode="numeric" value="{{ $applicant->nik }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="no_telp" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="no_telp" name="no_telp" inputmode="numeric"  value="{{ $applicant->no_telp }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="tpt_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tpt_lahir" name="tpt_lahir" value="{{ $applicant->tpt_lahir }}" readonly>
                    </div>
                    <div class="mb-3 col-sm-4">
                        <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="text" class="form-control" id="tgl_lahir" name="tgl_lahir" value="{{ $applicant->tgl_lahir }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea type="text" rows="3" cols="30" class="form-control" id="alamat" name="alamat" value="" readonly>{{ $applicant->alamat }}</textarea>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="pendidikan" class="form-label">Pendidikan</label>
                        <input type="text" class="form-control" id="pendidikan" name="pendidikan" value="{{ $applicant->pendidikan }}">
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="universitas" class="form-label">Universitas</label>
                        <input type="text" class="form-control" id="universitas" name="universitas" value="{{ $applicant->universitas }}">
                    </div>
                    <div class="mb-3 col-lg-6">
                        <label for="cv" class="form-label">CV</label>
                        @if($applicant->cv)
                            <embed src="{{ asset('storage/' . $applicant->cv) }}" type="application/pdf" width="100%" height="600px" />
                        @else
                            <p>CV belum diupload.</p>
                        @endif
                    </div>
                    <div class="mb-3 col-lg-6">
                        <label for="doc_tambahan" class="form-label">Dokumen Tambahan (Sertifikat, dll..)</label>
                        @if($applicant->doc_tambahan)
                            <embed src="{{ asset('storage/' . $applicant->doc_tambahan) }}" type="application/pdf" width="100%" height="600px" />
                        @else
                            <p>Dokumen Tambahan belum diupload.</p>
                        @endif
                    </div>     
                        <form action="{{ route('applicant.updateStatusLolos', $applicant->id) }}" method="POST" onsubmit="return confirm('Yakin ingin meloloskan pelamar ini?');">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                Lolos Seleksi Administrasi
                            </button>
                        </form>
                        {{-- <form action="{{ route('applicant.updateStatusTidakLolos', $applicant->id) }}" method="POST" onsubmit="return confirm('Yakin ingin tidak meloloskan pelamar ini?');">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                Tidak Lolos Seleksi Administrasi
                            </button>
                        </form> --}}
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>
