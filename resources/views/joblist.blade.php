<x-guest-layout>
    <div class="min-h-screen">
        <div class="bg-cover bg-center bg-no-repeat px-8 py-20 md:px-18" style="background-image: url('/img/bg-cover.png');">
            <div class="max-w-7xl mx-auto">
                <h2 class="font-semibold text-4xl text-white">Lowongan Tersedia</h2>
                <h4 class="text-xl text-white mt-2 font-sans">Mari berkarir bersama kami</h4>
            </div>
        </div>
        <div class="px-8 md:px-18">
            <div class="max-w-7xl mx-auto py-6">
                <div class="mb-7">
                    <h4 class="mb-2 text-lg">Menampilkan XX data lowongan aktif</h4>
                    <form>
                        <div class="flex flex-col sm:flex-row justify-between gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <!-- Heroicons search icon (SVG) -->
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                        </svg>
                                    </span>
                                    <input
                                        type="text"
                                        placeholder="Cari lowongan..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#009DA9] focus:border-[#009DA9] sm:text-sm" />
                                </div>
                            </div>
                            <div class="flex-2">
                                <select name="#" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">
                                    <option value="#">Pilih Jenjang pendidikan</option>
                                    <option value="#">SMA/SMK</option>
                                    <option value="#">D3</option>
                                    <option value="#">D4</option>
                                    <option value="#">S1</option>
                                    <option value="#">S2</option>
                                </select>
                            </div>
                            <div>
                                <input type="submit" class="w-full block text-center bg-[#009DA9] text-white px-4 py-2 sm:py-0 h-full rounded-lg hover:bg-blue-600" value="Cari">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">Junior Technical Support Grade I</h2>
                            <p class="text-gray-600 mb-4">Let's join with us</p>
                        </div>
                        <a href="{{ route('jobdetail') }}" class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-[#008A95]">
                            Lihat Detail
                        </a>
                    </div>
                    <!-- Card 1 -->
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">Junior Technical Support Grade I</h2>
                            <p class="text-gray-600 mb-4">Let's join with us</p>
                        </div>
                        <a href="{{ route('jobdetail') }}" class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-[#008A95]">
                            Lihat Detail
                        </a>
                    </div>
                    <!-- Card 1 -->
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">Junior Technical Support Grade I</h2>
                            <p class="text-gray-600 mb-4">Let's join with us</p>
                        </div>
                        <a href="{{ route('jobdetail') }}" class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-[#008A95]">
                            Lihat Detail
                        </a>
                    </div>
                    <!-- Card 1 -->
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">Junior Technical Support Grade I</h2>
                            <p class="text-gray-600 mb-4">Let's join with us</p>
                        </div>
                        <a href="{{ route('jobdetail') }}" class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-[#008A95]">
                            Lihat Detail
                        </a>
                    </div>
                    <!-- Card 1 -->
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">Junior Technical Support Grade I</h2>
                            <p class="text-gray-600 mb-4">Let's join with us</p>
                        </div>
                        <a href="{{ route('jobdetail') }}" class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-[#008A95]">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>