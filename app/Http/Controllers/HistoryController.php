<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua applicant milik user
        $applicants = Applicant::with([
                'position.test',
                'position.technicalSchedules' => fn ($q) => $q->orderByDesc('schedule_date'),
                'position.interviewSchedules' => fn ($q) => $q->orderByDesc('schedule_start'),
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
        });

        return response()
            ->view('history', compact('applicants'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
