{{-- resources/views/admin/applicant/seleksi/administrasi/index.blade.php --}}
<x-app-admin>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Seleksi Administrasi</h2>
  </x-slot>

  <div x-data="stageSeleksi()" x-init="init()" x-cloak>
    <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
      {{-- ================== Filter (GET) + Aksi ================== --}}
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
                  @click="submitStatus('gagal')">Gagal</button>
          <button type="button"
                  class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded"
                  @click="openEmailModal()">Email</button>
        </div>
      </div>

      {{-- ================== Form UPDATE STATUS ================== --}}
      <form id="statusForm" method="POST" action="{{ route('admin.applicant.seleksi.updateStatus') }}" data-stage="{{ $stage }}">
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
                       @click.prevent="openCvModal(
                         @js($applicant->cv_document ? \Illuminate\Support\Facades\Storage::url($applicant->cv_document) : null),
                         @js('CV - '.$applicant->name)
                       )"
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
           @click.outside="closeCvModal()">
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

    {{-- ================== MODAL: Kirim Email ================== --}}
    <div x-show="emailModalOpen" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg"
           @click.outside="closeEmailModal()">
        <div class="flex items-center justify-between px-4 py-3 border-b">
          <h3 class="font-semibold">Kirim Email Hasil {{ $stage }}</h3>
          <button class="text-gray-500 hover:text-gray-700" @click="closeEmailModal()">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.applicant.seleksi.sendEmail') }}"
              enctype="multipart/form-data" class="p-4" @submit="validateAndSubmit">
          @csrf
          <input type="hidden" name="stage" value="{{ $stage }}">
          <input type="hidden" x-ref="recipientIds" id="recipient_ids" name="recipient_ids">
          <input type="hidden" x-ref="recipients"   id="recipients"   name="recipients">

          <div class="space-y-4">
            <div>
              <label class="text-xs text-gray-600">Penerima</label>
              <textarea class="w-full border rounded px-3 py-2 text-sm" rows="3"
                        x-text="selectedEmails.join(', ')" readonly></textarea>
              <p class="text-xs text-gray-500 mt-1">
                Gunakan centang baris untuk memilih. Jika tidak ada yang dicentang, sistem otomatis memilih peserta dengan status
                <b>Lolos {{ $stage }}</b> di halaman ini.
              </p>
            </div>

            <div class="flex items-center gap-2">
              <input id="use_template" type="checkbox" class="h-4 w-4"
                     name="use_template" value="1"
                     x-model="useTemplate" @change="lockInputs(useTemplate)">
              <label for="use_template" class="text-sm text-gray-700">Gunakan template default</label>
            </div>

            <div>
              <label class="text-xs text-gray-600">Subjek</label>
              <input type="text" name="subject" class="w-full border rounded px-3 py-2"
                     x-ref="subject" placeholder="Subjek email (isi bila tidak pakai template)">
            </div>

            <div>
              <label class="text-xs text-gray-600">Pesan</label>
              <textarea name="message" rows="6" class="w-full border rounded px-3 py-2"
                        x-ref="message" placeholder="Isi email (isi bila tidak pakai template)"></textarea>
              <p class="text-xs text-gray-500 mt-1">Jika “Gunakan template default” dicentang, subjek & pesan otomatis diisi di server.</p>
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

  @push('scripts')
    <script>
      // HANYA di halaman ini (tidak ada definisi ganda di layout)
      function stageSeleksi() {
        return {
          stage: @json($stage),

          // Email
          emailModalOpen: false,
          selectedEmails: [],
          selectedMeta: [],
          useTemplate: true,

          // CV Modal
          cvModalOpen: false,
          cvUrl: null,
          cvName: null,

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

          // ===== Status bulk submit helper =====
          submitStatus(type) {
            const form = this.$root.querySelector('#statusForm');
            const wrap = form.querySelector('#statusInputs');
            wrap.innerHTML = '';
            const checked = Array.from(this.$root.querySelectorAll('.applicant-checkbox:checked'));
            if (checked.length === 0) { alert('Pilih minimal satu peserta.'); return; }
            checked.forEach(cb => {
              const i = document.createElement('input');
              i.type = 'hidden'; i.name = 'ids[]'; i.value = cb.value;
              wrap.appendChild(i);
            });
            const action = document.createElement('input');
            action.type = 'hidden'; action.name = 'action'; action.value = type;
            wrap.appendChild(action);
            form.submit();
          },

          // ===== Email =====
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
          applyTemplatePreview() {
            const first = this.selectedMeta?.[0] || {name: 'Peserta', position: '-'};
            const {subject, body} = this.buildTemplate(first.name, first.position);
            if (this.$refs.subject) this.$refs.subject.value = subject;
            if (this.$refs.message) this.$refs.message.value = body;
          },
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

          // ===== CV Modal =====
          openCvModal(url, name) { this.cvUrl = url; this.cvName = name; this.cvModalOpen = true; },
          closeCvModal() { this.cvModalOpen = false; this.cvUrl = null; this.cvName = null; },

          // ===== Edit =====
          openEditModal(data) { this.form = Object.assign({}, this.form, data||{}); this.editModalOpen = true; },
          closeEditModal() { this.editModalOpen = false; },
          updateUrl() { return this.updateBase.replace('__ID__', this.form.id ?? ''); },
        };
      }
    </script>
  @endpush
</x-app-admin>
