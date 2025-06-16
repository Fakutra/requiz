<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Edit Position') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">
                <form method="post" action="{{ route('position.update', $positions->id) }}" class="mb-5" enctype="multipart/form-data">
                  @method('put')
                  @csrf
                  <div class="mb-3">
                      <label for="name" class="form-label">Nama Posisi</label>
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required autofocus value="{{ old('name', $positions->name) }}">
                      @error('name')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                  </div>
                  <div class="mb-3">
                      <label for="quota" class="form-label">Kuota</label>
                      <input type="number" class="form-control @error('quota') is-invalid @enderror" id="quota" name="quota" required value="{{ old('quota', $positions->quota) }}">
                      @error('quota')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                  </div>
                  <div class="mb-3">
                      <label for="status" class="form-label">Status</label>
                      <select class="form-select" name="status">
                        <option value="{{ $positions->status }}" selected>{{ $positions->status }}</option>
                        <option value="inactive">Inactive</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label for="description" class="form-label">Description</label>
                      @error('description')
                          <p class="text-danger">{{ $message }}</p>
                      @enderror
                      <input id="description" type="hidden" name="description" value="{{ old('description', $positions->description) }}">
                      <trix-editor input="description"></trix-editor>                
                  </div>
                  
                  <button type="submit" class="btn btn-primary">Update Position</button>
              </form>
              </div>
          </div>
      </div>
  </div>
</x-app-layout>
