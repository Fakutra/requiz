<x-guest-layout>
    <div>
        <div class="bg-cover bg-center bg-no-repeat px-8 py-20 md:px-18" style="background-image: url('/img/bg-cover.png');">
            <div class="max-w-7xl mx-auto">
                <h3 class="text-2xl md:text-3xl text-white">Selamat Datang!</h3>
                @auth
                <h1 class="font-semibold text-4xl mt-2 text-white">
                    {{ Auth::user()->name }}
                </h1>
                @endauth
                @guest
                <h1 class="font-semibold text-3xl md:text-4xl mt-2 text-white">
                    Rekrutmen Tenaga Alih Daya
                </h1>
                @endguest
            </div>
        </div>
        <div class="w-full px-8" id="job">
            <div class="max-w-7xl py-10 mx-auto">
                <div class="flex flex-wrap justify-between align-items-center mb-4">
                    <h1 class="font-bold text-2xl md:text-3xl">Lowongan tersedia</h1>
                    <a href="{{ route('joblist') }}">
                        <div class="flex flex-wrap gap-2 items-center text-[#009DA9]">
                            Lihat lainnya
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 10.28a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 1 0-1.06 1.06l1.72 1.72H8.25a.75.75 0 0 0 0 1.5h5.69l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </a>
                </div>

                @if ($latestPositions->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($latestPositions as $position)
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6
                                hover:shadow-lg transition">
                        <h2 class="text-xl font-semibold mb-2">
                            {{ $position->name }}
                        </h2>
                        <div class="text-gray-500 space-y-1">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                                <span>Minimal {{ $position->pendidikan_minimum ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <span>
                                    Batas lamaran:
                                    {{ $position->deadline
                                                ? \Carbon\Carbon::parse($position->deadline)->translatedFormat('d F Y')
                                                : '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('jobdetail', $position) }}" class="block w-full text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-[#008A95]">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex items-center justify-center min-h-[36vh] bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="text-center max-w-md px-6">
                        <div class="bg-red-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-red-600">
                                <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748 c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5 L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">
                            Lowongan belum tersedia
                        </h1>
                        <p class="text-gray-500 text-sm md:text-lg leading-relaxed">
                            Saat ini belum ada lowongan pekerjaan yang dibuka. Silakan periksa kembali dalam beberapa waktu ke depan
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="w-full px-8" id="faq">
            <div class="max-w-7xl py-10 mx-auto">
                <h1 class="font-bold text-2xl md:text-3xl mb-4">Frequently Asked Question</h1>

                <div class="max-w-7xl mx-auto divide-y divide-gray-300">
                    @forelse ($faqs as $i => $faq)
                    <div class="py-4" x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }">
                        <button type="button"
                            class="w-full flex justify-between items-center text-left font-semibold text-gray-800 focus:outline-none"
                            @click="open = !open">
                            <span class="text-2xl">{{ $faq->question }}</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div class="mt-2 text-gray-600 text-xl" x-show="open" x-collapse x-cloak>
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                    @empty
                    <div class="flex items-center justify-center min-h-[36vh] bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                        <div class="text-center max-w-md px-6">
                            <div class="bg-red-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-red-600">
                                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748 c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5 L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">
                                Belum ada konten yang ditampilkan
                            </h1>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="w-full px-8" id="about">
            <div class="max-w-7xl py-10 mx-auto">
                <h1 class="font-bold text-2xl md:text-3xl mb-4">Tentang Kami</h1>
                @forelse($aboutBlocks as $block)
                @php
                $layout = $block->layout ?? 'image_left';
                $img = $block->image_path;
                $alt = $block->image_alt ?? 'Tentang Kami';
                @endphp

                @switch($layout)
                @case('image_right')
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <div class="flex-1 order-2 sm:order-2">
                        @if($img)
                        <img src="{{ Storage::url($img) }}" alt="{{ $alt }}" class="rounded-2xl w-full object-cover">
                        @else
                        <div class="aspect-video rounded-2xl w-full bg-gray-100 grid place-content-center text-gray-400">No Image</div>
                        @endif
                    </div>
                    <div class="flex-1 order-1 sm:order-1">
                        <div class="mt-2 text-[1.2rem] sm:text-2xl leading-relaxed text-gray-800">
                            {{ $block->description }}
                        </div>
                    </div>
                </div>
                @break

                @case('full_image')
                <div class="mt-6">
                    @if($img)
                    <img src="{{ Storage::url($img) }}" alt="{{ $alt }}" class="rounded-2xl w-full object-cover mb-4">
                    @else
                    <div class="aspect-video rounded-2xl w-full bg-gray-100 grid place-content-center text-gray-400 mb-4">No Image</div>
                    @endif
                    <div class="text-[1.2rem] sm:text-2xl leading-relaxed text-gray-800">
                        {{ $block->description }}
                    </div>
                </div>
                @break

                @default {{-- image_left --}}
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 mt-6">
                    <div class="flex-1">
                        @if($img)
                        <img src="{{ Storage::url($img) }}" alt="{{ $alt }}" class="rounded-2xl w-full object-cover">
                        @else
                        <div class="aspect-video rounded-2xl w-full bg-gray-100 grid place-content-center text-gray-400">No Image</div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="mt-2 text-[1.2rem] sm:text-2xl leading-relaxed text-gray-800">
                            {{ $block->description }}
                        </div>
                    </div>
                </div>
                @endswitch
                @empty
                <div class="flex items-center justify-center min-h-[36vh] bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="text-center max-w-md px-6">
                        <div class="bg-red-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-red-600">
                                <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748 c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5 L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">
                            Belum ada konten yang ditampilkan
                        </h1>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-guest-layout>