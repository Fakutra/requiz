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
        // GUARD: SUDAH DIRESPON
        // ==========================
        if ($offering->responded_at) {
            return redirect()
                ->route('history.index')
                ->with('status', 'Offering ini sudah kamu respon sebelumnya.');
        }

        // ==========================
        // GUARD: OFFERING EXPIRED
        // (sinkron dengan HistoryController)
        // ==========================
        if ($offering->response_deadline && now()->greaterThan($offering->response_deadline)) {
            DB::transaction(function () use ($offering, $applicant) {
                $offering->update([
                    'responded_at' => now(), // ✅ CATAT
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

            // 🔴 INI YANG HILANG SEBELUMNYA
            $offering->update([
                'responded_at' => now(), // ✅ TERISI
            ]);

            $applicant->update([
                'status' => $request->action === 'accept'
                    ? 'Menerima Offering'
                    : 'Menolak Offering',
            ]);
        });

        return redirect()
            ->route('history.index')
            ->with('success', 'Respon offering berhasil disimpan.');
    }
}
