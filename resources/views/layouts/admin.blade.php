{{-- resources/views/components/app-admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&display=swap" rel="stylesheet">

  {{-- Vite --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Trix (jika dipakai) --}}
  <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
  <script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js" defer></script>

  {{-- AlpineJS (cukup sekali) --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- ApexCharts (opsional) --}}
  <script defer src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  <style>[x-cloak]{display:none!important}</style>
</head>

<body class="font-sans antialiased bg-white" x-data="{ sidebarOpen:false }">
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-full max-h-full mx-auto">
      <!-- Header -->
      <div class="bg-white p-2 flex items-center">
        <!-- Hamburger -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden focus:outline-none">
          <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <img src="{{ asset('/app-logo.svg') }}" alt="Logo" class="w-14 ms-2"/>
      </div>

      <div class="flex">
        <!-- Sidebar -->
        @php
          $name = Auth::user()->name;
          $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
        @endphp

        <aside
          class="w-64 bg-white p-6 2xl:mt-6 2xl:rounded-xl absolute z-30 inset-y-0 left-0 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0"
          :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
          x-data="{
            userOpen: {{ request()->is('admin/applicant*') || request()->is('admin') || request()->is('selection') ? 'true' : 'false' }},
            jobOpen:  {{ request()->is('admin/batch') ? 'true' : 'false' }},
            quizOpen: {{ request()->is('quiz') ? 'true' : 'false' }}
          }"
        >
          <div class="flex items-center justify-between mb-8">
            <div class="flex gap-3">
              <div class="bg-orange-500 w-12 h-12 flex items-center justify-center rounded-full text-white">
                <span>{{ $initials }}</span>
              </div>
              <div>
                <h4 class="font-bold text-lg">{{ Auth::user()->name }}</h4>
                <span class="text-sm text-slate-700">Administrator</span>
              </div>
            </div>
            <!-- Close (mobile) -->
            <button @click="sidebarOpen = false" class="lg:hidden">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <nav class="space-y-5">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center no-underline {{ request()->is('admin/dashboard') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-2' : 'text-gray-600' }}">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25..."/>
              </svg>
              Dashboard
            </a>

            <!-- User menu -->
            <div class="py-1">
              <button @click="userOpen = !userOpen"
                      class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0..."/>
                </svg>
                Mengelola User
                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': userOpen }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div x-show="userOpen" x-transition class="mt-3 ml-10 space-y-4 text-sm text-gray-600">
                <a href="{{ route('admin.applicant.index') }}"
                   class="block hover:text-blue-600 no-underline {{ request()->is('admin/applicant') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                   Applicant
                </a>
                <a href="{{ route('admin.applicant.seleksi.index') }}"
                   class="block hover:text-blue-600 no-underline {{ request()->is('admin/applicant/seleksi') || request()->is('admin/applicant/seleksi/*') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                   Selection
                </a>
                <a href="#" class="block hover:text-blue-600 no-underline {{ request()->is('admin/admin') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                  Admin List
                </a>
              </div>
            </div>

            <!-- Batch menu -->
            <div class="py-1">
              <button @click="jobOpen = !jobOpen"
                      class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25..."/>
                </svg>
                Mengelola Batch
                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': jobOpen }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div x-show="jobOpen" x-transition class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                <a href="{{ route('batch.index') }}"
                   class="block hover:text-blue-600 no-underline {{ request()->is('admin/batch') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                   Batch
                </a>
              </div>
            </div>

            <!-- Quiz menu -->
            <div class="py-1">
              <button @click="quizOpen = !quizOpen"
                      class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025..."/>
                </svg>
                Mengelola Kuis
                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': quizOpen }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div x-show="quizOpen" x-transition class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                <a href="#" class="block hover:text-blue-600 no-underline {{ request()->is('admin/quiz') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Quiz</a>
                <a href="{{ route('question.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/question') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Question</a>
                <a href="{{ route('bundle.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/bundle') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Bundle</a>
              </div>
            </div>

            <a href="#" class="flex items-center no-underline {{ request()->is('admin/report') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-3 px-3' : 'text-gray-600' }}">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625..."/>
              </svg>
              Mengelola Report
            </a>
          </nav>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="mt-6 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
              <div class="flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 me-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25..."/>
                </svg>
                Logout
              </div>
            </button>
          </form>
        </aside>

        <!-- Overlay (mobile) -->
        <div class="fixed inset-0 bg-black bg-opacity-0 z-20 lg:hidden"
             x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Page Content -->
        <main class="flex-1 p-8 md:p-8 max-w-7xl mx-auto">
          {{ $slot }}
        </main>
      </div>
    </div>
  </div>

  <footer class="bg-gray-900 text-gray-200 py-10 px-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4">
      <div>
        <h3 class="text-3xl font-bold mb-4">ReQuiz Admin</h3>
        <h4 class="text-lg font-semibold">Kontak</h4>
        <p class="text-sm text-gray-400">Email: support@namabrand.com</p>
        <p class="text-sm text-gray-400">Telepon: +62 812 3456 7890</p>
      </div>
    </div>
    <div class="mt-10 border-t border-gray-700 pt-6 text-center text-sm text-gray-500">
      © {{ now()->year }} ReQuiz. All rights reserved.
    </div>

    {{-- Popup notifikasi sukses --}}
    @if (session('success'))
      <script>
        document.addEventListener("DOMContentLoaded", function () {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
          });
        });
      </script>
    @endif
  </footer>

  {{-- JS libs --}}
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
          crossorigin="anonymous"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
