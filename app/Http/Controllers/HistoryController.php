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
        // Mengambil data pelamar beserta relasi posisi dan tes-nya
        // dengan eager loading untuk menghindari N+1 query problem.
        $applicants = Applicant::with(['position.test'])
                                ->where('user_id', auth()->id())
                                ->latest()
                                ->get();
        
        return view('history', compact('applicants'));
    }
}
