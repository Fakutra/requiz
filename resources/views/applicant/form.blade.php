@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-6">
    <h1 class="text-2xl mb-4 font-semibold">{{ $applicant ? 'Edit Biodata' : 'Isi Biodata' }}</h1>

    @if(session('error'))
        <div class="mb-4 text-red-600">{{ session('error') }}</div>
    @endif

    <form action="{{ route('applicant.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="nik" class="block mb-1 font-medium">NIK</label>
            <input type="text" id="nik" name="nik" value="{{ old('nik', $applicant->nik ?? '') }}" required
                class="w-full border rounded px-3 py-2" />
            @error('nik') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="no_telp" class="block mb-1 font-medium">No. Telepon</label>
            <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp', $applicant->no_telp ?? '') }}" required
                class="w-full border rounded px-3 py-2" />
            @error('no_telp') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="tpt_lahir" class="block mb-1 font-medium">Tempat Lahir</label>
            <input type="text" id="tpt_lahir" name="tpt_lahir" value="{{ old('tpt_lahir', $applicant->tpt_lahir ?? '') }}" required
                class="w-full border rounded px-3 py-2" />
            @error('tpt_lahir') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="tgl_lahir" class="block mb-1 font-medium">Tanggal Lahir</label>
            <input type="date" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', $applicant->tgl_lahir ?? '') }}" required
                class="w-full border rounded px-3 py-2" />
            @error('tgl_lahir') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-
