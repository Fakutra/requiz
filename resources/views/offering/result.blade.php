<x-app-layout>
  <div class="max-w-lg mx-auto text-center py-10">
    @if ($status === 'accepted')
      <h1 class="text-2xl font-bold text-green-600">Terima kasih! Anda telah menerima tawaran ini 🎉</h1>
    @elseif ($status === 'declined')
      <h1 class="text-2xl font-bold text-red-600">Anda menolak tawaran ini ❌</h1>
    @else
      <h1 class="text-2xl font-bold text-gray-600">Link tidak valid</h1>
    @endif
  </div>
</x-app-layout>
