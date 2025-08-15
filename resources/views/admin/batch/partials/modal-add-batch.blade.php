<!-- Form -->
<form method="POST" action="{{ route('batch.store') }}" enctype="multipart/form-data" class="space-y-4" x-data="{ loading: false }" @submit="loading = true">
    @csrf

    <!-- Nama Batch -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Batch</label>
        <input type="text" id="name" name="name"
            class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            required autofocus value="{{ old('name') }}" placeholder="Nama Batch">
        @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status -->
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status"
            class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            <option value="" selected>--- Pilih ---</option>
            <option value="Active">Active</option>
            <option value="Closed">Closed</option>
        </select>
    </div>

    <div class="flex justify-between gap-3">
        <!-- Start Date -->
        <div class="flex-1">
            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="date" id="start_date" name="start_date"
                class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                required>
            @error('start_date')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- End Date -->
        <div class="flex-1">
            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
            <input type="date" id="end_date" name="end_date"
                class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                required>
            @error('end_date')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Footer -->
    <div class="flex justify-end space-x-2 pt-4">
        <button type="button" @click="showAddBatch = false"
            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">Close</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            <span x-show="!loading">Simpan</span>
            <svg x-show="loading" class="animate-spin h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>
            </svg>
        </button>
    </div>
</form>