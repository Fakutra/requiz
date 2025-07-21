@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1 class="text-3xl font-bold mb-4">Selamat datang, {{ auth()->user()->name }}!</h1>

    @if(auth()->user()->applicant)
        <p>Biodata sudah lengkap.</p>
        <a href="{{ route('lowongan.index') }}" class="text-indigo-600 underline">Lihat Lowongan Kerja</a>
    @else
        <p class="text-red-600 font-semibold">Anda harus melengkapi biodata terlebih dahulu.</p>
        <a href="{{ route('applicant.create') }}" class="text-indigo-600 underline">Lengkapi Biodata</a>
    @endif
@endsection
