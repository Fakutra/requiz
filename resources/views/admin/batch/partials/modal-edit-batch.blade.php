<form action="{{ route('batch.update', $batch->id) }}" method="POST" x-data="{ loading: false }" @submit="loading = true"
    @click.stop>
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="block text-sm font-medium">Nama Batch</label>
        <input type="text" name="name"
            class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:border-blue-500 focus:ring-blue-500"
            value="{{ old('name', $batch->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="block text-sm font-medium">Status</label>
        <select name="status"
            class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:border-blue-500 focus:ring-blue-500">
            <option value="Active" @selected(old('status', $batch->status) == 'Active')>Active</option>
            <option value="Closed" @selected(old('status', $batch->status) == 'Closed')>Closed</option>
        </select>
    </div>

    <div class="flex justify-between gap-3">
        <div class="mb-3 flex-1">
            <label class="block text-sm font-medium">Start Date</label>
            <input type="date" name="start_date"
                class="mt-1 block w-full border border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg p-2"
                value="{{ old('start_date', $batch->start_date) }}" required>
        </div>

        <div class="mb-3 flex-1">
            <label class="block text-sm font-medium">End Date</label>
            <input type="date" name="end_date"
                class="mt-1 block w-full border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 p-2"
                value="{{ old('end_date', $batch->end_date) }}" required>
        </div>
    </div>

    <div class="flex justify-end gap-2 mt-4">
        <button type="button" @click="showEditBatch = false" class="px-4 py-2 bg-gray-300 rounded-lg">Close</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
            <span x-show="!loading">Ubah</span>
            <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>
            </svg>
        </button>
    </div>
</form>
