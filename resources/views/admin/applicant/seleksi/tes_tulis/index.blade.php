{{-- resources/views/admin/applicant/seleksi/tes_tulis/index.blade.php --}}
<x-app-admin>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Seleksi Tes Tulis</h2>
  </x-slot>

  {{-- Flash --}}
  @if (session('status'))
    <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700">
      {{ session('status') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700">
      <ul class="list-disc ms-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div x-data="stageTesTulis()" x-init="init()" x-cloak>
    <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
      {{-- ================== Filter + Aksi ================== --}}
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
          <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                  @click="submitStatus('lolos')">Lolos</button>
          <button type="button" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                  @click="submitStatus('gagal')">Gagal</button>
          <button type="button" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded"
                  @click="openEmailModal()">Email</button>
        </div>
      </div>

      {{-- ================== Form UPDATE STATUS ================== --}}
      <form id="statusForm" method="POST" action="{{ route('admin.applicant.seleksi.updateStatus') }}">
        @csrf
        <input type="hidden" name="stage" value="{{ $stage }}">
        {{-- ⬇️ hidden action permanen (diisi Alpine saat klik tombol) --}}
        <input type="hidden" name="action" x-ref="actionInput">
        {{-- tempat sisip ids[] --}}
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

                $score = $applicant->_quiz_score ?? $applicant->quiz_score ?? null;
              @endphp

              <tr class="hover:bg-gray-50">
                <td class="p-3 border-b align-top">
                  <input type="checkbox"
                         class="applicant-checkbox"
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

    {{-- ================== MODAL EMAIL (opsional, tetap disertakan) ================== --}}
    <div x-show="emailModalOpen" class="fixed inset-0 z-40 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click.outside="emailModalOpen=false"></div>
      <div class="relative z-50 w-full max-w-2xl rounded-xl bg-white p-5 shadow">
        <h3 class="text-lg font-semibold mb-3">Kirim Email Hasil {{ $stage }}</h3>

        <form method="POST" action="{{ route('admin.applicant.seleksi.sendEmail') }}"
              enctype="multipart/form-data" class="grid gap-3"
              @submit="validateAndSubmit">
          @csrf
          <input type="hidden" name="stage" value="{{ $stage }}">
          <input type="hidden" x-ref="recipients" name="recipients">
          <input type="hidden" x-ref="recipientIds" name="recipient_ids">

          <div>
            <label class="text-xs text-gray-500 mb-1 block">Gunakan Template</label>
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" x-model="useTemplate" name="use_template" @change="toggleTemplate()">
              <span class="text-sm">Ya, gunakan template default</span>
            </label>
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">Subjek</label>
            <input type="text" class="w-full border rounded px-3 py-2" x-ref="subject" name="subject" placeholder="Subject...">
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">Pesan</label>
            <textarea class="w-full border rounded px-3 py-2 min-h-[140px]" x-ref="message" name="message" placeholder="Isi pesan..."></textarea>
            <p class="text-xs text-gray-500 mt-1">Jika centang template, subjek & pesan akan diisi otomatis di server.</p>
          </div>

          <div>
            <label class="text-xs text-gray-500 mb-1 block">Lampiran PDF (wajib, maks 5 MB)</label>
            <input type="file" name="attachment" accept="application/pdf" class="w-full" required>
          </div>

          <div class="mt-2 flex items-center justify-end gap-2">
            <button type="button" class="px-3 py-2 rounded border" @click="emailModalOpen=false">Batal</button>
            <button type="submit" class="px-4 py-2 rounded bg-orange-600 text-white hover:bg-orange-700">
              Kirim Email
            </button>
          </div>
        </form>

        <div class="mt-4">
          <div class="text-xs text-gray-500 mb-1">Penerima</div>
          <div class="max-h-28 overflow-auto border rounded p-2 text-sm">
            <template x-for="m in selectedMeta" :key="m.id">
              <div x-text="`${m.name} <${m.email}> — ${m.position}`"></div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      function stageTesTulis() {
        return {
          stage: @json($stage),

          // Email
          emailModalOpen: false,
          selectedEmails: [],
          selectedMeta: [],
          useTemplate: true,

          init() {
            const selectAll = this.$root.querySelector('#selectAll');
            if (selectAll) {
              selectAll.addEventListener('change', (e) => {
                this.$root.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = e.target.checked);
              });
            }
          },

          // ====== STATUS ======
          submitStatus(type) {
            const form = this.$root.querySelector('#statusForm');
            const wrap = form.querySelector('#statusInputs');
            wrap.innerHTML = '';

            const checked = Array.from(this.$root.querySelectorAll('.applicant-checkbox:checked'));
            if (checked.length === 0) { alert('Pilih minimal satu peserta.'); return; }

            // sisipkan ids[]
            checked.forEach(cb => {
              const i = document.createElement('input');
              i.type = 'hidden'; i.name = 'ids[]'; i.value = cb.value;
              wrap.appendChild(i);
            });

            // isi hidden action permanen -> in:lolos,gagal,reset
            let actionInput = this.$refs?.actionInput || form.querySelector('input[name="action"]');
            if (!actionInput) {
              actionInput = document.createElement('input');
              actionInput.type = 'hidden';
              actionInput.name = 'action';
              form.appendChild(actionInput);
            }
            actionInput.value = type; // 'lolos' / 'gagal' / 'reset'

            form.submit();
          },

          // ====== EMAIL ======
          lockInputs(lock) {
            const subj = this.$refs?.subject, msg = this.$refs?.message;
            [subj, msg].forEach(el => { if (!el) return; el.disabled = lock; el.readOnly = lock; el.required = !lock; });
          },
          toggleTemplate() {
            if (this.useTemplate) { this.applyTemplatePreview(); this.lockInputs(true); }
            else { this.lockInputs(false); }
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
        };
      }
    </script>
  @endpush
</x-app-admin>
