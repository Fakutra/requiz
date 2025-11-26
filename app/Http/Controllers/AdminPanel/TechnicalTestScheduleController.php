<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\TechnicalTestSchedule;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use App\Models\InterviewSchedule;

class TechnicalTestScheduleController extends Controller
{
    public function index()
    {
        // data untuk TAB TECH
        $techSchedules = TechnicalTestSchedule::with('position')
            ->orderByDesc('schedule_date')
            ->paginate(15);

        // data untuk TAB INTERVIEW
        $interviewSchedules = InterviewSchedule::with('position')
            ->orderByDesc('schedule_start')
            ->paginate(10);

        // posisi (dipakai dua-duanya)
        $positions = Position::with('batch')
            ->join('batches', 'positions.batch_id', '=', 'batches.id')
            ->orderBy('batches.name')
            ->orderBy('positions.name')
            ->select('positions.*')
            ->get();

        $activeTab = 'tech'; // ⬅️ default tab aktif

        return view('admin.schedule.index', compact(
            'techSchedules',
            'interviewSchedules',
            'positions',
            'activeTab'
        ));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'position_id'     => ['required', 'exists:positions,id'],
            'schedule_date'   => ['required', 'date'],
            'zoom_link'       => ['required', 'url'],
            'zoom_id'         => ['required', 'string', 'max:100'],
            'zoom_passcode'   => ['nullable', 'string', 'max:100'],
            'keterangan'      => ['nullable', 'string'],
            'upload_deadline' => ['nullable', 'date', 'after_or_equal:schedule_date'],
        ]);

        $schedule = TechnicalTestSchedule::create($data);

        // ✅ LOG CREATE
        $positionName = $schedule->position->name ?? 'Unknown Position';
        $scheduleDate = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
        ActivityLogger::log(
            'create',
            'Technical Test Schedule',
            auth()->user()->name . " membuat jadwal technical test untuk posisi '{$positionName}' pada tanggal {$scheduleDate}",
            "Schedule ID: {$schedule->id}"
        );

        return redirect()->route('tech-schedule.index')->with('success', 'Schedule berhasil dibuat.');
    }

    public function update(Request $request, TechnicalTestSchedule $schedule)
    {
        $data = $request->validate([
            'position_id'     => ['required', 'exists:positions,id'],
            'schedule_date'   => ['required', 'date'],
            'zoom_link'       => ['required', 'url'],
            'zoom_id'         => ['nullable', 'string', 'max:100'],
            'zoom_passcode'   => ['nullable', 'string', 'max:100'],
            'keterangan'      => ['nullable', 'string'],
            'upload_deadline' => ['nullable', 'date', 'after_or_equal:schedule_date'],
        ]);

        // ✅ Ambil data lama
        $oldData = $schedule->only([
            'position_id', 'schedule_date', 'zoom_link', 'zoom_id',
            'zoom_passcode', 'keterangan', 'upload_deadline'
        ]);

        // ✅ Update dan ambil perubahan
        $schedule->update($data);
        $changes = $schedule->getChanges();

        if (!empty($changes)) {
            $formattedChanges = collect($changes)->map(function ($new, $key) use ($oldData) {
                $old = $oldData[$key] ?? '(kosong)';

                // Format tanggal agar mudah dibaca
                if (str_contains($key, 'date') || str_contains($key, 'deadline')) {
                    try {
                        $old = $old ? Carbon::parse($old)->format('Y-m-d') : '(kosong)';
                        $new = $new ? Carbon::parse($new)->format('Y-m-d') : '(kosong)';
                    } catch (\Exception $e) {}
                }

                return "{$key}: '{$old}' → '{$new}'";
            })->implode(', ');

            $positionName = $schedule->position->name ?? 'Unknown Position';
            ActivityLogger::log(
                'update',
                'Technical Test Schedule',
                (auth()->user()->name ?? 'System') .
                " memperbarui jadwal technical test untuk posisi '{$positionName}' — {$formattedChanges}",
                "Schedule ID: {$schedule->id}"
            );
        }

        return redirect()->route('tech-schedule.index')->with('success', 'Schedule diperbarui.');
    }

    public function destroy(TechnicalTestSchedule $schedule)
    {
        $positionName = $schedule->position->name ?? 'Unknown Position';
        $scheduleId = $schedule->id;
        $scheduleDate = Carbon::parse($schedule->schedule_date)->format('Y-m-d');

        $schedule->delete();

        // ✅ LOG DELETE
        ActivityLogger::log(
            'delete',
            'Technical Test Schedule',
            auth()->user()->name . " menghapus jadwal technical test untuk posisi '{$positionName}' pada tanggal {$scheduleDate}",
            "Schedule ID: {$scheduleId}"
        );

        return redirect()->route('tech-schedule.index')->with('success', 'Schedule dihapus.');
    }
}
