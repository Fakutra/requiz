<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ReQuiz') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Gabarito&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Favicon (optional) -->
        <!-- <link rel="icon" href="{{ asset('favicon.png') }}" /> -->
    </head>
    <body class="bg-[#EDF7FB] font-sans text-gray-900 antialiased scroll-smooth">
  <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-6">
    <div class="w-full max-w-lg bg-white overflow-hidden rounded-[16px] shadow-md p-6 sm:p-8 lg:p-12">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
