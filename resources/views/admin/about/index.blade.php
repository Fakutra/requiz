{{-- resources/views/admin/about/index.blade.php --}}
<x-app-admin>
  <div x-data="{ openCreate:false }" class="bg-white rounded-lg shadow-sm p-6 max-w-7xl mx-auto">

    @if(session('success'))
      <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="mb-4 p-3 rounded bg-red-50 text-red-700">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-semibold">Tentang Kami</h2>
      <button @click="openCreate=true" class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700">Create</button>
    </div>

    @if($items->isEmpty())
      <div class="rounded-2xl border border-dashed p-8 text-center text-gray-500">
        Belum ada blok. Klik <span class="font-medium">Create</span> buat tambah.
      </div>
    @else
      <div class="space-y-8">
        @foreach($items as $item)
          @php($layout = $item->layout)
          <div x-data="{ openEdit:false }" class="rounded-xl border p-5">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold">Blok #{{ $item->id }}</h3>
              <div class="flex items-center gap-2">
                <span class="px-2 py-1 rounded text-xs bg-gray-100">{{ $layout }}</span>
                <button @click="openEdit=true" class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700">Edit</button>
                
                <form action="{{ route('admin.about.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus blok ini?')">
                  @csrf @method('DELETE')
                  <button class="px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-700">Hapus</button>
                </form>
               
              </div>
            </div>

            {{-- Preview --}}
            @switch($layout)
              @case('image_right')
                <div class="grid md:grid-cols-2 gap-6 items-center">
                  <div class="md:order-2">
                    @if($item->image_path)
                      <img src="{{ Storage::url($item->image_path) }}" class="w-full rounded-xl shadow object-cover">
                    @else
                      <div class="aspect-video rounded-xl bg-gray-100 grid place-content-center text-gray-400">No Image</div>
                    @endif
                  </div>
                  <div class="md:order-1">
                    <p class="text-gray-700">{{ $item->description }}</p>
                  </div>
                </div>
              @break

              @case('full_image')
                @if($item->image_path)
                  <div class="rounded-xl overflow-hidden mb-4">
                    <img src="{{ Storage::url($item->image_path) }}" class="w-full object-cover">
                  </div>
                @else
                  <div class="aspect-video rounded-xl bg-gray-100 grid place-content-center text-gray-400 mb-4">No Image</div>
                @endif
                <p class="text-gray-700">{{ $item->description }}</p>
              @break

              @default
                <div class="grid md:grid-cols-2 gap-6 items-center">
                  <div>
                    @if($item->image_path)
                      <img src="{{ Storage::url($item->image_path) }}" class="w-full rounded-xl shadow object-cover">
                    @else
                      <div class="aspect-video rounded-xl bg-gray-100 grid place-content-center text-gray-400">No Image</div>
                    @endif
                  </div>
                  <div>
                    <p class="text-gray-700">{{ $item->description }}</p>
                  </div>
                </div>
            @endswitch

            {{-- Modal Edit --}}
            <div x-cloak x-show="openEdit" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
              <div @click.away="openEdit=false" class="w-full max-w-3xl bg-white rounded-2xl shadow-xl">
                <div class="flex items-center justify-between px-5 py-4 border-b">
                  <h4 class="font-semibold">Edit Blok #{{ $item->id }}</h4>
                  <button @click="openEdit=false" class="text-2xl leading-none text-gray-500">&times;</button>
                </div>

                <form action="{{ route('admin.about.update', $item) }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-5">
                  @csrf @method('PUT')

                  <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea name="description" rows="6" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $item->description) }}</textarea>
                    @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                  </div>

                  <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium mb-1">Layout</label>
                      <select name="layout" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        @foreach(['image_left'=>'Gambar kiri, teks kanan','image_right'=>'Gambar kanan, teks kiri','full_image'=>'Gambar penuh di atas'] as $val => $label)
                          <option value="{{ $val }}" @selected(old('layout', $item->layout)===$val)>{{ $label }}</option>
                        @endforeach
                      </select>
                      @error('layout') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium mb-1">Gambar (opsional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    @error('image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                    @if($item->image_path)
                      <div class="mt-3">
                        <p class="text-sm text-gray-600 mb-1">Preview saat ini:</p>
                        <img src="{{ Storage::url($item->image_path) }}" class="w-48 rounded-lg shadow">
                      </div>
                    @endif
                  </div>

                  <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="openEdit=false" class="px-4 py-2 rounded border">Batal</button>
                    <button class="px-5 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    {{-- MODAL: CREATE --}}
    <div x-cloak x-show="openCreate" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div @click.away="openCreate=false" class="w-full max-w-3xl bg-white rounded-2xl shadow-xl">
        <div class="flex items-center justify-between px-5 py-4 border-b">
          <h3 class="font-semibold">Create Blok</h3>
          <button @click="openCreate=false" class="text-2xl leading-none text-gray-500">&times;</button>
        </div>

        <form action="{{ route('admin.about.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-5">
          @csrf

          <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="description" rows="6" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Layout</label>
              <select name="layout" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                <option value="image_left"  @selected(old('layout')==='image_left')>Gambar kiri, teks kanan</option>
                <option value="image_right" @selected(old('layout')==='image_right')>Gambar kanan, teks kiri</option>
                <option value="full_image"  @selected(old('layout')==='full_image')>Gambar penuh di atas</option>
              </select>
              @error('layout') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            @error('image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" @click="openCreate=false" class="px-4 py-2 rounded border">Batal</button>
            <button class="px-5 py-2 rounded bg-green-600 text-white hover:bg-green-700">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-admin>
