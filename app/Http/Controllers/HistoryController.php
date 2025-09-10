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
    // app/Http/Controllers/HistoryController.php
    public function index()
    {
        $userApplicantIds = \App\Models\Applicant::where('user_id', auth()->id())->pluck('id');

        $applicants = \App\Models\Applicant::with([
                'position.test',
                'position.technicalSchedules' => fn($q) => $q->orderByDesc('schedule_date'),
                'position.technicalSchedules.answers' => fn($q) => $q->whereIn('applicant_id', $userApplicantIds),
            ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()
            ->view('history', compact('applicants'))
            ->header('Cache-Control','no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma','no-cache');
    }

}
