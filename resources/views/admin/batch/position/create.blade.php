<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Create New Position') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">
                <form method="post" action="{{ route('position.store', $batchs->id) }}" class="mb-5" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 col-lg-8">
                        <label for="name" class="form-label">Nama Posisi</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required autofocus value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" disabled readonly>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="quota" class="form-label">Kuota</label>
                        <input type="number" class="form-control @error('quota') is-invalid @enderror" id="quota" name="quota" required value="{{ old('quota') }}">
                        @error('quota')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>                    
                    <div class="mb-3 col-lg-8">
                        <label for="description" class="form-label">Description</label>
                        @error('description')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <input id="description" type="hidden" name="description">
                        <trix-editor input="description"></trix-editor>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Position</button>
                </form>
              </div>
          </div>
      </div>
  </div>
  <script>
    const name = document.querySelector('#name');
    const slug = document.querySelector('#slug');

    name.addEventListener('change', function() {
        fetch('/admin/batch/position/checkSlug?name=' + name.value)
            .then(response => response.json())
            .then(data => slug.value = data.slug)
    });
</script>
</x-app-layout>
