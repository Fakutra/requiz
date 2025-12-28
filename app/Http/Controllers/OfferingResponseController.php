<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Offering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfferingResponseController extends Controller
{
    public function response(Request $request, Offering $offering)
    {
        // ==========================
        // VALIDASI INPUT
        // ==========================
        $request->validate([
            'action' => 'required|in:accept,decline',
        ]);

        // ambil applicant terkait offering
        $applicant = Applicant::findOrFail($offering->applicant_id);

        // ==========================
        // KEAMANAN: KEPEMILIKAN
        // ==========================
        abort_unless($applicant->user_id === Auth::id(), 403);

        // ==========================
        // GUARD: SUDAH FINAL
        // ==========================
        if ($offering->responded_at) {
            return redirect()
                ->route('history.index')
                ->with('status', 'Offering ini sudah kamu respon sebelumnya.');
        }

        // ==========================
        // GUARD: OFFERING EXPIRED
        // ==========================
        if ($offering->response_deadline && now()->greaterThan($offering->response_deadline)) {

            DB::transaction(function () use ($offering, $applicant) {
                $offering->update([
                    'decision'        => 'declined',
                    'decision_by'     => 'system',
                    'decision_reason' => 'expired',
                    'responded_at'    => now(),
                ]);

                $applicant->update([
                    'status' => 'Menolak Offering',
                ]);
            });

            return redirect()
                ->route('history.index')
                ->with('status', 'Offering sudah melewati batas waktu dan dianggap ditolak.');
        }

        // ==========================
        // PROSES TERIMA / TOLAK
        // ==========================
        DB::transaction(function () use ($request, $offering, $applicant) {

            $isAccept = $request->action === 'accept';

            $offering->update([
                'decision'        => $isAccept ? 'accepted' : 'declined',
                'decision_by'     => 'user',
                'decision_reason' => 'manual',
                'responded_at'    => now(),
            ]);

            $applicant->update([
                'status' => $isAccept
                    ? 'Menerima Offering'
                    : 'Menolak Offering',
            ]);
        });

        return redirect()
            ->route('history.index')
            ->with('success', 'Respon offering berhasil disimpan.');
    }
}
