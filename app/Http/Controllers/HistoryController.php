<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 🔹 Ambil semua applicant ID milik user
        $userApplicantIds = Applicant::where('user_id', $userId)->pluck('id');

        // Ambil semua applicant milik user
        $applicants = Applicant::with([
            'position.test',

            // Jadwal Technical Test untuk posisi (urut terbaru)
            'position.technicalSchedules' => fn ($q) =>
                $q->orderByDesc('schedule_date'),

            // 🔐 Ambil jawaban technical test HANYA milik user
            'position.technicalSchedules.answers' => fn ($q) =>
                $q->whereIn('applicant_id', $userApplicantIds),

            // Jadwal Interview untuk posisi (urut terbaru)
            'position.interviewSchedules' => fn ($q) =>
                $q->orderByDesc('schedule_start'),

            'offering',
        ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        /**
         * 🔐 FALLBACK AUTO-REJECT OFFERING
         * Kalau offering sudah expired tapi status masih "Offering"
         */
        $applicants->each(function ($applicant) {
            $offering = $applicant->offering;

            if (
                $offering &&
                $offering->isExpired() &&
                $applicant->status === 'Offering'
            ) {
                DB::transaction(function () use ($offering, $applicant) {
                    $offering->update([
                        'responded_at' => now(),
                    ]);

                    $applicant->update([
                        'status' => 'Menolak Offering',
                    ]);
                });
            }
        });

        /**
         * ===== STATUS TES TULIS =====
         */
        $applicants->each(function ($applicant) {
            $test = $applicant->position?->test;

            if (!$test) {
                $applicant->hasStartedWrittenTest  = false;
                $applicant->hasFinishedWrittenTest = false;
                return;
            }

            $latestResult = TestResult::where('applicant_id', $applicant->id)
                ->where('test_id', $test->id)
                ->latest('id')
                ->first();

            $applicant->hasStartedWrittenTest  = $latestResult && $latestResult->started_at;
            $applicant->hasFinishedWrittenTest = $latestResult && $latestResult->finished_at;
            $hasStarted  = $latestResult && !is_null($latestResult->started_at);
            $hasFinished = $latestResult && !is_null($latestResult->finished_at);

            // $applicant->hasStartedWrittenTest  = $hasStarted;
            // $applicant->hasFinishedWrittenTest = $hasFinished;


            // Set default
            $applicant->isOfferingExpired = false;

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
