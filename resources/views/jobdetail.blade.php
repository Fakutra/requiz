<x-guest-layout>
    <div class="max-w-7xl mx-auto py-8 px-6 md:px-8 xl:px-0"
        x-data="applyForm({
            loginUrl: '{{ route('login') }}',
            initialShow: @js($errors->any() || session('open_apply')),
            isAuth: @js(auth()->check()),
            loading: false
        })">

        {{-- ===== Breadcrumb ===== --}}
        <nav class="text-md text-gray-500">
            <a href="{{ route('joblist') }}" class="hover:text-[#009DA9]">
                < Job List</a>
            <span class="mx-2">/</span>
            <span class="text-[#009DA9] font-semibold">Job Detail</span>
        </nav>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- ===== LEFT: Job detail ===== --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-300 shadow-sm">
                    <div class="p-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#009DA9] mt-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                            </svg>

                            <div>
                                <h1 class="text-xl font-semibold text-gray-900">
                                    {{ $position->name }}
                                </h1>
                                <h5 class="text-sm text-[#009DA9] mt-1">
                                    {{ $job->company ?? 'PLN Icon Plus' }}
                                </h5>
                            </div>
                        </div>

                        <div class="mt-6 space-y-6 text-md leading-6 text-gray-800">
                            {{-- DESKRIPSI PEKERJAAN --}}
                            @php
                                // normalize description ke array
                                if (is_array($position->description)) {
                                    $descList = $position->description;
                                } elseif ($position->description && @json_decode($position->description, true)) {
                                    $descList = json_decode($position->description, true);
                                } elseif ($position->description) {
                                    $descList = preg_split('/\r\n|\r|\n/', $position->description);
                                } else {
                                    $descList = [];
                                }
                            @endphp

                            @if(count($descList))
                                <ul class="mt-1 list-disc list-inside space-y-1 text-gray-600">
                                    @foreach($descList as $d)
                                        @if(trim($d) !== '')
                                            <li>{{ trim($d) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-1 text-gray-600">-</p>
                            @endif

                            {{-- SKILL YANG DIBUTUHKAN --}}
                            @php
                                // skill bisa array / JSON / CSV
                                if (is_array($position->skills)) {
                                    $skillOptions = $position->skills;
                                } elseif ($position->skills && @json_decode($position->skills, true)) {
                                    $skillOptions = json_decode($position->skills, true);
                                } elseif ($position->skills) {
                                    // comma or newline separated
                                    $skillOptions = preg_split('/\r\n|\r|\n|,/', $position->skills);
                                } else {
                                    $skillOptions = [];
                                }
                                $skillOptions = array_values(array_filter(array_map('trim', $skillOptions)));
                            @endphp

                            @if (count($skillOptions))
                                <div>
                                    <h3 class="font-semibold text-gray-900">Skill yang dibutuhkan</h3>
                                    <ul class="mt-1 list-disc list-inside space-y-1 text-gray-600">
                                        @foreach ($skillOptions as $skill)
                                            <li>{{ $skill }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== RIGHT: Sidebar ===== --}}
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 text-md shadow-sm p-5 lg:sticky lg:top-24">

                    {{-- PENDIDIKAN MINIMUM --}}
                    @if (!empty($position->pendidikan_minimum))
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full bg-[#009DA9] ring-4 ring-[#009DA9]/15"></span>
                                <h3 class="font-semibold text-gray-900 ml-1">Pendidikan Minimum</h3>
                            </div>
                            <span class="inline-block text-gray-600 ml-5 mt-1">
                                {{ $position->pendidikan_minimum }}
                            </span>
                        </div>
                    @endif

                    {{-- PERSYARATAN UMUM --}}
                    @php
                        // helper: normalize value ke array bersih
                        $toList = function ($value) {
                            if (is_array($value)) {
                                $items = $value;
                            } elseif (is_null($value) || $value === '') {
                                $items = [];
                            } else {
                                // coba decode JSON dulu
                                $decoded = json_decode($value, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $items = $decoded;
                                } else {
                                    // fallback: pecah pakai newline / koma
                                    $items = preg_split('/\r\n|\r|\n|,/', $value);
                                }
                            }

                            // trim + buang kosong
                            return array_values(array_filter(array_map('trim', (array) $items)));
                        };
                    @endphp

                    {{-- Requirements --}}
                    @php
                        $reqList = $toList($position->requirements);
                    @endphp

                    @if (count($reqList))
                        <div class="mt-5">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full bg-[#009DA9] ring-4 ring-[#009DA9]/15"></span>
                                <h3 class="font-semibold text-gray-900 ml-1">Persyaratan Umum</h3>
                            </div>
                            <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                @foreach ($reqList as $req)
                                    <li>{{ $req }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Majors --}}
                    @php
                        $majorsList = $toList($position->majors);
                    @endphp

                    @if (count($majorsList))
                        <div class="mt-5">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full bg-[#009DA9] ring-4 ring-[#009DA9]/15"></span>
                                <h3 class="font-semibold text-gray-900 ml-1">Jurusan yang dapat melamar</h3>
                            </div>
                            <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                @foreach ($majorsList as $major)
                                    <li>{{ $major }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- BATAS LAMARAN --}}
                    @if (!empty($position->deadline))
                        <div class="mt-5">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full bg-[#009DA9] ring-4 ring-[#009DA9]/15"></span>
                                <h3 class="font-semibold text-gray-900 ml-1">Batas Lamaran</h3>
                            </div>
                            <span class="inline-block text-gray-600 ml-5 mt-1">
                                {{ \Carbon\Carbon::parse($position->deadline)->translatedFormat('d F Y') }}
                            </span>
                        </div>
                    @endif

                    {{-- Tombol Lamar --}}
                    <div class="mt-5">
                        @php
                            $alreadyApplied = in_array($position->batch_id, $appliedBatchIds, true);
                        @endphp

                        @if ($alreadyApplied)
                            <button type="button"
                                class="w-full rounded-lg bg-gray-200 text-gray-500 font-semibold px-4 py-3 cursor-not-allowed">
                                Sudah melamar pada batch ini
                            </button>
                        @else
                            <button type="button"
                                @click="isAuth ? (showApply = true) : window.location.href='{{ route('login', ['redirectTo' => request()->fullUrl()]) }}'"
                                class="w-full rounded-lg bg-[#009DA9] hover:bg-[#007C85] text-white font-semibold py-2.5 shadow-sm
                                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#007C85]">
                                Lamar sekarang
                            </button>
                        @endif
                    </div>
                </div>
            </aside>
        </div>

        {{-- ===== MODAL APPLY ===== --}}
        <div
            x-cloak
            x-show="showApply"
            x-transition.opacity
            @keydown.escape.window="showApply=false"
            class="fixed inset-0 z-[60] flex items-center justify-center"
            role="dialog" aria-modal="true" aria-labelledby="applyTitle">
            {{-- overlay --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            {{-- panel --}}
            <form 
                x-ref="applyForm" 
                method="POST" 
                enctype="multipart/form-data"
                action="{{ route('apply.store', $position) }}"
                @submit.prevent="submit($event)"
                class="relative w-full max-w-3xl mx-4 bg-white rounded-2xl shadow-xl border border-gray-200
                    flex flex-col max-h-[80vh] focus:outline-none">
                @csrf
                <div class="flex items-start justify-between p-5 border-b">
                    <div>
                        <h2 id="applyTitle" class="text-lg font-semibold text-gray-900">
                            Apply — {{ $position->name ?? 'undefined' }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Silahkan untuk mengisi field yang kosong pada formulir berikut.</p>
                    </div>
                    <button class="p-2 rounded-lg hover:bg-gray-100" @click="showApply=false" aria-label="Tutup">✕</button>
                </div>

                <div class="p-5 flex-1 overflow-y-auto">
                    {{-- error summary --}}
                    <template x-if="Object.keys(errors).length">
                        <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
                            Ada beberapa field yang belum valid. Silahkan diperiksa kembali.
                        </div>
                    </template>

                    <div class="flex items-center gap-2 mb-2 mb-4">
                        <h3 class="text-base font-semibold text-gray-800 whitespace-nowrap">
                            Data Pribadi
                        </h3>
                        <div class="flex-1 border-t border-gray-300"></div>
                    </div>

                    {{-- Info badge --}}
                    @auth
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" disabled
                                    class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
                                    placeholder="Nama lengkap"
                                    value="{{ old('name', $user->name) }}" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" disabled
                                    class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
                                    placeholder="email@example.com"
                                    value="{{ old('email', $user->email) }}" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK</label>
                                <input type="text" name="nik" inputmode="numeric" disabled
                                    class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
                                    placeholder="16 digit"
                                    value="{{ old('nik', $user->profile->identity_num ?? '') }}" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="tel" name="no_telp" inputmode="numeric" disabled
                                    class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
                                    placeholder="62xxxxxxxxxx"
                                    value="{{ old('no_telp', $user->profile->phone_number ?? '') }}" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                                <input type="text" name="tpt_lahir" disabled
                                    class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
                                    placeholder="Tempat Lahir"
                                    value="{{ old('tpt_lahir', $user->profile->birthplace ?? '') }}" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" disabled
                                    class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
                                    value="{{ old('tgl_lahir', optional($user->profile?->birthdate)->format('Y-m-d')) }}" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Alamat KTP</label>
                                <textarea rows="2" name="alamat" disabled
                                    class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
                                    placeholder="Alamat KTP">{{ old('alamat', $user->profile->address ?? '') }}</textarea>
                            </div>
                        </div>
                    @endauth

                    {{-- Subjudul dengan garis horizontal --}}
                    <div class="flex items-center gap-2 mt-8 mb-4">
                        <h3 class="text-base font-semibold text-gray-800 whitespace-nowrap">
                            Keahlian (Skills)
                        </h3>
                        <div class="flex-1 border-t border-gray-300"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            // normalize skills to array (same logic as above)
                            if (is_array($position->skills)) {
                                $skillOptions = $position->skills;
                            } elseif ($position->skills && @json_decode($position->skills, true)) {
                                $skillOptions = json_decode($position->skills, true);
                            } elseif ($position->skills) {
                                $skillOptions = preg_split('/\r\n|\r|\n|,/', $position->skills);
                            } else {
                                $skillOptions = [];
                            }
                            $skillOptions = array_values(array_filter(array_map('trim', $skillOptions)));
                        @endphp

                        @if (count($skillOptions))
                            @foreach ($skillOptions as $skill)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox"
                                        name="skills[]"
                                        value="{{ $skill }}"
                                        class="rounded border-gray-300 text-[#009DA9] focus:ring-[#009DA9]"
                                        @checked(is_array(old('skills')) && in_array($skill, old('skills')))>
                                    <span class="text-sm text-gray-700">{{ $skill }}</span>
                                </label>
                            @endforeach
                        @else
                            <p class="text-gray-500 italic col-span-2">Tidak ada skill khusus yang ditentukan untuk posisi ini.</p>
                        @endif

                        {{-- Input teks muncul kalau pilih "Lainnya" --}}
                        <div x-data="{ showOther: false }"
                            x-init="showOther = {{ Js::from(is_array(old('skills')) && in_array('Lainnya', old('skills'))) }}">
                            <label class="flex items-center space-x-2 mb-2">
                                <input type="checkbox"
                                    name="skills[]"
                                    value="Lainnya"
                                    class="rounded border-gray-300 text-[#009DA9] focus:ring-[#009DA9]"
                                    @click="showOther = !showOther"
                                    @checked(is_array(old('skills')) && in_array('Lainnya', old('skills')))>
                                <span class="text-sm text-gray-700">Lainnya</span>
                            </label>

                            <input type="text"
                                name="other_skill"
                                placeholder="Tulis skill lainnya"
                                value="{{ old('other_skill') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-[#009DA9] focus:ring-[#009DA9]"
                                x-show="showOther"
                                x-cloak>
                        </div>
                    </div>

                    {{-- Subjudul dengan garis horizontal --}}
                    <div class="flex items-center gap-2 mt-8 mb-4">
                        <h3 class="text-base font-semibold text-gray-800 whitespace-nowrap">
                            Pendidikan Terakhir
                        </h3>
                        <div class="flex-1 border-t border-gray-300"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="pendidikan" class="block text-sm font-medium text-gray-700">
                                Jenjang Pendidikan
                            </label>
                            <select id="pendidikan" name="pendidikan" required
                                class="mt-1 w-full rounded-lg border-gray-300 bg-white focus:border-[#009DA9] focus:ring-[#009DA9]">
                                <option value="">--- Pilih ---</option>
                                <option value="SMA/Sederajat" {{ old('pendidikan') == 'SMA/Sederajat' ? 'selected' : '' }}>SMA / Sederajat</option>
                                <option value="D1" {{ old('pendidikan') == 'D1' ? 'selected' : '' }}>Diploma 1 (D1)</option>
                                <option value="D2" {{ old('pendidikan') == 'D2' ? 'selected' : '' }}>Diploma 2 (D2)</option>
                                <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>Diploma 3 (D3)</option>
                                <option value="D4" {{ old('pendidikan') == 'D4' ? 'selected' : '' }}>Diploma 4 (D4)</option>
                                <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>Sarjana (S1)</option>
                                <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>Magister (S2)</option>
                                <option value="S3" {{ old('pendidikan') == 'S3' ? 'selected' : '' }}>Doktor (S3)</option>
                            </select>

                            @error('pendidikan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="universitas" class="block text-sm font-medium text-gray-700">Universitas</label>
                            <input id="universitas" name="universitas" type="text" required
                                value="{{ old('universitas') }}"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:border-[#009DA9] focus:ring-[#009DA9]" placeholder="Nama Universitas" />
                            @error('universitas')
                            <p class="text-sm text-red-600 mt-1" x-text="errors.name?.[0]" x-show="errors.universitas"></p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Jurusan</label>
                            <input name="jurusan" type="text" class="mt-1 w-full rounded-lg border-gray-300 focus:border-[#009DA9] focus:ring-[#009DA9]" placeholder="Jurusan" / required>
                            <p class="text-sm text-red-600 mt-1" x-text="errors.jurusan?.[0]" x-show="errors.jurusan"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Tahun Lulus</label>
                            <input name="thn_lulus" type="text" maxlength="4" class="mt-1 w-full rounded-lg border-gray-300 focus:border-[#009DA9] focus:ring-[#009DA9]" placeholder="Tahun Kelulusan" / required>
                            <p class="text-sm text-red-600 mt-1" x-text="errors.thn_lulus?.[0]" x-show="errors.thn_lulus"></p>
                        </div>
                    </div>

                    {{-- Subjudul dengan garis horizontal --}}
                    <div class="flex items-center gap-2 mt-8 mb-4">
                        <h3 class="text-base font-semibold text-gray-800 whitespace-nowrap">
                            Berkas Lamaran
                        </h3>
                        <div class="flex-1 border-t border-gray-300"></div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium">Ekspetasi Gaji</label>
                            <div class="mt-1 flex rounded-lg shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 bg-gray-50 text-gray-500">Rp</span>
                                <input type="number" name="ekspektasi_gaji"
                                    class="flex-1 rounded-r-lg border border-gray-300 focus:ring-2 focus:ring-cyan-500"
                                    placeholder="Contoh: 5000000" inputmode="numeric" autocomplete="number" id="ekspetasi_gaji" :value="old('ekspetasi_gaji')" required>
                            </div>
                            <p class="text-sm text-red-600 mt-1" x-text="errors.ekspetasi_gaji?.[0]" x-show="errors.ekspetasi_gaji"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="cv" class="block text-sm font-medium text-gray-700">CV (PDF • Max 500KB)</label>
                                <input id="cv" name="cv_document" type="file" accept=".pdf"
                                    class="mt-1 block w-full text-sm rounded-lg
                                focus:border-[#009DA9] focus:ring-[#009DA9]
                                file:mr-4 file:py-2 file:px-3
                                file:rounded-lg file:border file:border-[#009DA9]
                                file:text-[#009DA9] file:bg-white
                                hover:file:bg-[#009DA9]/10 hover:file:text-[#007C85]" />
                                @error('cv')
                                <p class="mt-1 text-sm text-red-600" x-text="errors.cv_document?.[0]" x-show="errors.cv_document"></p>
                                @enderror
                            </div>
                            <div>
                                <label for="doc_tambahan" class="block text-sm font-medium text-gray-700">Dokumen Tambahan (PDF • Max 5 MB)</label>
                                <input id="doc_tambahan" name="doc_tambahan" type="file" accept=".pdf"
                                    class="mt-1 block w-full text-sm rounded-lg
                                focus:border-[#009DA9] focus:ring-[#009DA9]
                                file:mr-4 file:py-2 file:px-3
                                file:rounded-lg file:border file:border-[#009DA9]
                                file:text-[#009DA9] file:bg-white
                                hover:file:bg-[#009DA9]/10 hover:file:text-[#007C85]" />
                                @error('doc_tambahan')
                                <p class="mt-1 text-sm text-red-600" x-text="errors.doc_tambahan?.[0]" x-show="errors.doc_tambahan"></p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-t p-5 bg-white rounded-b-2xl border border-gray-300">
                    <!-- kiri -->
                    <div class="flex items-center gap-3">
                        <input id="terms" type="checkbox" x-model="agreed" name="agreed" value="1"
                            class="h-4 w-4 rounded rounded border-gray-300 text-[#009DA9] focus:ring-[#009DA9]">
                        <label for="terms" class="text-sm text-gray-700">
                            Dengan melamar, saya setuju dengan syarat & ketentuan yang berlaku
                        </label>
                    </div>
                    @error('agreed')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <!-- kanan -->
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showApply=false"
                            class="bg-gray-100 rounded-lg px-4 py-2.5 text-gray-700 hover:bg-gray-100">
                            Batal
                        </button>

                        <button type="submit"
                            :disabled="!agreed || loading"
                            class="rounded-lg bg-[#009DA9] hover:bg-[#007C85] disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold px-5 py-2.5 shadow-sm flex items-center justify-center min-w-40">
                            <span x-show="!loading">Submit Lamaran</span>
                            <svg x-show="loading" class="animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        {{-- ===== END MODAL APPLY ===== --}}
    </div>

    {{-- ===== SCRIPT ALPINE ===== --}}
    <script>
        function applyForm({ loginUrl, initialShow = false, isAuth = false }) {
            return {
                showApply: initialShow,
                isAuth,
                loginUrl,
                loading: false,
                errors: {},
                agreed: false,
                async submit(e) {
                    this.loading = true;
                    this.errors = {};
                    const form = e.target;

                    try {
                        const res = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: new FormData(form)
                        });

                        this.loading = false;

                        if (res.ok) {
                            if (typeof Swal !== 'undefined') {
                                await Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Selamat, lamaran anda telah berhasil dikirim!',
                                    confirmButtonColor: '#009DA9'
                                });
                            }
                            form.reset();
                            this.showApply = false;
                            window.location.href = "{{ route('history.index') }}";
                            return;
                        }

                        if (res.status === 401) {
                            window.location.href = loginUrl;
                            return;
                        }

                        if (res.status === 422) {
                            const data = await res.json();
                            this.errors = data.errors || {};
                            if (this.errors.agreed) this.agreed = false;
                            return;
                        }

                        alert('Oops, terjadi kesalahan (' + res.status + '). Coba lagi.');
                    } catch (err) {
                        this.loading = false;
                        alert('Network error. Coba lagi');
                    }
                }
            }
        }
    </script>
</x-guest-layout>