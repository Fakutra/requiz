<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Services\SelectionLogger;
use Illuminate\Support\Facades\Auth;

class StageActionController extends Controller
{
    public function mark(Request $r)
    {
        $data = $r->validate([
            'applicant_id' => ['required','exists:applicants,id'],
            'stage'        => ['required','string','max:100'],           // 'Seleksi Administrasi' | 'Tes Tulis' | ...
            'result'       => ['required','in:lolos,tidak_lolos'],
            'update_status'=> ['nullable','in:auto'],                    // null atau 'auto'
        ]);

        $a = Applicant::findOrFail($data['applicant_id']);

        // 1) Tulis log (bahan rekap)
        SelectionLogger::write($a, $data['stage'], $data['result'], Auth::id());

        // 2) Ubah status global applicant (opsi 'auto')
        if (($data['update_status'] ?? null) === 'auto') {
            $a->forceFill(['status' => $this->newStatus($data['stage'], $data['result'], $a->status)])->save();
        }

        return back()->with('success', 'Keputusan tersimpan.');
    }

    private function newStatus(string $stage, string $result, string $current): string
    {
        if ($result === 'lolos') {
            return match ($stage) {
                'Seleksi Administrasi' => 'Tes Tulis',
                'Tes Tulis'            => 'Technical Test',
                'Technical Test'       => 'Interview',
                'Interview'            => 'Offering',
                'Offering'             => 'Menerima Offering',
                default                => $current,
            };
        }
        // tidak_lolos
        return match ($stage) {
            'Seleksi Administrasi' => 'Tidak Lolos Seleksi Administrasi',
            'Tes Tulis'            => 'Tidak Lolos Tes Tulis',
            'Technical Test'       => 'Tidak Lolos Technical Test',
            'Interview'            => 'Tidak Lolos Interview', // <- fix
            'Offering'             => 'Menolak Offering',
            default                => $current,
        };
    }
}
