<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Offering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferingResponseController extends Controller
{
    public function response(Request $request, Offering $offering)
    {
        $request->validate([
            'action' => 'required|in:accept,decline',
        ]);

        // ambil applicant terkait offering
        $applicant = Applicant::findOrFail($offering->applicant_id);

        // keamanan: pastiin applicant ini milik user yg login
        abort_unless($applicant->user_id === Auth::id(), 403);

        // anti double submit (optional)
        if (in_array($applicant->status, ['Menerima Offering', 'Menolak Offering'])) {
            return redirect()->route('history.index')
                ->with('status', 'Offering ini sudah kamu respon sebelumnya.');
        }

        // update status applicant
        $applicant->status = $request->action === 'accept'
            ? 'Menerima Offering'
            : 'Menolak Offering';

        $applicant->save();

        return redirect()->route('history.index');
}
}
