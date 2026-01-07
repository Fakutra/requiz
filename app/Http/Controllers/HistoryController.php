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

            'position.technicalSchedules' => fn ($q) =>
                $q->orderByDesc('schedule_date'),

            'position.technicalSchedules.answers' => fn ($q) =>
                $q->whereIn('applicant_id', $userApplicantIds),

            'position.interviewSchedules' => fn ($q) =>
                $q->orderByDesc('schedule_start'),

            'offering',
            'offering.field',
            'offering.subfield',
            'offering.job',
            'offering.seksi',
        ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        /**
         * 🔐 AUTO-REJECT OFFERING (SYSTEM)
         * HANYA JIKA:
         * - offering ada
         * - BELUM ADA keputusan
         * - deadline terlewati
         * - status masih Offering
         */
        $applicants->each(function ($applicant) {
            $offering = $applicant->offering;

            if (
                $offering &&
                is_null($offering->decision) && // ✅ lebih aman
                $offering->response_deadline &&
                now()->greaterThan($offering->response_deadline) &&
                $applicant->status === 'Offering'
            ) {
                DB::transaction(function () use ($offering, $applicant) {
                    $offering->update([
                        'decision'         => 'declined',
                        'decision_by'      => 'system',
                        'decision_reason'  => 'expired',
                        'responded_at'     => now(),
                    ]);

                    $applicant->update([
                        'status' => 'Menolak Offering',
                    ]);
                });
            }
        });

        /**
         * ===== STATUS TES TULIS & OFFERING EXPIRED CHECK =====
         */
        $applicants->each(function ($applicant) {
            $test = $applicant->position?->test;

            if (!$test) {
                $applicant->hasStartedWrittenTest  = false;
                $applicant->hasFinishedWrittenTest = false;
            } else {
                $latestResult = TestResult::where('applicant_id', $applicant->id)
                    ->where('test_id', $test->id)
                    ->latest('id')
                    ->first();

                $applicant->hasStartedWrittenTest  = $latestResult && $latestResult->started_at;
                $applicant->hasFinishedWrittenTest = $latestResult && $latestResult->finished_at;
            }

            // ✅ PERBAIKAN: Gunakan response_deadline langsung dari database
            $applicant->isOfferingExpired = false;
            
            if ($applicant->offering) {
                // Ambil deadline langsung dari response_deadline
                $applicant->deadlineDate = $applicant->offering->response_deadline;
                
                // Tentukan apakah expired
                $applicant->isOfferingExpired = 
                    $applicant->deadlineDate && 
                    now()->greaterThan($applicant->deadlineDate) &&
                    $applicant->status === 'Offering';
            }
            
            // 🔥 PERBAIKAN BARU: Hitung status technical test
            $this->calculateTechnicalTestStatus($applicant);
        });

        return response()
            ->view('history', compact('applicants'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
    
    /**
     * Calculate technical test status for applicant
     */
    private function calculateTechnicalTestStatus($applicant)
    {
        $now = now();
        
        // Ambil jadwal technical test terbaru
        $techSched = $applicant->position?->technicalSchedules?->first();
        
        if ($techSched) {
            $scheduleDate = $techSched->schedule_date;
            $uploadDeadline = $techSched->upload_deadline;
            
            // KEDUA TOMBOL AKTIF JIKA: schedule_date ≤ sekarang ≤ upload_deadline
            $isActivePeriod = false;
            
            if ($scheduleDate && $uploadDeadline) {
                // PERIODE AKTIF: antara schedule_date dan upload_deadline (inklusif)
                $isActivePeriod = $now->between($scheduleDate, $uploadDeadline, true);
            } elseif ($scheduleDate && !$uploadDeadline) {
                // Hanya schedule_date: aktif setelah schedule_date
                $isActivePeriod = $now->gte($scheduleDate);
            } elseif (!$scheduleDate && $uploadDeadline) {
                // Hanya upload_deadline: aktif sebelum deadline
                $isActivePeriod = $now->lte($uploadDeadline);
            } else {
                // Tidak ada keduanya: selalu aktif
                $isActivePeriod = true;
            }
            
            // Simpan status ke applicant object
            $applicant->techTestActive = $isActivePeriod;
            $applicant->techSchedule = $techSched;
            $applicant->techScheduleDate = $scheduleDate;
            $applicant->techUploadDeadline = $uploadDeadline;
            
            // Status message untuk user
            if (!$isActivePeriod) {
                if ($scheduleDate && $now->lt($scheduleDate)) {
                    $applicant->techStatusMessage = 'Technical test dimulai: ' . 
                        $scheduleDate->translatedFormat('l, d F Y, H:i');
                } elseif ($uploadDeadline && $now->gt($uploadDeadline)) {
                    $applicant->techStatusMessage = 'Batas waktu upload telah berakhir: ' . 
                        $uploadDeadline->translatedFormat('l, d F Y, H:i');
                }
            }
        }
    }
}