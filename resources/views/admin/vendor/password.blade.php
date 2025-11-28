<x-app-admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ubah Password
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white shadow-sm rounded-xl p-6 border border-gray-200">
        <form method="POST" action="{{ route('admin.vendor.password.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>

              <div class="relative">
                  <input type="password" name="current_password" id="current_password"
                        class="border rounded w-full px-3 py-2 text-sm pr-10 @error('current_password') border-red-500 @enderror"
                        required>

                  <span onclick="togglePassword('current_password')" 
                        class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500">
                      <svg id="icon_current_password" xmlns="http://www.w3.org/2000/svg" fill="none"
                          viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3l18 18M9.88 9.88a3 3 0 104.24 4.24M6.5 6.5C4.46 8.06 3 10.39 3 12c0 3.5 4.5 7 9 7 1.59 0 3.17-.36 4.59-1.07M12 5c2.85 0 5.73 1.53 7.5 4" />
                      </svg>
                  </span>
              </div>

              @error('current_password')
                  <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>

              <div class="relative">
                  <input type="password" name="password" id="password"
                        class="border rounded w-full px-3 py-2 text-sm pr-10 @error('password') border-red-500 @enderror"
                        required>

                  <span onclick="togglePassword('password')" 
                        class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500">
                      <svg id="icon_password" xmlns="http://www.w3.org/2000/svg" fill="none"
                          viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3l18 18M9.88 9.88a3 3 0 104.24 4.24M6.5 6.5C4.46 8.06 3 10.39 3 12c0 3.5 4.5 7 9 7 1.59 0 3.17-.36 4.59-1.07M12 5c2.85 0 5.73 1.53 7.5 4" />
                      </svg>
                  </span>
              </div>

              @error('password')
                  <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>

              <div class="relative">
                  <input type="password" name="password_confirmation" id="password_confirmation"
                        class="border rounded w-full px-3 py-2 text-sm pr-10"
                        required>

                  <span onclick="togglePassword('password_confirmation')" 
                        class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500">
                      <svg id="icon_password_confirmation" xmlns="http://www.w3.org/2000/svg" fill="none"
                          viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3l18 18M9.88 9.88a3 3 0 104.24 4.24M6.5 6.5C4.46 8.06 3 10.39 3 12c0 3.5 4.5 7 9 7 1.59 0 3.17-.36 4.59-1.07M12 5c2.85 0 5.73 1.53 7.5 4" />
                      </svg>
                  </span>
              </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
                Simpan Password
            </button>
        </form>
    </div>

    <script>
      function togglePassword(id) {
          const input = document.getElementById(id);
          const icon = document.getElementById("icon_" + id);

          if (input.type === "password") {
              input.type = "text";
              icon.innerHTML = `
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  <circle cx="12" cy="12" r="3" />
              `;
          } else {
              input.type = "password";
              icon.innerHTML = `
                  <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 3l18 18M9.88 9.88a3 3 0 104.24 4.24M6.5 6.5C4.46 8.06 3 10.39 3 12c0 3.5 4.5 7 9 7 1.59 0 3.17-.36 4.59-1.07M12 5c2.85 0 5.73 1.53 7.5 4" />
              `;
          }
      }
    </script>
</x-app-admin>
