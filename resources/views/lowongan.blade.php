<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Lowongan') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">
                <div class="row">
                  @foreach ($lowongans as $lowongan)
                    <div class="col-md-6 mb-3">
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">{{ $lowongan->name }}</h5>
                          <div class="card-body">
                            <p class="card-text">{{ $lowongan->description }}</p>
                          </div>
                          <a href="apply/{{ $lowongan->id }}" class="btn btn-primary">Apply</a>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  
                  </div>
              </div>
          </div>
      </div>
  </div>
</x-app-layout>

