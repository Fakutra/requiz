<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            'position.technicalSchedules' => fn($q) => $q->orderByDesc('schedule_date'),
            // Eager load answers milik user saja -> hemat query & data
            'position.technicalSchedules.answers' => fn($q) => $q->whereIn('applicant_id', $userApplicantIds),
            // Jadwal Interview untuk posisi (urut terbaru)
            'position.interviewSchedules' => fn($q) => $q->orderByDesc('schedule_start'),
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


            // Set default
            $applicant->isOfferingExpired = false;
            $applicant->deadlineDate = null; // Inisialisasi awal agar tidak undefined

            if ($applicant->offering) {
                // Ambil waktu dibuatnya offering
                $createdAt = $applicant->offering->created_at;

                // 2. Tentukan Deadline (Tambah 5 hari kerja)
                // Menggunakan addWeekdays(5) secara otomatis melompati Sabtu & Minggu
                $applicant->deadlineDate = $createdAt->copy()->addWeekdays(5)->endOfDay();

                // 3. Tentukan apakah sudah expired
                // Cukup bandingkan waktu sekarang dengan deadlineDate
                $applicant->isOfferingExpired = now()->greaterThan($applicant->deadlineDate);
            }
        });

        return response()
            ->view('history', compact('applicants'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
