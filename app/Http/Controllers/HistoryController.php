<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua applicant.id milik user ini (dipakai untuk filter eager load answers)
        $userApplicantIds = Applicant::where('user_id', $userId)->pluck('id');

        $applicants = Applicant::with([
                'position.test',
                // Jadwal Technical Test untuk posisi (urut terbaru)
                'position.technicalSchedules' => fn ($q) => $q->orderByDesc('schedule_date'),
                // Eager load answers milik user saja -> hemat query & data
                'position.technicalSchedules.answers' => fn ($q) => $q->whereIn('applicant_id', $userApplicantIds),
                // Jadwal Interview untuk posisi (urut terbaru)
                'position.interviewSchedules' => fn ($q) => $q->orderByDesc('schedule_start'),
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()
            ->view('history', compact('applicants'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
