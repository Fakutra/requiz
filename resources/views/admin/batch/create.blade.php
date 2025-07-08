<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Create New Batch') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">
                <form method="post" action="{{ route('batch.index') }}" class="mb-5" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 col-lg-8">
                        <label for="name" class="form-label">Nama Batch</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required autofocus value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" disabled>
                    </div>
                    <div class="mb-3 col-lg-8">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option selected>--- Pilih ---</option>
                            <option value="Active">Active</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <div class="mb-3 col-sm-4">
                      <label for="start_date" class="form-label">Start Date</label>
                      <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" required value="">
                      @error('start_date')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                    </div>
                    <div class="mb-3 col-sm-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" required value="">
                        @error('end_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Create Batch</button>
                </form>
              </div>
          </div>
      </div>
  </div>
  <script>
    const name = document.querySelector('#name');
    const slug = document.querySelector('#slug');

    name.addEventListener('change', function() {
        fetch('/admin/batch/checkSlug?name=' + name.value)
            .then(response => response.json())
            .then(data => slug.value = data.slug)
    });
</script>
</x-app-layout>
