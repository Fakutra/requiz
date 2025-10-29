<<<<<<< HEAD
<form action="{{ route('batch.update', $batch->id) }}"
      method="POST"
      x-data="{ loading: false }"
      @submit="loading = true">

=======
<<<<<<< HEAD
<form action="{{ route('batch.update', $batch->id) }}" method="POST" x-data="{ loading: false }" @submit="loading = true" @click.stop>
=======
<form action="{{ route('batch.update', $batch->id) }}" method="POST" x-data="{ loading: false }" @submit="loading = true"
    @click.stop>
>>>>>>> origin/main
>>>>>>> FE-Bagas
    @csrf
    @method('PUT')

    {{-- NAMA BATCH --}}
    <div class="mb-3">
        <label class="block text-sm font-medium">Nama Batch</label>
<<<<<<< HEAD
        <input type="text" name="name"
            class="mt-1 block w-full border border-gray-300 rounded-lg p-2
                   focus:border-blue-500 focus:ring-blue-500"
=======
        <input type="text" name="name" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:border-blue-500 focus:ring-blue-500"
>>>>>>> FE-Bagas
            value="{{ old('name', $batch->name) }}" required>
    </div>

    {{-- STATUS --}}
    <div class="mb-3">
        <label class="block text-sm font-medium">Status</label>
<<<<<<< HEAD
        <select name="status"
            class="mt-1 block w-full border border-gray-300 rounded-lg p-2
                   focus:border-blue-500 focus:ring-blue-500">
=======
        <select name="status" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:border-blue-500 focus:ring-blue-500">
>>>>>>> FE-Bagas
            <option value="Active" @selected(old('status', $batch->status) == 'Active')>Active</option>
            <option value="Closed" @selected(old('status', $batch->status) == 'Closed')>Closed</option>
        </select>
    </div>

    {{-- TANGGAL --}}
    <div class="flex justify-between gap-3">
        <div class="mb-3 flex-1">
            <label class="block text-sm font-medium">Start Date</label>
<<<<<<< HEAD
            <input type="date" name="start_date"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2
                       focus:border-blue-500 focus:ring-blue-500"
                value="{{ old('start_date', \Carbon\Carbon::parse($batch->start_date)->format('Y-m-d')) }}"
                required>
=======
            <input type="date" name="start_date" class="mt-1 block w-full border border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg p-2"
                value="{{ old('start_date', $batch->start_date) }}" required>
>>>>>>> FE-Bagas
        </div>

        <div class="mb-3 flex-1">
            <label class="block text-sm font-medium">End Date</label>
<<<<<<< HEAD
            <input type="date" name="end_date"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2
                       focus:border-blue-500 focus:ring-blue-500"
                value="{{ old('end_date', \Carbon\Carbon::parse($batch->end_date)->format('Y-m-d')) }}"
                required>
=======
            <input type="date" name="end_date" class="mt-1 block w-full border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 p-2"
                value="{{ old('end_date', $batch->end_date) }}" required>
>>>>>>> FE-Bagas
        </div>
    </div>

    {{-- TOMBOL --}}
    <div class="flex justify-end gap-2 mt-4">
        <button type="button"
            @click="$root.showEditBatch = null"
            class="px-4 py-2 bg-gray-300 rounded-lg">
            Close
        </button>

        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2">
            <span x-show="!loading">Ubah</span>
<<<<<<< HEAD
            <svg x-show="loading"
                class="animate-spin h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
=======
            <svg x-show="loading" class="animate-spin h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>
>>>>>>> FE-Bagas
            </svg>
        </button>
    </div>

</form>
