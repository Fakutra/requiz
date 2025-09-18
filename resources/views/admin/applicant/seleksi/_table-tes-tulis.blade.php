{{-- resources/views/admin/applicant/seleksi/_table-tes-tulis.blade.php --}}
@php
  use Illuminate\Support\Facades\Storage;
@endphp

<div x-data="stageSeleksi()" x-init="init()" x-cloak>
  <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
    {{-- Filter + Aksi --}}
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
      <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-2">
        <input type="hidden" name="batch" value="{{ request('batch') }}">

        <div>
          <label class="block text-xs text-gray-500 mb-1">Cari</label>
          <input type="text" name="search" value="{{ request('search') }}"
                 class="border rounded px-3 py-2 w-60" placeholder="Search...">
        </div>

        <div>
          <label class="block text-xs text-gray-500 mb-1">Jurusan</label>
          <select name="jurusan" class="border rounded px-3 py-2 w-56">
            <option value="">Semua Jurusan</option>
            @foreach ($allJurusan as $jur)
              <option value="{{ $jur }}" {{ request('jurusan') == $jur ? 'selected' : '' }}>{{ $jur }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs text-gray-500 mb-1">Status (tahap ini)</label>
          <select name="status" class="border rounded px-3 py-2 w-56">
            <option value="">Semua Status</option>
            <option value="{{ $stage }}" {{ request('status') == $stage ? 'selected' : '' }}>
              {{ $stage }} (sedang tahap ini)
            </option>
            <option value="__NEXT__" {{ request('status') == '__NEXT__' ? 'selected' : '' }}>
              Lolos {{ $stage }} → {{ $nextStage ?? '—' }}
            </option>
            <option value="__FAILED__" {{ request('status') == '__FAILED__' ? 'selected' : '' }}>
              Tidak Lolos {{ $stage }}{{ isset($failEnum) ? ' ('.$failEnum.')' : '' }}
            </option>
          </select>
        </div>

        <div>
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Filter
          </button>
        </div>
      </form>

      <div class="flex items-center gap-2">
        <button type="button" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                @click="submitStatus('lolos')">Lolos</button>
        <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                @click="submitStatus('tidak_lolos')">Gagal</button>
        <button type="button" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded"
                @click="openEmailModal()">Email</button>
      </div>
    </div>

    {{-- Form UPDATE STATUS --}}
    <form id="statusForm" method="POST" action="{{ route('admin.applicant.seleksi.update-status') }}">
      @csrf
      <input type="hidden" name="stage" value="{{ $stage }}">
      <div id="statusInputs"></div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">
          <thead class="bg-gray-100">
          <tr>
            <th class="p-3 border-b w-10"><input type="checkbox" id="selectAll"></th>
            <th class="p-3 border-b">Nama Peserta</th>
            <th class="p-3 border-b">Email</th>
            <th class="p-3 border-b">Jurusan</th>
            <th class="p-3 border-b">Posisi Yang Dilamar</th>
            <th class="p-3 border-b text-center">Score Quiz</th>
            <th class="p-3 border-b">Status Seleksi</th>
            <th class="p-3 border-b text-center">Action</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($applicants as $applicant)
            @php
              $state = 'other';
              $badge = 'bg-gray-100 text-gray-800 border border-gray-200';
              $text  = $stage;

              if ($applicant->status === $stage) {
                  $state = 'current';
              } elseif (($failEnum ?? null) && $applicant->status === $failEnum) {
                  $state = 'gagal';   $text  = $failEnum;  $badge = 'bg-red-50 text-red-700 border border-red-200';
              } elseif ((($nextStage ?? null) && $applicant->status === $nextStage)) {
                  $state = 'lolos';   $text  = 'Lolos '.$stage; $badge = 'bg-green-50 text-green-700 border border-green-200';
              }

              $state = $applicant->_stage_state  ?? $state;
              $text  = $applicant->_stage_status ?? $text;
              $badge = $applicant->_stage_badge  ?? $badge;

              $cvUrl = $applicant->cv_document ? Storage::url($applicant->cv_document) : null;
              $score = $applicant->_quiz_score ?? $applicant->quiz_score ?? null;
            @endphp

            <tr class="hover:bg-gray-50">
              <td class="p-3 border-b align-top">
                <input type="checkbox"
                       class="applicant-checkbox"
                       name="selected_applicants[]"
                       value="{{ $applicant->id }}"
                       data-email="{{ $applicant->email }}"
                       data-stage-state="{{ $state }}"
                       data-name="{{ $applicant->name }}"
                       data-position="{{ $applicant->position->name ?? '-' }}">
              </td>
              <td class="p-3 border-b align-top"><div class="font-medium">{{ $applicant->name }}</div></td>
              <td class="p-3 border-b align-top">{{ $applicant->email }}</td>
              <td class="p-3 border-b align-top">{{ $applicant->jurusan }}</td>
              <td class="p-3 border-b align-top">{{ $applicant->position->name ?? '-' }}</td>
              <td class="p-3 border-b align-top text-center">
                {{ isset($score) ? (is_numeric($score) ? number_format($score,0) : $score) : '-' }}
              </td>
              <td class="p-3 border-b align-top">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">
                  {{ $text }}
                </span>
              </td>
              <td class="p-3 border-b align-top text-center">
                <a href="#"
                   @click.prevent="openEditModal(@js([
                       'id'          => $applicant->id,
                       'name'        => $applicant->name,
                       'email'       => $applicant->email,
                       'nik'         => $applicant->nik,
                       'no_telp'     => $applicant->no_telp,
                       'tpt_lahir'   => $applicant->tpt_lahir,
                       'tgl_lahir'   => \Illuminate\Support\Carbon::parse($applicant->tgl_lahir)->format('Y-m-d'),
                       'alamat'      => $applicant->alamat,
                       'pendidikan'  => $applicant->pendidikan,
                       'universitas' => $applicant->universitas,
                       'jurusan'     => $applicant->jurusan,
                       'thn_lulus'   => $applicant->thn_lulus,
                       'position_id' => $applicant->position_id,
                       'status'      => $applicant->status,
                   ]))"
                   class="inline-flex p-2 rounded hover:bg-orange-50" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-9.9 9.9a1 1 0 01-.464.263l-3.243.81a.5.5 0 01-.606-.606l.81-3.243a1 1 0 01.263-.464l9.9-9.9z"/>
                    <path d="M12.172 5l2.828 2.828"/>
                  </svg>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="p-6 text-center text-gray-500">Tidak ada data untuk kriteria ini.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        {{ $applicants->withQueryString()->links() }}
      </div>
    </form>
  </div>

  {{-- MODAL: Edit Applicant (sama seperti di default) --}}
  <div x-show="editModalOpen" x-transition.opacity
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg" @click.away="closeEditModal()">
      <div class="flex items-center justify-between px-4 py-3 border-b">
        <h3 class="font-semibold">Edit Data Pelamar</h3>
        <button class="text-gray-500 hover:text-gray-700" @click="closeEditModal()">✕</button>
      </div>
      <form :action="updateUrl()" method="POST" enctype="multipart/form-data" class="p-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="text-xs text-gray-600">Nama</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" x-model="form.name" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" x-model="form.email" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">NIK</label>
            <input type="text" name="nik" class="w-full border rounded px-3 py-2" x-model="form.nik" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">No. Telp</label>
            <input type="text" name="no_telp" class="w-full border rounded px-3 py-2" x-model="form.no_telp" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">Tempat Lahir</label>
            <input type="text" name="tpt_lahir" class="w-full border rounded px-3 py-2" x-model="form.tpt_lahir" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" class="w-full border rounded px-3 py-2" x-model="form.tgl_lahir" required>
          </div>
          <div class="md:col-span-2">
            <label class="text-xs text-gray-600">Alamat</label>
            <input type="text" name="alamat" class="w-full border rounded px-3 py-2" x-model="form.alamat" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">Pendidikan</label>
            <select name="pendidikan" class="w-full border rounded px-3 py-2" x-model="form.pendidikan" required>
              <option value="SMA/Sederajat">SMA/Sederajat</option>
              <option value="Diploma">Diploma</option>
              <option value="S1">S1</option>
              <option value="S2">S2</option>
              <option value="S3">S3</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-gray-600">Universitas</label>
            <input type="text" name="universitas" class="w-full border rounded px-3 py-2" x-model="form.universitas" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">Jurusan</label>
            <input type="text" name="jurusan" class="w-full border rounded px-3 py-2" x-model="form.jurusan" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">Tahun Lulus</label>
            <input type="text" name="thn_lulus" class="w-full border rounded px-3 py-2" x-model="form.thn_lulus" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">Posisi</label>
            <select name="position_id" class="w-full border rounded px-3 py-2" x-model="form.position_id" required>
              @foreach($positions as $pos)
                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="text-xs text-gray-600">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2" x-model="form.status" required>
              <option>Seleksi Administrasi</option>
              <option>Tes Tulis</option>
              <option>Technical Test</option>
              <option>Interview</option>
              <option>Offering</option>
              <option>Tidak Lolos Seleksi Administrasi</option>
              <option>Tidak Lolos Seleksi Tes Tulis</option>
              <option>Tidak Lolos Technical Test</option>
              <option>Tidak Lolos interview</option>
              <option>Menerima Offering</option>
              <option>Menolak Offering</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="text-xs text-gray-600">Ganti CV (PDF, max 3MB)</label>
            <input type="file" name="cv_document" accept="application/pdf,.pdf" class="w-full border rounded px-3 py-2">
          </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
          <button type="button" class="px-4 py-2 rounded border" @click="closeEditModal()">Batal</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL: Kirim Email --}}
  <div x-show="emailModalOpen" x-transition.opacity
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg" @click.away="closeEmailModal()">
      <div class="flex items-center justify-between px-4 py-3 border-b">
        <h3 class="font-semibold">Kirim Email Hasil {{ $stage }}</h3>
        <button class="text-gray-500 hover:text-gray-700" @click="closeEmailModal()">✕</button>
      </div>
      <form method="POST" action="{{ route('admin.applicant.seleksi.sendEmail') }}"
            enctype="multipart/form-data" class="p-4" @submit="validateAndSubmit">
        @csrf
        <input type="hidden" name="stage" value="{{ $stage }}">
        <input type="hidden" x-ref="recipientIds" name="recipient_ids">
        <input type="hidden" x-ref="recipients"   name="recipients">

        <div class="space-y-4">
          <div>
            <label class="text-xs text-gray-600">Penerima</label>
            <textarea class="w-full border rounded px-3 py-2 text-sm" rows="3"
                      x-text="selectedEmails.join(', ')" readonly></textarea>
            <p class="text-xs text-gray-500 mt-1">Gunakan centang baris; jika tidak ada yang dicentang, sistem ambil yang statusnya <b>Lolos {{ $stage }}</b>.</p>
          </div>

          <div class="flex items-center gap-2">
            <input id="use_template" type="checkbox" class="h-4 w-4"
                   x-model="useTemplate" @change="toggleTemplate()">
            <label for="use_template" class="text-sm text-gray-700">Gunakan template default</label>
          </div>

          <div>
            <label class="text-xs text-gray-600">Subjek</label>
            <input type="text" name="subject" class="w-full border rounded px-3 py-2"
                   x-ref="subject" placeholder="Subjek email">
          </div>

          <div>
            <label class="text-xs text-gray-600">Pesan</label>
            <textarea name="message" rows="6" class="w-full border rounded px-3 py-2"
                      x-ref="message" placeholder="Isi email"></textarea>
            <p class="text-xs text-gray-500 mt-1">
              Preview untuk penerima pertama. Saat dikirim, nama & posisi akan dipersonalisasi untuk masing-masing penerima.
            </p>
          </div>

          <div>
            <label class="text-xs text-gray-600">Lampiran (PDF, max 5 MB)</label>
            <input type="file" name="attachment" accept="application/pdf,.pdf"
                   class="w-full border rounded px-3 py-2" required>
          </div>
        </div>

        <div class="mt-5 flex justify-end gap-2">
          <button type="button" class="px-4 py-2 rounded border" @click="closeEmailModal()">Batal</button>
          <button type="submit" class="px-4 py-2 rounded bg-orange-600 text-white">Kirim</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function stageSeleksi() {
  return {
    // stage untuk template
    stage: @json($stage),

    // Email
    emailModalOpen: false,
    selectedEmails: [],
    selectedMeta: [], // {id,email,name,position}
    useTemplate: true,

    // Edit
    editModalOpen: false,
    form: {
      id: null,
      name: '', email: '',
      nik: '', no_telp: '',
      tpt_lahir: '', tgl_lahir: '',
      alamat: '',
      pendidikan: 'S1',
      universitas: '', jurusan: '',
      thn_lulus: '',
      position_id: '',
      status: 'Seleksi Administrasi',
    },

    updateBase: @json(url('admin/applicant/__ID__')),

    init() {
      const selectAll = this.$root.querySelector('#selectAll');
      if (selectAll) {
        selectAll.addEventListener('change', (e) => {
          this.$root.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = e.target.checked);
        });
      }
    },

    lockInputs(lock) {
      const subj = this.$refs?.subject, msg = this.$refs?.message;
      [subj, msg].forEach(el => {
        if (!el) return;
        el.disabled = lock; el.readOnly = lock; el.required = !lock;
      });
    },

    buildTemplate(fullName, positionName) {
      const name = fullName && fullName.trim() ? fullName : 'Peserta';
      const pos  = positionName && positionName.trim() ? positionName : '-';
      const subject = `INFORMASI HASIL SELEKSI ${this.stage} TAD/OUTSOURCING - PLN ICON PLUS`;
      const body =
`Halo ${name}

Terima kasih atas partisipasi Saudara/i dalam mengikuti proses seleksi TAD/OUTSOURCING PLN ICON PLUS pada posisi ${pos}.

Selamat Anda lolos pada tahap ${this.stage}. Selanjutnya, silakan cek jadwal Anda untuk tahap berikutnya pada lampiran email ini.

Demikian kami sampaikan.
Terima kasih atas partisipasinya dan semoga sukses.`;
      return { subject, body };
    },

    gatherEmails() {
      const checked = Array.from(this.$root.querySelectorAll('.applicant-checkbox:checked'));
      let nodes = checked;
      if (nodes.length === 0) {
        nodes = Array.from(this.$root.querySelectorAll('.applicant-checkbox'))
          .filter(cb => (cb.dataset.stageState || '').toLowerCase() === 'lolos');
      }
      const recipients = [], ids = [], meta = [];
      nodes.forEach(cb => {
        const email = cb.dataset.email;
        if (email) {
          recipients.push(email);
          ids.push(cb.value);
          meta.push({
            id: cb.value,
            email,
            name: cb.dataset.name || '',
            position: cb.dataset.position || '-',
          });
        }
      });
      return { recipients, ids, meta };
    },

    openEmailModal() {
      const { recipients, ids, meta } = this.gatherEmails();
      if (recipients.length === 0) { alert('Pilih baris atau pastikan ada yang statusnya Lolos.'); return; }

      this.selectedEmails = recipients;
      this.selectedMeta   = meta;

      if (this.$refs.recipients)   this.$refs.recipients.value   = recipients.join(',');
      if (this.$refs.recipientIds) this.$refs.recipientIds.value = ids.join(',');

      if (this.useTemplate) { this.applyTemplatePreview(); this.lockInputs(true); }
      else { this.lockInputs(false); }

      this.emailModalOpen = true;
    },

    toggleTemplate() {
      if (this.useTemplate) { this.applyTemplatePreview(); this.lockInputs(true); }
      else { this.lockInputs(false); }
    },

    applyTemplatePreview() {
      const first = this.selectedMeta?.[0] || {name: 'Peserta', position: '-'};
      const {subject, body} = this.buildTemplate(first.name, first.position);
      if (this.$refs.subject) this.$refs.subject.value = subject;
      if (this.$refs.message) this.$refs.message.value = body;
    },

    closeEmailModal() { this.emailModalOpen = false; },

    validateAndSubmit(e) {
      const form = e.target;
      const file = form.querySelector('input[type="file"][name="attachment"]')?.files?.[0];
      if (!file) { e.preventDefault(); alert('Wajib unggah lampiran PDF.'); return; }
      const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
      if (!isPdf) { e.preventDefault(); alert('Lampiran harus PDF.'); return; }
      if (file.size > 5 * 1024 * 1024) { e.preventDefault(); alert('Ukuran PDF maksimal 5 MB.'); return; }

      if (!this.useTemplate) {
        const subject = (this.$refs?.subject?.value || '').trim();
        const message = (this.$refs?.message?.value || '').trim();
        if (!subject || !message) { e.preventDefault(); alert('Isi subjek & pesan.'); }
      }
    },

    openEditModal(data) { this.form = Object.assign({}, this.form, data||{}); this.editModalOpen = true; },
    closeEditModal() { this.editModalOpen = false; },
    updateUrl() { return this.updateBase.replace('__ID__', this.form.id ?? ''); },
  };
}
</script>
