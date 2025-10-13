  <x-app-admin>
    <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
      <div class="relative flex items-center gap-2 mb-4">
      <a href="{{ route('admin.applicant.seleksi.index') }}" 
        class="text-gray-600 hover:text-gray-900 flex items-center">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </a>

      <h2 class="text-lg font-semibold leading-none m-0">Interview</h2>
    </div>

      {{-- Toolbar --}}
      <div class="flex w-full mb-2 items-end gap-2">
        <form method="GET" class="flex-1 min-w-[220px]">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">
          <div class="relative flex items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                  placeholder="Nama / Email / Jurusan / Posisi..."
                  class="w-full h-10 pl-3 pr-9 border rounded text-sm focus:ring focus:border-blue-500">
            <span class="absolute right-3 text-gray-500">
              <x-search-button/>
            </span>
          </div>
        </form>

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
                onclick="document.getElementById('emailModalInterview').classList.remove('hidden')"
                class="px-3 py-2 border rounded bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center"
                title="Kirim Email">
          <i class="fas fa-envelope"></i>
        </button>

        {{-- Export --}}
        <a href="{{ route('admin.applicant.seleksi.interview.export', request()->query()) }}"
          class="px-3 py-2 border rounded bg-green-600 text-white flex items-center justify-center"
          title="Export">
          <i class="fas fa-file-export"></i>
        </a>

        {{-- Lolos --}}
        <button type="submit" form="bulkActionForm" name="bulk_action" value="lolos"
                class="px-3 py-2 rounded bg-blue-600 text-white">
          Lolos
        </button>

        {{-- Gagal --}}
        <button type="submit" form="bulkActionForm" name="bulk_action" value="tidak_lolos"
                class="px-3 py-2 rounded bg-red-600 text-white">
          Gagal
        </button>
      </div>
    </div>

    {{-- Table --}}
    <form id="bulkActionForm" method="POST" action="{{ route('admin.applicant.seleksi.interview.bulkMark') }}">
      @csrf
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-3 py-2"><input type="checkbox" id="checkAll"></th>
              <th class="px-3 py-2 text-left">Nama Peserta</th>
              <th class="px-3 py-2 text-left">Jurusan</th>
              <th class="px-3 py-2 text-left">Posisi</th>
              <th class="px-3 py-2 text-left">Dokumen</th>
              <th class="px-3 py-2 text-left">Score Quiz</th>
              <th class="px-3 py-2 text-left">Score Praktik</th>
              <th class="px-3 py-2 text-left">Score Interview</th>
              <th class="px-3 py-2 text-left">Potential By</th>
              <th class="px-3 py-2 text-left">Status Email</th>
              <th class="px-3 py-2 text-left">Status</th>
              <th class="px-3 py-2 text-left">Note</th>
              <th class="px-3 py-2 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($applicants as $a)
              @php
                  $myResult = \App\Models\InterviewResult::where('applicant_id', $a->id)
                                ->where('user_id', auth()->id())
                                ->first();
              @endphp
              <tr>
                <td class="px-3 py-2"><input type="checkbox" name="ids[]" value="{{ $a->id }}"></td>
                <td class="px-3 py-2">{{ $a->name }}</td>
                <td class="px-3 py-2">{{ $a->jurusan }}</td>
                <td class="px-3 py-2">{{ $a->position->name ?? '-' }}</td>
                <td class="px-3 py-2 text-center">
                  @if($a->cv_document)
                    <a href="{{ asset('storage/'.$a->cv_document) }}" target="_blank" class="text-blue-600 hover:underline">
                      <i class="fas fa-file-pdf"></i>
                    </a>
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
                <td class="px-3 py-2 text-center">{{ $a->quiz_score ?? '-' }}</td>
                <td class="px-3 py-2 text-center">{{ $a->praktik_score ?? '-' }}</td>
                <td class="px-3 py-2 text-center">
                  {{ $a->interview_avg ? number_format($a->interview_avg, 2) : '-' }}
                </td>
                <td class="px-3 py-2">
                  {{ $a->potential_by ? implode(', ', $a->potential_by) : '-' }}
                </td>

                {{-- Email Status --}}
                <td class="px-3 py-2 text-center">
                  @php
                    $log = $a->latestEmailLog;
                    if ($log && $log->stage !== 'Interview') {
                        $log = null;
                    }
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
                <td class="px-3 py-2">
                  @php
                      $displayStatus = $a->status;
                      if ($a->status === 'Offering') {
                          $displayStatus = 'Lolos Interview';
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
                <td class="px-3 py-2">{{ $a->interview_note ?? '-' }}</td>

                {{-- Aksi --}}
                <td class="px-3 py-2 text-center">
                  <i class="fas fa-pen text-blue-600 cursor-pointer hover:text-blue-800"
                    title="Input Penilaian Interview"
                    onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.remove('hidden')"></i>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="12" class="text-center text-gray-500 py-5">Tidak ada data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </form>

    <div class="mt-3">{{ $applicants->links() }}</div>
  </div>

  {{-- Modal Input Score Interview --}}
  @foreach($applicants as $a)
  @php
      $myResult = \App\Models\InterviewResult::where('applicant_id', $a->id)
                    ->where('user_id', auth()->id())
                    ->first();
  @endphp
  <div id="scoreModal-{{ $a->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h3 class="text-lg font-semibold">Input Score Interview</h3>
        <button type="button"
                onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700">&times;</button>
      </div>

      <form method="POST" action="{{ route('admin.applicant.seleksi.interview.storeScore') }}">
        @csrf
        <input type="hidden" name="applicant_id" value="{{ $a->id }}">

        <div class="mb-3">
          <label class="block text-sm">Poin Kepribadian</label>
          <input type="number" name="poin_kepribadian"
                 value="{{ $myResult->poin_kepribadian ?? '' }}"
                 class="border rounded w-full px-2 py-1" required>
        </div>

        <div class="mb-3">
          <label class="block text-sm">Poin Wawasan</label>
          <input type="number" name="poin_wawasan"
                 value="{{ $myResult->poin_wawasan ?? '' }}"
                 class="border rounded w-full px-2 py-1" required>
        </div>

        <div class="mb-3">
          <label class="block text-sm">Poin Gestur</label>
          <input type="number" name="poin_gestur"
                 value="{{ $myResult->poin_gestur ?? '' }}"
                 class="border rounded w-full px-2 py-1" required>
        </div>

        <div class="mb-3">
          <label class="block text-sm">Poin Cara Bicara</label>
          <input type="number" name="poin_cara_bicara"
                 value="{{ $myResult->poin_cara_bicara ?? '' }}"
                 class="border rounded w-full px-2 py-1" required>
        </div>

        <div class="mb-3">
          <label class="block text-sm">Catatan</label>
          <textarea name="note" class="border rounded w-full px-2 py-1">{{ $myResult->note ?? '' }}</textarea>
        </div>

        <div class="mb-3 flex items-center gap-2">
          <input type="checkbox" name="potencial"
                 {{ ($myResult && $myResult->potencial) ? 'checked' : '' }}
                 class="rounded">
          <label class="text-sm">Potencial</label>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" 
                  onclick="document.getElementById('scoreModal-{{ $a->id }}').classList.add('hidden')"
                  class="px-3 py-1 border rounded">Batal</button>
          <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  @endforeach

  {{-- ✅ Modal Email Interview --}}
  <div id="emailModalInterview" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-5">
      <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h3 class="text-lg font-semibold">Kirim Email Peserta Seleksi Interview</h3>
        <button type="button" onclick="document.getElementById('emailModalInterview').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700">&times;</button>
      </div>

      {{-- Tabs --}}
      <div class="border-b mb-4 flex">
        <button type="button" data-tab="tabLolosInterview"
                class="tab-btn-int px-4 py-2 border-b-2 border-blue-600 text-blue-600">Lolos</button>
        <button type="button" data-tab="tabTidakLolosInterview"
                class="tab-btn-int px-4 py-2 border-b-2 border-transparent">Tidak Lolos</button>
        <button type="button" data-tab="tabTerpilihInterview"
                class="tab-btn-int px-4 py-2 border-b-2 border-transparent">Terpilih</button>
      </div>

      {{-- Tab Lolos --}}
      <div id="tabLolosInterview" class="tab-content-int">
        <form method="POST" action="{{ route('admin.applicant.seleksi.interview.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateLolosInterview" class="rounded">
            <label for="useTemplateLolosInterview" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectLolosInterview" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageLolosInterview" type="hidden" name="message">
            <trix-editor input="messageLolosInterview" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Lampiran</label>
            <input type="file" name="attachments[]" multiple>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModalInterview').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Tidak Lolos --}}
      <div id="tabTidakLolosInterview" class="tab-content-int hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.interview.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="tidak_lolos">
          <input type="hidden" name="batch" value="{{ $batchId }}">
          <input type="hidden" name="position" value="{{ $positionId }}">

          <div class="mb-3 flex items-center gap-2">
            <input type="checkbox" id="useTemplateTidakLolosInterview" class="rounded">
            <label for="useTemplateTidakLolosInterview" class="text-sm font-medium">Gunakan template</label>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" id="subjectTidakLolosInterview" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageTidakLolosInterview" type="hidden" name="message">
            <trix-editor input="messageTidakLolosInterview" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModalInterview').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>

      {{-- Tab Terpilih --}}
      <div id="tabTerpilihInterview" class="tab-content-int hidden">
        <form method="POST" action="{{ route('admin.applicant.seleksi.interview.sendEmail') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" value="selected">
          <input type="hidden" name="ids" id="selectedIdsInterview">

          <div class="mb-3">
            <label class="block text-sm font-medium">Subjek</label>
            <input type="text" name="subject" class="border rounded w-full px-2 py-1" required>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Isi Email</label>
            <input id="messageSelectedInterview" type="hidden" name="message">
            <trix-editor input="messageSelectedInterview" class="trix-content border rounded w-full"></trix-editor>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('emailModalInterview').classList.add('hidden')"
                    class="px-3 py-1 border rounded">Batal</button>
            <button type="submit" onclick="setSelectedIdsInterview()" class="px-3 py-1 rounded bg-blue-600 text-white">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  {{-- Script untuk Modal Email Interview --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.umd.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.8/trix.min.css"/>

  <script>
    // Template Lolos Interview
    document.getElementById('useTemplateLolosInterview').addEventListener('change', function() {
      if (this.checked) {
        document.getElementById('subjectLolosInterview').value = "INFORMASI HASIL SELEKSI INTERVIEW - PLN ICON PLUS";
        document.querySelector("trix-editor[input=messageLolosInterview]").editor.loadHTML(
          `Selamat! Anda dinyatakan <strong>LOLOS</strong> pada tahap 'Interview' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
          Tahap selanjutnya akan diinformasikan melalui portal seleksi.<br><br>
          Terima kasih atas partisipasi Anda.`
        );
      } else {
        document.getElementById('subjectLolosInterview').value = "";
        document.querySelector("trix-editor[input=messageLolosInterview]").editor.loadHTML("");
      }
    });

    // Template Tidak Lolos Interview
    document.getElementById('useTemplateTidakLolosInterview').addEventListener('change', function() {
      if (this.checked) {
        document.getElementById('subjectTidakLolosInterview').value = "INFORMASI HASIL SELEKSI INTERVIEW - PLN ICON PLUS";
        document.querySelector("trix-editor[input=messageTidakLolosInterview]").editor.loadHTML(
          `Mohon maaf, Anda dinyatakan <strong>TIDAK LOLOS</strong> pada tahap 'Interview' TAD/OUTSOURCING PLN ICON PLUS.<br><br>
          Terima kasih telah berpartisipasi.<br><br>
          Hormat Kami,<br>
          Recruitment Team`
        );
      } else {
        document.getElementById('subjectTidakLolosInterview').value = "";
        document.querySelector("trix-editor[input=messageTidakLolosInterview]").editor.loadHTML("");
      }
    });

    // Tab switcher
    document.querySelectorAll('.tab-btn-int').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn-int').forEach(b => b.classList.remove('border-blue-600','text-blue-600'));
        this.classList.add('border-blue-600','text-blue-600');
        const target = this.dataset.tab;
        document.querySelectorAll('.tab-content-int').forEach(c => c.classList.add('hidden'));
        document.getElementById(target).classList.remove('hidden');
      });
    });

    // Set selected IDs for "Terpilih"
    function setSelectedIdsInterview() {
      let ids = [];
      document.querySelectorAll('input[name="ids[]"]:checked').forEach(cb => ids.push(cb.value));
      if (ids.length === 0) {
        alert("Silakan pilih peserta terlebih dahulu.");
        event.preventDefault();
        return false;
      }
      document.getElementById('selectedIdsInterview').value = ids.join(',');
    }
  </script>


  {{-- Font Awesome --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</x-app-admin>
