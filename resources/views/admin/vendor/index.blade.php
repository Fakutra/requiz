<x-app-admin>
    <div class="bg-white rounded-lg shadow-sm p-5">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Daftar Vendor</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola vendor yang bekerja sama dengan perusahaan.
                </p>
            </div>

            <button
                type="button"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                + Tambah Vendor
            </button>
        </div>

        {{-- Tabel Vendor (UI only, dummy data) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-gray-600 bg-gray-50 border-b">
                        <th class="px-4 py-2 text-left font-medium">Nama Vendor</th>
                        <th class="px-4 py-2 text-left font-medium">PIC</th>
                        <th class="px-4 py-2 text-left font-medium">Kontak</th>
                        <th class="px-4 py-2 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    {{-- Row contoh 1 --}}
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-medium">
                            PT Contoh Vendor Satu
                        </td>
                        <td class="px-4 py-3">
                            Budi Santoso
                        </td>
                        <td class="px-4 py-3">
                            0812-0000-0001
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="px-3 py-1.5 rounded-lg border text-red-600 hover:bg-red-50">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        {{-- Placeholder pagination (UI saja) --}}
        <div class="mt-4 flex justify-between items-center text-xs text-gray-500">
            <div>
                Menampilkan 1–3 dari 3 vendor
            </div>
            <div class="inline-flex border rounded-lg overflow-hidden">
                <button class="px-3 py-1 border-r hover:bg-gray-50">&laquo;</button>
                <button class="px-3 py-1 bg-blue-50 text-blue-600 font-medium">1</button>
                <button class="px-3 py-1 border-l hover:bg-gray-50">&raquo;</button>
            </div>
        </div>
    </div>
</x-app-admin>
