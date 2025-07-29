<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Edit Batch') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">
                <form method="post" action="{{ route('batch.update', $batchs->id) }}" class="mb-5" enctype="multipart/form-data">
                  @method('put')
                  @csrf
                  <div class="mb-3 col-md-6">
                      <label for="name" class="form-label">Nama Batch</label>
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required value="{{ old('name', $batchs->name) }}">
                      @error('name')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                  </div>
                  <div class="mb-3 col-md-6">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $batchs->slug) }}" disabled>
                  </div>
                  
                  <div class="mb-3 col-md-6">
                      <label for="status" class="form-label">Status</label>
                      <select class="form-select" name="status">
                        @if ($batchs->status === 'Closed')
                          <option value="{{ $batchs->status }}" selected>{{ $batchs->status }}</option>
                          <option value="Active">Active</option>
                        @else
                          <option value="{{ $batchs->status }}" selected>{{ $batchs->status }}</option>
                          <option value="Closed">Closed</option>
                        @endif
                      </select>
                  </div>
                  <div class="mb-3 col-sm-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $batchs->start_date }}">
                  </div>
                  <div class="mb-3 col-sm-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $batchs->end_date }}">
                  </div>
                  
                  <button type="submit" class="btn btn-primary">Update Batch</button>
                </form>
              </div>
          </div>
      </div>
  </div>
</x-app-layout>
