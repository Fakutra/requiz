<x-guest-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500">
            <a href="{{ route('joblist') }}" class="hover:text-[#009DA9]">
                < Job List</a>
                    <span class="mx-2">/</span>
                    <span class="text-[#009DA9] font-semibold">Job Detail</span>
        </nav>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEFT: Job detail -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#009DA9] mt-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                            </svg>
                            <div>
                                <h1 class="text-xl font-semibold text-gray-900">
                                    {{ $job->title ?? 'Application Support' }}
                                </h1>
                                <h5 class="text-sm text-[#009DA9] mt-1">
                                    {{ $job->company ?? 'PLN Icon Plus' }}
                                </h5>
                            </div>
                        </div>

                        <div class="mt-6 space-y-6 text-md leading-6 text-gray-800">
                            <div>
                                <h3 class="font-semibold text-gray-900">Deskripsi Pekerjaan:</h3>
                                <p class="mt-1 text-gray-600">
                                    {{ $job->description ?? 'Provide technical support and assistance to application users (internal or external), analyze and resolve application errors, bugs, or performance issues, participate in application testing (e.g., UAT), prior to deployment or updates, etc.' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-900">Kriteria Khusus:</h3>
                                <ul class="mt-1 list-disc list-inside space-y-1 text-gray-600">
                                    <li>Master one of database</li>
                                    <li>Master one of programming language</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Sidebar -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 text-md shadow-sm p-5 lg:sticky lg:top-24">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#009DA9] ring-4 ring-[#009DA9]/15"></span>
                        <h3 class="font-semibold text-gray-900 ml-1">Persyaratan Umum</h3>
                    </div>
                    <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                        <li>D3 – S1</li>
                        <li>Min. GPA ≥ 3.00</li>
                        <li>Age limit ≤ 35 years</li>
                    </ul>

                    <div class="mt-5">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-[#009DA9] ring-4 ring-[#009DA9]/15"></span>
                            <h3 class="font-semibold text-gray-900 ml-1">Jurusan yang dapat melamar</h3>
                        </div>
                        <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                            <li>Teknik Informatika</li>
                            <li>Teknik Telekomunikasi</li>
                            <li>Ilmu Komputer</li>
                            <li>Sistem Informasi</li>
                        </ul>
                    </div>

                    <form action="{{ route('apply.store', $job->slug ?? 'application-support') }}" method="POST" class="mt-6">
                        @csrf
                        <button
                            class="w-full rounded-lg bg-[#009DA9] hover:bg-[#007C85] text-white font-semibold py-2.5 shadow-sm
                     focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#007C85]">
                            Apply Now
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</x-guest-layout>