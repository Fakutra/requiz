<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\TestResult;
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
                'offering',
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();
        
        $applicants->each(function ($applicant) {
            $test = $applicant->position?->test;

            // Kalau posisinya gak punya tes tulis
            if (!$test) {
                $applicant->hasStartedWrittenTest  = false;
                $applicant->hasFinishedWrittenTest = false;
                return;
            }

            // Ambil test_result terbaru untuk applicant + test ini
            $latestResult = TestResult::where('applicant_id', $applicant->id)
                ->where('test_id', $test->id)
                ->latest('id')
                ->first();

            $hasStarted  = $latestResult && !is_null($latestResult->started_at);
            $hasFinished = $latestResult && !is_null($latestResult->finished_at);

            $applicant->hasStartedWrittenTest  = $hasStarted;
            $applicant->hasFinishedWrittenTest = $hasFinished;
        });

        return response()
            ->view('history', compact('applicants'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
