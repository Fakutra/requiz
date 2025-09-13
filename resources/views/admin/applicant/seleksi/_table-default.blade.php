{{-- resources/views/admin/applicant/seleksi/_table-default.blade.php --}}
@php
  use Illuminate\Support\Facades\Storage;
@endphp

<div x-data="stageSeleksi()" x-init="init()" x-cloak>
  <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">

    {{-- ================== Filter (GET) + Aksi ================== --}}
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
      <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-2">
        {{-- Penting: bawa batch --}}
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
            @foreach ($allJurusan as $jurusan)
              <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>
                {{ $jurusan }}
              </option>
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
              Lolos {{ $stage }}{{ isset($nextStage) ? ' → '.$nextStage : '' }}
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
        <button type="button" @click="openEmailModalAuto()"
                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">Email</button>
      </div>
    </div>

    {{-- ================== Form UPDATE STATUS ================== --}}
    <form id="statusForm" method="POST" action="{{ route('admin.applicant.seleksi.update-status') }}">
      @csrf
      <input type="hidden" name="stage" value="{{ $stage }}">
      <div id="statusInputs"></div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">
          <thead class="bg-gray-100">
          <tr>
            <th class="p-3 border-b w-10">
              <input type="checkbox" id="selectAll">
            </th>
            <th class="p-3 border-b">Nama</th>
            <th class="p-3 border-b">Email</th>
            <th class="p-3 border-b">Jurusan</th>
            <th class="p-3 border-b">Posisi</th>
            <th class="p-3 border-b">Umur</th>
            <th class="p-3 border-b">Status Seleksi</th>
            <th class="p-3 border-b">Status Email</th>
            <th class="p-3 border-b">Aksi</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($applicants as $applicant)
            @php
              $state = 'other';
              $badge = 'bg-gray-100 text-gray-800 border border-gray-200';
              $text  = $stage;

              if ($applicant->status === $stage) {
                  $state = 'current'; $text  = $stage;
              } elseif (($failEnum ?? null) && $applicant->status === $failEnum) {
                  $state = 'gagal';   $text  = $failEnum;  $badge = 'bg-red-50 text-red-700 border border-red-200';
              } elseif ((($nextStage ?? null) && $applicant->status === $nextStage)
                        || ($stage === 'Offering' && $applicant->status === 'Menerima Offering')) {
                  $state = 'lolos';   $text  = ($stage === 'Offering') ? 'Menerima Offering' : ('Lolos '.$stage);
                  $badge = 'bg-green-50 text-green-700 border border-green-200';
              } elseif ($stage === 'Offering' && $applicant->status === 'Menolak Offering') {
                  $state = 'gagal';   $text  = 'Menolak Offering';    $badge = 'bg-red-50 text-red-700 border border-red-200';
              }

              $state = $applicant->_stage_state  ?? $state;
              $text  = $applicant->_stage_status ?? $text;
              $badge = $applicant->_stage_badge  ?? $badge;

              $cvUrl = $applicant->cv_document ? Storage::url($applicant->cv_document) : null;
            @endphp

            <tr class="hover:bg-gray-50">
              <td class="p-3 border-b align-top">
                <input type="checkbox"
                       class="applicant-checkbox"
                       name="selected_applicants[]"
                       value="{{ $applicant->id }}"
                       data-email="{{ $applicant->email }}"
                       data-stage-state="{{ $state }}">
              </td>
              <td class="p-3 border-b align-top">{{ $applicant->name }}</td>
              <td class="p-3 border-b align-top">{{ $applicant->email }}</td>
              <td class="p-3 border-b align-top">{{ $applicant->jurusan }}</td>
              <td class="p-3 border-b align-top">{{ $applicant->position->name ?? '-' }}</td>
              <td class="p-3 border-b align-top">{{ $applicant->age }} tahun</td>
              <td class="p-3 border-b align-top">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">
                  {{ $text }}
                </span>
              </td>
              <td class="p-3 border-b align-top">
                @if(!empty($applicant->_email_sent))
                  <span class="inline-flex items-center gap-1 text-green-600" title="Terkirim">✓</span>
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </td>

              {{-- ===== Aksi ===== --}}
              <td class="p-3 border-b align-top">
                <div class="flex items-center gap-3 text-sm">
                  {{-- Lihat CV --}}
                  <a href="#"
                     @click.prevent="openCvModal(@js($cvUrl), @js('CV - '.$applicant->name))"
                     class="text-blue-600 hover:underline">Lihat</a>

                  {{-- Edit data --}}
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
                     class="text-amber-600 hover:underline">Edit</a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="p-6 text-center text-gray-500">Tidak ada data untuk kriteria ini.</td>
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

  {{-- ================== MODAL: Lihat CV ================== --}}
  <div x-show="cvModalOpen" x-transition.opacity
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white w-full max-w-4xl max-h-[85vh] rounded-lg shadow-lg overflow-hidden"
         @click.away="closeCvModal()">
      <div class="flex items-center justify-between px-4 py-3 border-b">
        <h3 class="font-semibold" x-text="cvName || 'CV'"></h3>
        <button class="text-gray-500 hover:text-gray-700" @click="closeCvModal()">✕</button>
      </div>
      <div class="p-4">
        <template x-if="cvUrl">
          <iframe :src="cvUrl" class="w-full h-[70vh]" frameborder="0"></iframe>
        </template>
        <template x-if="!cvUrl">
          <div class="text-center text-gray-500 py-10">CV tidak tersedia.</div>
        </template>
      </div>
    </div>
  </div>

  {{-- ================== MODAL: Edit Applicant ================== --}}
  <div x-show="editModalOpen" x-transition.opacity
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg"
         @click.away="closeEditModal()">
      <div class="flex items-center justify-between px-4 py-3 border-b">
        <h3 class="font-semibold">Edit Data Pelamar</h3>
        <button class="text-gray-500 hover:text-gray-700" @click="closeEditModal()">✕</button>
      </div>

      <form :action="updateUrl()" method="POST" enctype="multipart/form-data" class="p-4">
        @csrf
        @method('PUT')

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
</div>

