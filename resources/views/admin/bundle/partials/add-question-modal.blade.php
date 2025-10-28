{{-- File: resources/views/admin/bundle/partials/add-question-modal.blade.php --}}

<div class="modal fade" id="addQuestionModal-{{ $bundle->id }}" tabindex="-1"
    aria-labelledby="addQuestionModalLabel-{{ $bundle->id }}" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="addQuestionModalLabel-{{ $bundle->id }}">
                    <i class="bi bi-plus-square-dotted me-2"></i>Tambah Soal ke Bundle:
                    <strong>{{ $bundle->name }}</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bundle.questions.add', $bundle) }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Toolbar untuk Filter dan Aksi --}}
                    <div class="p-3 bg-white rounded-3 mb-3 shadow-sm">
                        {{-- BARIS INI DIMODIFIKASI untuk menampung filter kategori --}}
                        <div class="row g-3 align-items-center">
                        {{-- 🔍 Kolom Pencarian Soal --}}
                        <div class="col-12 col-md-5">
                              <input 
                                    type="text" 
                                    class="form-control question-search-input" 
                                    placeholder="Cari soal..." 
                                    data-target-list="#question-list-{{ $bundle->id }}">
                        </div>

                        {{-- ✅ Kolom Pilih Semua --}}
                        <div class="col-12 col-md-3">
                              <div class="d-flex align-items-center justify-content-start justify-content-md-center">
                                    <div class="form-check form-switch">
                                          <input 
                                                class="form-check-input select-all-questions" 
                                                type="checkbox" 
                                                role="switch" 
                                                data-target-list="#question-list-{{ $bundle->id }}">
                                          <label class="form-check-label ms-2">Pilih Semua</label>
                                    </div>
                              </div>
                        </div>

                        {{-- 🏷️ Filter Kategori --}}
                        <div class="col-12 col-md-4">
                              <select 
                                    class="form-select question-category-filter"
                                    data-target-list="#question-list-{{ $bundle->id }}">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $category)
                                          <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                              </select>
                        </div>
                    </div>

                    </div>

                    <div class="question-scroll-area" style="max-height: 45vh; overflow-y: auto;">
                        <div class="list-group question-list" id="question-list-{{ $bundle->id }}">
                            @forelse ($availableQuestions as $question)
                                {{-- LABEL INI DIMODIFIKASI dengan atribut data-category dan badge --}}
                                <label class="list-group-item list-group-item-action"
                                    data-category="{{ $question->category }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div>
                                            <input class="form-check-input me-3" type="checkbox" name="question_ids[]"
                                                value="{{ $question->id }}">
                                            <span class="fw-bold">{{ $question->question }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <span
                                                class="badge bg-secondary text-dark rounded-pill">{{ $question->category }}</span>
                                            <span
                                                class="badge bg-info text-dark rounded-pill">{{ $question->type }}</span>
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="text-center py-5">
                                    <h6 class="text-muted">Tidak ada soal baru untuk ditambahkan</h6>
                                    <p>Semua soal yang tersedia sudah ada di dalam bundle ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <span class="text-muted selection-counter">0 soal terpilih</span>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Tambahkan
                            Soal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
