<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Position;
use App\Models\User;
use App\Models\Test;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Menggunakan Carbon untuk manipulasi tanggal dan waktu

class HistoryController extends Controller
{
    public function index()
    {
        // Ambil applicant + test (seperti sebelumnya)
        $applicants = Applicant::with(['position.test'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Penting: jangan di-cache agar tombol bisa aktif tepat saatnya ketika user refresh
        return response()
            ->view('history', compact('applicants'))
            ->header('Cache-Control','no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma','no-cache');
    }
}
