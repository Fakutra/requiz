<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&display=swap" rel="stylesheet">

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Trix -->
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
  <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

  {{-- Datatables --}}
  {{-- <link rel="stylesheet" href="resources/css/dataTables.css" /> --}}

        {{-- Datatables --}}
        {{-- <link rel="stylesheet" href="resources/css/dataTables.css" /> --}}
        <style>
            body {
                background-image: url('component/bg-app.jpg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                height: full;
                margin: 0;
        }
        </style>

<body class="font-sans antialiased">
  <div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @if (isset($header))
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{ $header }}
      </div>
    </header>
    @endif

    <!-- Page Content -->
    <main>
      {{ $slot }}
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

<footer class="bg-gray-900 text-gray-200 py-10 px-8">
  <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4">
    <!-- Brand -->
    <div>
      <h3 class="text-3xl font-bold mb-4">ReQuiz</h3>
      <div>
        <h4 class="text-lg font-semibold">Kontak</h4>
        <p class="text-sm text-gray-400">Email: support@namabrand.com</p>
        <p class="text-sm text-gray-400">Telepon: +62 812 3456 7890</p>
        <div class="flex space-x-4 mt-4">
          <a href="#" class="hover:text-white"><i class="fab fa-facebook"></i></a>
          <a href="#" class="hover:text-white"><i class="fab fa-instagram"></i></a>
          <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-10 border-t border-gray-700 pt-6 text-center text-sm text-gray-500">
    © 2025 ReQuiz. All rights reserved.
  </div>
</footer>

<script>
  function toggleAccordion(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('svg');

    const isOpen = !content.classList.contains('hidden');

    // Tutup semua accordion lain
    document.querySelectorAll('[onclick="toggleAccordion(this)"]').forEach(btn => {
      btn.nextElementSibling.classList.add('hidden');
      btn.querySelector('svg').classList.remove('rotate-180');
    });

    if (!isOpen) {
      content.classList.remove('hidden');
      icon.classList.add('rotate-180');
    }
  }
</script>

</html>