{{-- ================== SCRIPT: Alpine store untuk halaman tahap ================== --}}
<script>
function stageSeleksi() {
  return {
    // ===== Email =====
    emailModalOpen: false,
    selectedEmails: [],
    useTemplate: true,

    // ===== CV Modal =====
    cvModalOpen: false,
    cvUrl: null,
    cvName: '',

    // ===== Edit Modal =====
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

    // base URL untuk update → akan diganti __ID__ di JS
    updateBase: @json(url('admin/applicant/__ID__')),

    init() {
      const selectAll = document.getElementById('selectAll');
      if (selectAll) {
        selectAll.addEventListener('change', (e) => {
          document.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = e.target.checked);
        });
      }
    },

    // ==== EMAIL ====
    lockInputs(lock) {
      const subj = this.$refs?.subject, msg = this.$refs?.message;
      [subj, msg].forEach(el => {
        if (!el) return;
        el.disabled = lock;
        el.readOnly = lock;
        el.required = !lock;
      });
    },
    submitStatus(action) {
      const form = document.getElementById('statusForm');
      const container = document.getElementById('statusInputs');
      container.innerHTML = '';
      const checked = Array.from(document.querySelectorAll('.applicant-checkbox:checked'));
      if (checked.length === 0) { alert('Pilih minimal satu peserta dulu.'); return; }
      checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `status[${cb.value}]`;
        input.value = (action === 'lolos') ? 'lolos' : 'tidak_lolos';
        container.appendChild(input);
      });
      form.submit();
    },
    openEmailModalAuto() {
      const recipients = [], recipientIds = [];
      document.querySelectorAll('.applicant-checkbox').forEach(cb => {
        if (cb.dataset.stageState === 'lolos') {
          recipients.push(cb.dataset.email);
          recipientIds.push(cb.value);
          cb.checked = true;
        }
      });
      if (recipients.length === 0) { alert('Tidak ada peserta dengan status lolos pada halaman ini.'); return; }
      this.useTemplate = true;
      this.selectedEmails = recipients;
      document.getElementById('recipients').value = recipients.join(',');
      document.getElementById('recipient_ids').value = recipientIds.join(',');
      this.emailModalOpen = true;
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
        const subject = form.querySelector('[name="subject"]')?.value?.trim() ?? '';
        const message = form.querySelector('[name="message"]')?.value?.trim() ?? '';
        if (!subject || !message) { e.preventDefault(); alert('Subjek dan pesan wajib diisi.'); }
      }
    },

    // ==== CV MODAL ====
    openCvModal(url, name) {
      if (!url) { alert('CV tidak tersedia.'); return; }
      this.cvUrl = url;
      this.cvName = name || 'CV';
      this.cvModalOpen = true;
    },
    closeCvModal() {
      this.cvModalOpen = false;
      this.cvUrl = null;
      this.cvName = '';
    },

    // ==== EDIT MODAL ====
    openEditModal(data) {
      this.form = Object.assign({}, this.form, data || {});
      this.editModalOpen = true;
    },
    closeEditModal() { this.editModalOpen = false; },
    updateUrl() { return this.updateBase.replace('__ID__', this.form.id ?? ''); },
  };
}
</script>
