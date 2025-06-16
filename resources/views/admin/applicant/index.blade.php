<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Pelamar') }}
      </h2>
      {{-- <a href="/admin/position/create" class="btn btn-primary mb-3">Create New</a>
      @if (session()->has('success'))
        <div class="alert alert-success col-lg-8" role="alert">
          {{ session('success') }}
        </div>
      @endif --}}
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Posisi</th>
                      <th>Kuota</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {{-- @foreach ($positions as $position)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $position->name }}</td>
                        <td>{{ $position->quota }}</td>
                        <td>{{ $position->status }}</td>
                        <td>
                          <a href="/admin/position/{{ $position->id }}/edit" class="btn btn-sm btn-success">Edit</a>
                          <form action="/admin/position/{{ $position->id }}" method="post" class="d-inline">
                            @method('delete')
                              @csrf
                              <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                Delete
                              </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach --}}
                  </tbody>
                </table> 
              </div>
          </div>
      </div>
  </div>
</x-app-layout>

