<x-app-admin>
  <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">Seleksi Tes Tulis</h2>
    </div>

    {{-- Toolbar --}}
    <div class="flex justify-between mb-3">
      <form method="GET" class="flex gap-2 flex-1">
        <input type="hidden" name="batch" value="{{ $batchId }}">
        <input type="hidden" name="position" value="{{ $positionId }}">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama/email/jurusan..."
               class="border rounded px-3 py-2 flex-1 text-sm">
      </form>

      {{-- Tombol Aksi --}}
      <div class="flex gap-2">
        {{-- Filter --}}
        <button type="button"
                onclick="document.getElementById('filterModal').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-gray-600 text-white flex items-center justify-center"
                title="Filter">
          <i class="fas fa-filter"></i>
        </button>

        {{-- Email --}}
        <button type="button"
                onclick="document.getElementById('emailModal').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center"
                title="Kirim Email">
          <i class="fas fa-envelope"></i>
        </button>

        {{-- Export --}}
        <a href="{{ route('admin.applicant.seleksi.tes_tulis.export', request()->query()) }}"
          class="px-3 py-2 border rounded bg-green-600 text-white flex items-center justify-center"
          title="Export">
          <i class="fas fa-file-export"></i>
        </a>

        {{-- Lolos --}}
        <button type="button"
                onclick="openConfirmModal('lolos')"
                class="px-3 py-2 rounded bg-blue-600 text-white">
          Lolos
        </button>

        {{-- Gagal --}}
        <button type="button"
                onclick="openConfirmModal('tidak_lolos')"
                class="px-3 py-2 rounded bg-red-600 text-white">
          Gagal
        </button>
      </div>
    </div>

    {{-- Table --}}
    <form id="bulkActionForm" method="POST" action="{{ route('admin.applicant.seleksi.tes_tulis.bulkMark') }}">
      @csrf
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-3 py-2"><input type="checkbox" id="checkAll"></th>
              <th class="px-3 py-2 text-left">Nama Peserta</th>
              <th class="px-3 py-2 text-left">Section 1</th>
              <th class="px-3 py-2 text-left">Section 2</th>
              <th class="px-3 py-2 text-left">Section 3</th>
              <th class="px-3 py-2 text-left">Section 4</th>
              <th class="px-3 py-2 text-left">Section 5</th>
              <th class="px-3 py-2 text-left">Total Nilai</th>
              <th class="px-3 py-2 text-left">Status</th>
              <th class="px-3 py-2 text-left">Email</th>
              <th class="px-3 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($applicants as $a)
              <tr>
                <td class="px-3 py-2">
                  <input type="checkbox" name="ids[]" value="{{ $a->id }}">
                </td>
                <td class="px-3 py-2">{{ $a->name }}</td>

                {{-- Loop 5 section --}}
                @for ($i = 1; $i <= 5; $i++)
                  @php
                    // Ambil section sesuai urutan (misal pakai field 'order' di test_sections)
                    $sectionResult = $a->latestTestResult?->sectionResults
                                        ->firstWhere('testSection.order', $i);

                    // Jika tidak ada kolom 'order', bisa pakai id section
                    // $sectionResult = $a->latestTestResult?->sectionResults
                    //                     ->firstWhere('test_section_id', $i);
                  @endphp
                  <td class="px-3 py-2">
                    {{ $sectionResult?->score ?? '-' }}
                  </td>
                @endfor


                {{-- Total nilai --}}
                <td class="px-3 py-2">
                  {{ $a->latestTestResult?->score ?? '-' }}
                </td>

                {{-- Status --}}
                <td class="px-3 py-2">
                  @php
                      $displayStatus = $a->status;
                      if ($a->status === 'Technical Test') {
                          $displayStatus = 'Lolos Tes Tulis';
                      }
                      $isLolos = \Illuminate\Support\Str::startsWith($displayStatus, 'Lolos');
                      $isTidak = \Illuminate\Support\Str::startsWith($displayStatus, 'Tidak Lolos');
                      $badgeClass = $isLolos
                          ? 'bg-green-100 text-green-700'
                          : ($isTidak ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                  @endphp

                  <span class="px-2 py-1 text-xs rounded {{ $badgeClass }}">
                    {{ $displayStatus }}
                  </span>
                </td>

                {{-- Email --}}
                <td class="px-3 py-2 text-center">
                  @php
                    $log = $a->latestEmailLog;
                    if ($log && $log->stage !== 'Tes Tulis') $log = null;
                  @endphp
                  @if($log)
                    @if($log->success)
                      <i class="fas fa-check-circle text-green-500" title="Terkirim"></i>
                    @else
                      <i class="fas fa-times-circle text-red-500" title="Gagal: {{ $log->error }}"></i>
                    @endif
                  @else
                    <i class="fas fa-minus-circle text-gray-400" title="Belum dikirim"></i>
                  @endif
                </td>

                {{-- Action --}}
                <td class="px-3 py-2 text-center flex gap-2 justify-center">
                  {{-- Lihat Detail --}}
                  <i class="fas fa-eye text-blue-600 cursor-pointer hover:text-blue-800"
                    title="Lihat Detail"
                    onclick="document.getElementById('detailModal-{{ $a->id }}').classList.remove('hidden')"></i>

                  {{-- Tombol Pensil (selalu tampil) --}}
                  <i class="fas fa-pencil-alt text-yellow-600 cursor-pointer hover:text-yellow-800"
                    title="Nilai Essay"
                    onclick="document.getElementById('essayModal-{{ $a->id }}').classList.remove('hidden')"></i>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center text-gray-500 py-5">Tidak ada data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </form>


    <div class="mt-3">{{ $applicants->links() }}</div>
  </div>

  {{-- Modal Detail per applicant --}}
  @foreach($applicants as $a)
  <div id="detailModal-{{ $a->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl p-6 overflow-y-auto max-h-[90vh]">
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-xl font-semibold text-gray-800">Detail Applicant</h3>
        <button type="button"
                onclick="document.getElementById('detailModal-{{ $a->id }}').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div><p class="text-gray-500 font-medium">Nama</p><p class="text-gray-900">{{ $a->name }}</p></div>
        <div><p class="text-gray-500 font-medium">Email</p><p class="text-gray-900">{{ $a->email }}</p></div>
        <div><p class="text-gray-500 font-medium">Jurusan</p><p class="text-gray-900">{{ $a->jurusan }}</p></div>
        <div><p class="text-gray-500 font-medium">Posisi</p><p class="text-gray-900">{{ $a->position->name ?? '-' }}</p></div>
        <div><p class="text-gray-500 font-medium">Batch</p><p class="text-gray-900">{{ $a->batch->name ?? '-' }}</p></div>
        <div><p class="text-gray-500 font-medium">Umur</p><p class="text-gray-900">{{ $a->age ?? '-' }}</p></div>
        <div><p class="text-gray-500 font-medium">Nilai Tes</p><p class="text-gray-900">{{ $a->test_score ?? '-' }}</p></div>
        <div>
          <p class="text-gray-500 font-medium">Status Seleksi</p>
          @php
            $statusColor = str_contains($a->status, 'Tidak') ? 'bg-red-100 text-red-700' 
                          : (str_contains($a->status, 'Lolos') ? 'bg-green-100 text-green-700' 
                          : 'bg-yellow-100 text-yellow-700');
          @endphp
          <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColor }}">
            {{ $a->status }}
          </span>
        </div>
      </div>

      <div class="mt-6 text-right">
        <button type="button"
                onclick="document.getElementById('detailModal-{{ $a->id }}').classList.add('hidden')"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Tutup</button>
      </div>
    </div>
  </div>
  @endforeach

  {{-- Modal Penilaian Essay --}}
  @foreach($applicants as $a)
    <div id="essayModal-{{ $a->id }}"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 overflow-y-auto max-h-[90vh]">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
          <h3 class="text-lg font-semibold">Penilaian Essay - {{ $a->name }}</h3>
          <button type="button"
                  onclick="document.getElementById('essayModal-{{ $a->id }}').classList.add('hidden')"
                  class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        @php
          $essaySections = collect($a->latestTestResult?->sectionResults ?? [])
                            ->filter(fn($s) => $s->testSection && str_contains(strtolower($s->testSection->name), 'essay'))
                            ->sortBy('testSection.order'); // kalau ada field order
        @endphp

        @if($essaySections->isEmpty())
          <p class="text-gray-600">Peserta ini belum mengerjakan essay.</p>
        @else
          <form method="POST" action="{{ route('admin.applicant.seleksi.tes_tulis.scoreEssay') }}">
            @csrf

            @foreach($essaySections as $sectionResult)
              {{-- Kirim id section --}}
              <input type="hidden" name="section_result_ids[]" value="{{ $sectionResult->id }}">

              <div class="mb-6">
                <h4 class="text-md font-semibold mb-2">
                  Section: {{ $sectionResult->testSection->name }}
                </h4>

                @foreach($sectionResult->answers as $ans)
                  <div class="mb-4 border-b pb-2">
                    <p class="font-medium">Soal:</p>
                    <p class="text-gray-700 mb-1">{{ $ans->question->question }}</p>
                    <p class="text-sm text-gray-500">Jawaban Peserta:</p>
                    <p class="text-gray-900 border px-2 py-1 rounded bg-gray-50">{{ $ans->answer }}</p>

                    <label class="block mt-2 text-sm">Nilai Jawaban</label>
                    <input type="number" 
                          name="answer_scores[{{ $sectionResult->id }}][{{ $ans->id }}]"
                          value="{{ $ans->score }}"
                          min="0" max="100"
                          class="border rounded px-2 py-1 w-24">
                  </div>
                @endforeach
              </div>
            @endforeach

            <div class="flex justify-end gap-2 mt-4">
              <button type="button"
                      onclick="document.getElementById('essayModal-{{ $a->id }}').classList.add('hidden')"
                      class="px-3 py-1 border rounded">Batal</button>
              <button type="submit"
                      class="px-3 py-1 bg-blue-600 text-white rounded">Simpan Semua</button>
            </div>
          </form>
        @endif
      </div>
    </div>
  @endforeach

  {{-- Modal Filter --}}
  <div id="filterModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-lg font-semibold">Filter Data</h3>
        <button type="button"
                onclick="document.getElementById('filterModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>

      <form method="GET" action="{{ route('admin.applicant.seleksi.tes_tulis.index') }}" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Batch</label>
          <select name="batch" class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Semua Batch</option>
            @foreach($batches as $b)
              <option value="{{ $b->id }}" {{ (string)$batchId === (string)$b->id ? 'selected' : '' }}>
                {{ $b->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium">Posisi</label>
          <select name="position" class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Semua Posisi</option>
            @foreach($positions as $p)
              <option value="{{ $p->id }}" {{ $positionId == $p->id ? 'selected' : '' }}>
                {{ $p->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium">Status Tes</label>
          <select name="status" class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Semua Status</option>
            <option value="Tes Tulis" {{ request('status') === 'Tes Tulis' ? 'selected' : '' }}>Tes Tulis</option>
            <option value="Technical Test" {{ request('status') === 'Technical Test' ? 'selected' : '' }}>Lolos Tes Tulis</option>
            <option value="Tidak Lolos Tes Tulis" {{ request('status') === 'Tidak Lolos Tes Tulis' ? 'selected' : '' }}>Tidak Lolos Tes Tulis</option>
          </select>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" 
                  onclick="document.getElementById('filterModal').classList.add('hidden')"
                  class="px-3 py-1 border rounded">Batal</button>
          <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Terapkan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Modal Konfirmasi --}}
  <div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-lg font-semibold">Konfirmasi Aksi</h3>
        <button type="button" 
                onclick="document.getElementById('confirmModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>
      <p id="confirmMessage" class="text-gray-700 mb-6">Apakah Anda yakin?</p>
      <div class="flex justify-end gap-2">
        <button type="button" 
                onclick="document.getElementById('confirmModal').classList.add('hidden')"
                class="px-4 py-2 border rounded">Batal</button>
        <button type="button" id="confirmYesBtn"
                class="px-4 py-2 bg-blue-600 text-white rounded">Ya, Lanjutkan</button>
      </div>
    </div>
  </div>

  {{-- Modal Email --}}
  <div id="emailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-5">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h3 class="text-lg font-semibold">Kirim Email Peserta Tes Tulis</h3>
        <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700">&times;</button>
      </div>

      <div class="border-b mb-4 flex">
        <button type="button" data-tab="tabLolos" class="tab-btn px-4 py-2 border-b-2 border-blue-600 text-blue-600">Lolos</button>
        <button type="button" data-tab="tabTidakLolos" class="tab-btn px-4 py-2 border-b-2 border-transparent">Tidak Lolos</button>
        <button type="button" data-tab="tabTerpilih" class="tab-btn px-4 py-2 border-b-2 border-transparent">Terpilih</button>
      </div>

      {{-- Tab Lolos --}}
      <div id="tabLolos" class="tab-content">
        <form method="POST" action="{{ route('admin.applicant.seleksi.tes_tulis.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          {{-- Gunakan template --}}
          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateLolosTesTulis" class="rounded">
            <label for="useTemplateLolosTesTulis" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectLolosTesTulis" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageLolosTesTulis" type="hidden" name="message">
            <trix-editor input="messageLolosTesTulis" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')" class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Tidak Lolos --}}
      <div id="tabTidakLolos" class="tab-content hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.tes_tulis.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="tidak_lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          {{-- Gunakan template --}}
          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateTidakLolosTesTulis" class="rounded">
            <label for="useTemplateTidakLolosTesTulis" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectTidakLolosTesTulis" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageTidakLolosTesTulis" type="hidden" name="message">
            <trix-editor input="messageTidakLolosTesTulis" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')" class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Terpilih --}}
      <div id="tabTerpilih" class="tab-content hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.tes_tulis.sendEmail') }}">
          @csrf
          <input type="hidden" name="type" value="selected">
          <input type="hidden" name="ids" id="selectedIds">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageSelected" type="hidden" name="message">
            <trix-editor input="messageSelected" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" 
                    onclick="document.getElementById('emailModal').classList.add('hidden')" 
                    class="px-3 py-1 border rounded">Batal</button>

            {{-- ✅ type=button + JS submit manual --}}
            <button type="button" 
                    class="px-3 py-1 rounded bg-blue-600 text-white"
                    onclick="if(setSelectedIds() !== false) this.closest('form').submit();">
              Kirim
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Scripts --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.umd.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.min.css"/>

    <script>
    // 🔹 Set selected IDs untuk tab "Terpilih"
    function setSelectedIds() {
      let ids = [];
      document.querySelectorAll('input[name="ids[]"]:checked')
        .forEach(cb => ids.push(cb.value));

      if (ids.length === 0) { 
        alert("Silakan pilih peserta terlebih dahulu."); 
        return false; // ❌ jangan submit
      }

      document.getElementById('selectedIds').value = ids.join(',');
      return true; // ✅ lanjut submit
    }

    // 🔹 Check All
    document.getElementById('checkAll').addEventListener('change', function(e){
      document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = e.target.checked);
    });

    // 🔹 Tab switcher (Lolos / Tidak Lolos / Terpilih)
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('border-blue-600','text-blue-600'));
        this.classList.add('border-blue-600','text-blue-600');
        const target = this.dataset.tab;
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById(target).classList.remove('hidden');
      });
    });

    // 🔹 Modal Konfirmasi (bulk action)
    let selectedAction = null;
    function openConfirmModal(action) {
      selectedAction = action;
      const msg = action === 'lolos' 
        ? "Apakah Anda yakin ingin meloloskan peserta yang dipilih?" 
        : "Apakah Anda yakin ingin menggagalkan peserta yang dipilih?";
      document.getElementById('confirmMessage').innerText = msg;
      document.getElementById('confirmModal').classList.remove('hidden');
    }

    document.getElementById('confirmYesBtn').addEventListener('click', function() {
      if (selectedAction) {
        const form = document.getElementById('bulkActionForm');
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'bulk_action';
        input.value = selectedAction;
        form.appendChild(input);
        form.submit();
      }
    });

    // 🔹 Gunakan template email untuk peserta LOLOS Tes Tulis
    const useTemplateLolos = document.getElementById('useTemplateLolosTesTulis');
    if (useTemplateLolos) {
      useTemplateLolos.addEventListener('change', function() {
        if (this.checked) {
          document.getElementById('subjectLolosTesTulis').value = "INFORMASI HASIL TES TULIS - PLN ICON PLUS";
          document.querySelector("trix-editor[input=messageLolosTesTulis]").editor.loadHTML(
            `Selamat! Anda dinyatakan <strong>LOLOS</strong> pada tahap 'Tes Tulis' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
            Untuk tahap selanjutnya, mohon untuk mempersiapkan diri mengikuti Technical Test.<br>
            Jadwal dan detail pelaksanaan Technical Test akan disampaikan pada laman Proses Seleksi.<br>
            Silakan lakukan pengecekan secara berkala pada laman Proses Seleksi.<br><br>
            Terima kasih dan semoga sukses.`
          );
        } else {
          document.getElementById('subjectLolosTesTulis').value = "";
          document.querySelector("trix-editor[input=messageLolosTesTulis]").editor.loadHTML("");
        }
      });
    }

    // 🔹 Gunakan template email untuk peserta TIDAK LOLOS Tes Tulis
    const useTemplateTidakLolos = document.getElementById('useTemplateTidakLolosTesTulis');
    if (useTemplateTidakLolos) {
      useTemplateTidakLolos.addEventListener('change', function() {
        if (this.checked) {
          document.getElementById('subjectTidakLolosTesTulis').value = "INFORMASI HASIL TES TULIS - PLN ICON PLUS";
          document.querySelector("trix-editor[input=messageTidakLolosTesTulis]").editor.loadHTML(
            `Mohon maaf, Anda dinyatakan <strong>TIDAK LOLOS</strong> pada tahap 'Tes Tulis' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
            Kami berterima kasih atas partisipasi Anda dalam proses seleksi ini.<br>
            Semoga sukses di kesempatan berikutnya.<br><br>
            Hormat kami,<br>
            Recruitment Team`
          );
        } else {
          document.getElementById('subjectTidakLolosTesTulis').value = "";
          document.querySelector("trix-editor[input=messageTidakLolosTesTulis]").editor.loadHTML("");
        }
      });
    }
  </script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</x-app-admin>
