<x-app-admin>
    <div x-data="{ showUploadModal: false, type: '', title: '' }">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-blue-950">Kelola Manual Book</h1>
        </div>

        {{-- Modal Upload Dinamis --}}
        <div x-show="showUploadModal" x-transition.opacity x-cloak
            class="fixed inset-0 backdrop-blur-md bg-black/20 flex items-center justify-center z-50">
            <div @click.away="showUploadModal = false" class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
                <button @click="showUploadModal = false" class="absolute top-3 right-3 text-gray-500">✕</button>

                <h2 class="text-lg font-semibold mb-4 text-blue-950" x-text="'Update ' + title"></h2>

                <form action="{{ route('admin.manualbook.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" :value="type">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File PDF</label>
                        <input type="file" name="manual_book" accept="application/pdf" required
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-2">*Maksimal ukuran file 10MB (Format: PDF)</p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showUploadModal = false"
                            class="bg-gray-200 px-4 py-2 rounded-lg text-sm transition hover:bg-gray-300">Batal</button>
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm rounded-xl border border-zinc-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd" />
                                    <path d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z" />
                                </svg>

                            </span>
                            {{-- Cek status aktif --}}
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ ($setting->manual_book_path ?? false) ? 'bg-green-100 text-green-600' : 'bg-zinc-100 text-zinc-500' }}">
                                {{ ($setting->manual_book_path ?? false) ? 'Aktif' : 'Belum Upload' }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Manual Book Applicant</h3>
                        <p class="text-sm text-gray-500 mb-6">Panduan untuk Applicant tentang cara menggunakan sistem ReQuiz</p>
                    </div>

                    <div class="space-y-4">
                        <div class="text-xs text-gray-400 flex items-center gap-1 italic">
                            <i class="bi bi-file-earmark-pdf"></i>
                            {{-- Menampilkan nama file jika ada --}}
                            {{ ($setting->manual_book_path ?? false) ? basename($setting->manual_book_path) : 'Belum ada file diupload' }}
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if($setting->manual_book_path ?? false)
                            <a href="{{ asset('storage/' . $setting->manual_book_path) }}" target="_blank" class="flex-1 text-center bg-zinc-100 hover:bg-zinc-200 text-zinc-700 py-2 rounded-lg text-sm transition border border-zinc-300">Lihat</a>
                            @endif

                            <button @click="showUploadModal = true; type = 'applicant'; title = 'Manual Book Applicant'"
                                class="flex-1 py-2 rounded-lg text-sm transition {{ ($setting->manual_book_path ?? false) ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-md' : 'bg-white border-2 border-dashed border-blue-600 text-blue-600 hover:bg-blue-50' }}">
                                {{ ($setting->manual_book_path ?? false) ? 'Ganti File' : '+ Upload File' }}
                            </button>

                            @if($setting->manual_book_path ?? false)
                            <form action="{{ route('admin.manualbook.delete', 'applicant') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus file ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 bg-red-50 text-red-600 py-2 rounded-lg text-sm transition border border-red-200 hover:bg-red-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-zinc-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="p-2 bg-purple-100 text-purple-600 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ ($setting->admin_manual_path ?? false) ? 'bg-green-100 text-green-600' : 'bg-zinc-100 text-zinc-500' }}">
                                {{ ($setting->admin_manual_path ?? false) ? 'Aktif' : 'Belum Upload' }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Manual Book Admin</h3>
                        <p class="text-sm text-gray-500 mb-6">Panduan untuk Admin tentang cara menggunakan sistem ReQuiz</p>
                    </div>

                    <div class="space-y-4">
                        <div class="text-xs text-gray-400 flex items-center gap-1 italic">
                            <i class="bi bi-file-earmark-pdf"></i>
                            {{ ($setting->admin_manual_path ?? false) ? basename($setting->admin_manual_path) : 'Belum ada file diupload' }}
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if($setting->admin_manual_path ?? false)
                            <a href="{{ asset('storage/' . $setting->admin_manual_path) }}" target="_blank" class="flex-1 text-center bg-zinc-100 hover:bg-zinc-200 text-zinc-700 py-2 rounded-lg text-sm transition border border-zinc-300">Lihat</a>
                            @endif

                            <button @click="showUploadModal = true; type = 'admin'; title = 'Manual Book Admin'"
                                class="flex-1 py-2 rounded-lg text-sm transition {{ ($setting->admin_manual_path ?? false) ? 'bg-purple-600 hover:bg-purple-700 text-white shadow-md' : 'bg-white border-2 border-dashed border-purple-600 text-purple-600 hover:bg-purple-50' }}">
                                {{ ($setting->admin_manual_path ?? false) ? 'Ganti File' : '+ Upload File' }}
                            </button>

                            @if($setting->admin_manual_path ?? false)
                            <form action="{{ route('admin.manualbook.delete', 'admin') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus file ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 bg-red-50 text-red-600 py-2 rounded-lg text-sm transition border border-red-200 hover:bg-red-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-zinc-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 0 0 1.5v16.5h-.75a.75.75 0 0 0 0 1.5H15v-18a.75.75 0 0 0 0-1.5H3ZM6.75 19.5v-2.25a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1-.75-.75ZM6 6.75A.75.75 0 0 1 6.75 6h.75a.75.75 0 0 1 0 1.5h-.75A.75.75 0 0 1 6 6.75ZM6.75 9a.75.75 0 0 0 0 1.5h.75a.75.75 0 0 0 0-1.5h-.75ZM6 12.75a.75.75 0 0 1 .75-.75h.75a.75.75 0 0 1 0 1.5h-.75a.75.75 0 0 1-.75-.75ZM10.5 6a.75.75 0 0 0 0 1.5h.75a.75.75 0 0 0 0-1.5h-.75Zm-.75 3.75A.75.75 0 0 1 10.5 9h.75a.75.75 0 0 1 0 1.5h-.75a.75.75 0 0 1-.75-.75ZM10.5 12a.75.75 0 0 0 0 1.5h.75a.75.75 0 0 0 0-1.5h-.75ZM16.5 6.75v15h5.25a.75.75 0 0 0 0-1.5H21v-12a.75.75 0 0 0 0-1.5h-4.5Zm1.5 4.5a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Zm.75 2.25a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75v-.008a.75.75 0 0 0-.75-.75h-.008ZM18 17.25a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ ($setting->vendor_manual_path ?? false) ? 'bg-green-100 text-green-600' : 'bg-zinc-100 text-zinc-500' }}">
                                {{ ($setting->vendor_manual_path ?? false) ? 'Aktif' : 'Belum Upload' }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Manual Book Vendor</h3>
                        <p class="text-sm text-gray-500 mb-6">Panduan untuk Vendor tentang cara menggunakan sistem ReQuiz</p>
                    </div>

                    <div class="space-y-4">
                        <div class="text-xs text-gray-400 flex items-center gap-1 italic">
                            <i class="bi bi-file-earmark-pdf"></i>
                            {{ ($setting->vendor_manual_path ?? false) ? basename($setting->vendor_manual_path) : 'Belum ada file diupload' }}
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if($setting->vendor_manual_path ?? false)
                            <a href="{{ asset('storage/' . $setting->vendor_manual_path) }}" target="_blank" class="flex-1 text-center bg-zinc-100 hover:bg-zinc-200 text-zinc-700 py-2 rounded-lg text-sm transition border border-zinc-300">Lihat</a>
                            @endif

                            <button @click="showUploadModal = true; type = 'vendor'; title = 'Manual Book Vendor'"
                                class="flex-1 py-2 rounded-lg text-sm transition {{ ($setting->vendor_manual_path ?? false) ? 'bg-orange-600 hover:bg-orange-700 text-white shadow-md' : 'bg-white border-2 border-dashed border-orange-600 text-orange-600 hover:bg-orange-50' }}">
                                {{ ($setting->vendor_manual_path ?? false) ? 'Ganti File' : '+ Upload File' }}
                            </button>

                            @if($setting->vendor_manual_path ?? false)
                            <form action="{{ route('admin.manualbook.delete', 'vendor') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus file ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 bg-red-50 text-red-600 py-2 rounded-lg text-sm transition border border-red-200 hover:bg-red-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-admin>