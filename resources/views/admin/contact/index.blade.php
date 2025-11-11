<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Informasi Kontak</h2>

    <form class="space-y-5 w-full item-center">
      {{-- EMAIL --}}
      <div class="flex items-center w-full">
        <label for="email" class="w-40 text-sm font-medium text-gray-800 flex-shrink-0">
          Email
        </label>
        <input
          type="email"
          id="email"
          placeholder="example@gmail.com"
          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      {{-- NARAHUBUNG --}}
      <div class="flex items-center w-full">
        <label for="pic_name" class="w-40 text-sm font-medium text-gray-800 flex-shrink-0">
          Narahubung
        </label>
        <input
          type="text"
          id="pic_name"
          placeholder="Jok***"
          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <div class="pt-4 flex justify-end">
        <button
          type="button"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium">
          Simpan
        </button>
      </div>
    </form>
  </div>
</x-app-admin>
