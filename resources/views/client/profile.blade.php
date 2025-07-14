<x-app-layout>
    <div class="min-h-screen px-6 md:px-8 max-w-7xl mx-auto mt-6">
        <!-- Profil Header -->
        <div class="bg-white rounded-xl p-6 flex flex-col md:flex-row items-center justify-between shadow-md mb-6">
            <div class="flex items-center gap-4">
                <img src="#" alt="Foto" class="w-24 h-24 rounded-full object-cover bg-gray-300" />
                <h2 class="text-2xl font-bold">Bagas Prasetio</h2>
            </div>
            <button class="mt-4 md:mt-0 w-full md:w-auto block text-center bg-[#009DA9] text-white flex items-center justify-center space-x-2 px-4 py-2 rounded-lg shadow-md hover:bg-indigo-800 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Unduh Daftar Riwayat Hidup</span>
            </button>
        </div>

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Sidebar -->
            <div class="bg-white rounded-xl shadow-md p-4 space-y-2 md:col-span-1">
                <div class="flex items-center justify-between bg-[#EDF6FF] text-[#009DA9] font-semibold px-4 py-2 rounded-lg">
                    <div class="flex items-center gap-2">
                        Data Diri
                    </div>
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>

                <div class="flex items-center justify-between hover:bg-gray-100 px-4 py-2 rounded cursor-pointer">
                    <div class="flex items-center gap-2">
                        Pendidikan
                    </div>
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>

                <div class="flex items-center justify-between hover:bg-gray-100 px-4 py-2 rounded cursor-pointer">
                    <div class="flex items-center gap-2">
                        Alamat
                    </div>
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>

                <div class="flex items-center justify-between hover:bg-gray-100 px-4 py-2 rounded cursor-pointer">
                    <div class="flex items-center gap-2">
                        Pengalaman Kerja
                    </div>
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
            </div>

            <!-- Konten Kanan -->
            <div class="md:col-span-3 space-y-4">
                <!-- Alert Box -->
                <div class="bg-orange-50 text-orange-800 border border-orange-400 px-4 py-3 rounded-md flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p>Pastikan data diri yang Anda masukkan sesuai dengan informasi yang tercantum di KTP untuk menghindari kesalahan.</p>
                </div>

                <!-- Data Diri -->
                <div class="bg-white rounded-xl p-6 shadow-md">
                    <h3 class="text-xl font-bold mb-4">Data Diri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">No. KTP</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Nama Lengkap</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">No. Kontak</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Jenis Kelamin</p>
                            <p class="font-semibold">Laki-Laki</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal Lahir</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tempat Lahir</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Status Perkawinan</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Agama</p>
                            <p class="font-semibold">*********</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konten Kanan Pendidikan-->
            <div class="md:col-span-3 space-y-4">
                <!-- Alert Box -->
                <div class="bg-orange-50 text-orange-800 border border-orange-400 px-4 py-3 rounded-md flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p>Lorem ipsum</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md flex justify-between items-center">
                    <h3 class="text-xl font-bold">Pendidikan</h3>
                    <button class="mt-4 md:mt-0 w-full md:w-auto block text-center bg-[#009DA9] text-white flex items-center justify-center space-x-2 px-4 py-2 rounded-lg shadow-md hover:bg-indigo-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah Riwayat Pendidikan</span>
                    </button>
                </div>

                <!-- Data Diri -->
                <div class="bg-white rounded-xl p-6 shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold">Diploma III</h3>
                            <h5 class="text-lg">Universitas Trisakti</h5>
                        </div>
                        <div class="flex space-x-2">
                            <div class="p-2 bg-amber-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-amber-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </div>
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4 border-gray-300" />
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">IPK</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tahun Mulai</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tahun Lulus</p>
                            <p class="font-semibold">*********</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Jurusan</p>
                            <p class="font-semibold">*********</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>