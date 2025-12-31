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
                                <i class="bi bi-people-fill text-xl"></i>
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
                                <i class="bi bi-shield-lock-fill text-xl"></i>
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
            </div>
        </div>
    </div>
</x-app-admin>