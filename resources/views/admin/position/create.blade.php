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
                <form method="post" action="{{ route('position.index') }}" class="mb-5" enctype="multipart/form-data">
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
                        <select class="form-select" name="status">
                            <option selected value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="description" class="form-label">Description</label>
                        @error('description')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <input id="description" type="hidden" name="description">
                        <trix-editor input="description"></trix-editor>
                    </div>
                    {{-- <div class="mb-3">
                        <label for="image" class="form-label">Post Image</label>
                        <img class="img-preview img-fluid mb-3 col-sm-5">
                        <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" onchange="previewImage()">
                        @error('image')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div> --}}
                    {{-- <div class="mb-3">
                        <label for="body" class="form-label">Body</label>
                        @error('body')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <input id="body" type="hidden" name="body" value="{{ old('body') }}">
                        <trix-editor input="body"></trix-editor>                
                    </div> --}}
                    
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
        fetch('/admin/position/checkSlug?name=' + name.value)
            .then(response => response.json())
            .then(data => slug.value = data.slug)
    });
</script>
</x-app-layout>
