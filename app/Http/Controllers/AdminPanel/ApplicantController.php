<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Position;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Exports\ApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ActivityLogger; // ✅ tambahkan ini
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;

class ApplicantController extends Controller
{
    /**
     * INDEX: search + filter batch + filter position
     */
    public function index(Request $request)
    {
        $search     = trim((string) $request->query('search'));
        $positionId = $request->query('position');
        $batchId    = $request->query('batch');

        // sort: name/email(->users), umur(->profiles.birthdate), sisanya kolom applicants
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        $allowedSorts = [
            'name', 'email', 'umur',            // via collection (relasi)
            'position_id', 'ekspektasi_gaji',   // via DB
            'pendidikan', 'jurusan', 'batch_id' // via DB
        ];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'name';

        $q = Applicant::query()
            ->with([
                'position:id,name',
                'batch:id,name',
                'user:id,name,email',
                'user.profile:id,user_id,identity_num,phone_number,birthplace,birthdate,address',
            ]);

        // 🔎 Search: users.name, users.email, applicants.jurusan, positions.name
        if ($search !== '') {
            $needle = '%'.mb_strtolower($search).'%';
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(applicants.jurusan) LIKE ?', [$needle])
                ->orWhereHas('position', fn($p) =>
                    $p->whereRaw('LOWER(name) LIKE ?', [$needle])
                )
                ->orWhereHas('user', function ($u) use ($needle) {
                    $u->whereRaw('LOWER(name) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$needle]);
                });
            });
        }

        if (!empty($positionId)) $q->where('position_id', $positionId);
        if (!empty($batchId))    $q->where('batch_id', $batchId);

        // ⚖️ Sorting
        $sortUsingCollection = in_array($sort, ['name','email','umur'], true);

        if ($sortUsingCollection) {
            // ambil dulu semua sesuai filter
            $collection = $q->get();

            // sort in-memory pakai relasi & accessor age
            $collection = $collection->sortBy(function ($a) use ($sort) {
                return match ($sort) {
                    'name'  => mb_strtolower($a->user->name  ?? ''),
                    'email' => mb_strtolower($a->user->email ?? ''),
                    'umur'  => $a->age ?? -1, // accessor dari model
                    default => null,
                };
            }, SORT_NATURAL, $direction === 'desc');

            // paginate manual
            $page    = (int) $request->input('page', 1);
            $perPage = 15;

            $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
                $collection->forPage($page, $perPage)->values(),
                $collection->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // orderBy di DB (prefix table biar gak ambiguous)
            $applicants = $q->orderBy('applicants.'.$sort, $direction)
                ->paginate(15)
                ->appends($request->query());
            // ❌ gak perlu inject age/gaji_formatted — sudah accessor di model
        }

        $positions = Position::orderBy('name')->get(['id','name']);
        $batches   = Batch::orderBy('id')->get(['id','name']);

