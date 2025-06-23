<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Apply') }}
      </h2>
      @if (session()->has('success'))
        <div class="alert alert-success col-lg-8" role="alert">
          {{ session('success') }}
        </div>
      @endif
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">
                <form method="post" action="{{ route('apply.store', $positions) }}" class="mb-5" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 col-lg-8">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required autofocus value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" inputmode="numeric" required value="{{ old('nik') }}">
                        @error('nik')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="no_telp" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" inputmode="numeric" required value="{{ old('no_telp') }}">
                        @error('no_telp')
                        <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="tpt_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control @error('tpt_lahir') is-invalid @enderror" id="tpt_lahir" name="tpt_lahir" required value="{{ old('tpt_lahir') }}">
                        @error('tpt_lahir')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-sm-4">
                        <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control @error('tgl_lahir') is-invalid @enderror" id="tgl_lahir" name="tgl_lahir" required value="{{ old('tgl_lahir') }}">
                        @error('tgl_lahir')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea type="text" rows="3" cols="30" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" required value="{{ old('alamat') }}"></textarea>
                        @error('alamat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="pendidikan" class="form-label">Pendidikan</label>
                        <select class="form-select" name="pendidikan">
                          <option selected>--- Pilih ---</option>
                          <option value="SMA/Sederajat/">SMA/Sederajat</option>
                          <option value="Diploma">Diploma</option>
                          <option value="S1">S1</option>
                          <option value="S2">S2</option>
                          <option value="S3">S3</option>
                        </select>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="universitas" class="form-label">Universitas</label>
                        <input type="text" class="form-control @error('universitas') is-invalid @enderror" id="universitas" name="universitas" required value="{{ old('universitas') }}">
                        @error('universitas')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-6">
                        <label for="cv" class="form-label">CV</label>
                        <input type="file" rows="3" cols="30" class="form-control @error('cv') is-invalid @enderror" id="cv" name="cv"></input>
                        @error('cv')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-6">
                        <label for="doc_tambahan" class="form-label">Dokumen Tambahan (Sertifikat, dll..)</label>
                        <input type="file" rows="3" cols="30" class="form-control @error('doc_tambahan') is-invalid @enderror" id="doc_tambahan" name="doc_tambahan"></input>
                        @error('doc_tambahan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </form>
              </div>
          </div>
      </div>
  </div>
</x-app-layout>