        return view('admin.applicant.index', compact(
            'applicants','positions','batches','sort','direction'
        ));
    }

    /**
     * UPDATE: dipanggil dari modal Edit
     */
    public function update(Request $request, Applicant $applicant)
    {
        $allowedPendidikan = ['SMA/Sederajat','D1','D2','D3','D4','S1','S2','S3'];
        $allowedStatus = [
            'Seleksi Administrasi','Tes Tulis','Technical Test','Interview','Offering',
            'Tidak Lolos Seleksi Administrasi','Tidak Lolos Tes Tulis',
            'Tidak Lolos Technical Test','Tidak Lolos Interview',
            'Menerima Offering','Menolak Offering',
        ];

        // validasi gabungan (user + profile + applicant)
        $data = $request->validate([
            // users
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($applicant->user_id)],

            // profiles
            'nik'       => ['nullable','string','max:32'],
            'no_telp'   => ['nullable','string','max:32'],
            'tpt_lahir' => ['nullable','string','max:255'],
            'tgl_lahir' => ['nullable','date'],
            'alamat'    => ['nullable','string','max:500'],

            // applicants
            'pendidikan'      => ['nullable', Rule::in($allowedPendidikan)],
            'universitas'     => ['nullable','string','max:255'],
            'jurusan'         => ['nullable','string','max:255'],
            'thn_lulus'       => ['nullable','digits:4'],
            'position_id'     => ['required','exists:positions,id'],
            'batch_id'        => ['nullable','exists:batches,id'],
            'ekspektasi_gaji' => ['required','numeric','min:0','max:100000000'],
            'status'          => ['nullable', Rule::in($allowedStatus)],
            'skills'          => ['nullable','string','max:5000'],
            'cv_document'     => ['nullable','file','mimes:pdf','max:1024'],
            'doc_tambahan'    => ['nullable','file','mimes:pdf','max:5120'], // 5MB
        ]);

        DB::transaction(function () use ($data, $request, $applicant) {

            // === Update Users ===
            $user = $applicant->user;
            $user->fill([
                'name'  => $data['name'],
                'email' => $data['email'],
            ])->save();

            // === Update Profiles (make or update) ===
            $profile = $user->profile ?: new Profile(['user_id' => $user->id]);
            $profile->fill([
                'identity_num' => $data['nik']       ?? null,
                'phone_number' => $data['no_telp']   ?? null,
                'birthplace'   => $data['tpt_lahir'] ?? null,
                'birthdate'    => $data['tgl_lahir'] ?? null,
                'address'      => $data['alamat']    ?? null,
            ])->save();

            // === Handle CV baru ===
            if ($request->hasFile('cv_document')) {
                if ($applicant->cv_document) {
                    Storage::disk('public')->delete($applicant->cv_document);
                }
                $data['cv_document'] = $request->file('cv_document')->store('cv_documents', 'public');
            } else {
                unset($data['cv_document']);
            }

            // handle Dokumen Tambahan
            if ($request->hasFile('doc_tambahan')) {
                if ($applicant->doc_tambahan) {
                    Storage::disk('public')->delete($applicant->doc_tambahan);
                }
                $data['doc_tambahan'] = $request->file('doc_tambahan')->store('applicant_docs', 'public');
            } else {
                unset($data['doc_tambahan']);
            }

            // === Update Applicants (hanya kolom applicant) ===
            $applicant->update([
                'pendidikan'      => $data['pendidikan']      ?? $applicant->pendidikan,
                'universitas'     => $data['universitas']     ?? $applicant->universitas,
                'jurusan'         => $data['jurusan']         ?? $applicant->jurusan,
                'thn_lulus'       => $data['thn_lulus']       ?? $applicant->thn_lulus,
                'position_id'     => $data['position_id'],
                'batch_id'        => $data['batch_id']        ?? $applicant->batch_id,
                'ekspektasi_gaji' => $data['ekspektasi_gaji'],
                'status'          => $data['status']          ?? $applicant->status,
                'skills'          => $data['skills']          ?? $applicant->skills,
                'cv_document'     => $data['cv_document']     ?? $applicant->cv_document,
                'doc_tambahan'    => $data['doc_tambahan']    ?? $applicant->doc_tambahan,
            ]);
        });

        return redirect()
            ->route('admin.applicant.index', $request->only('search','position','batch','page'))
            ->with('success','Data pelamar berhasil diperbarui.');
    }

    /**
     * DESTROY: konfirmasi via modal
     */
    public function destroy(Applicant $applicant)
    {
        $name = $applicant->name;

        if ($applicant->cv_document) {
            Storage::disk('public')->delete($applicant->cv_document);
        }
        if ($applicant->doc_tambahan) {
            Storage::disk('public')->delete($applicant->doc_tambahan);
        }
        $applicant->delete();

        // ✅ Log penghapusan data
        ActivityLogger::log(
            'delete',
            'Data Pelamar',
            auth()->user()->name." menghapus data pelamar {$name}",
            "Nama: {$name}"
        );

        return redirect()->route('admin.applicant.index')->with('success','Data pelamar berhasil dihapus.');
    }

    /**
     * EXPORT: mengikuti filter aktif (search + position + batch)
     */
    public function export(Request $request)
    {
        $search     = trim((string) $request->query('search'));
        $positionId = $request->query('position');
        $batchId    = $request->query('batch');

        $fileName = 'data-pelamar-' . now()->format('Y-m-d') . '.xlsx';

        // ✅ Log export data
        ActivityLogger::log(
            'export',
            'Data Pelamar',
            auth()->user()->name.' mengekspor data pelamar ke file Excel',
            "File: {$fileName}"
        );

        return Excel::download(
            new ApplicantsExport($search, $positionId, $batchId),
            $fileName
        );
    }
}
