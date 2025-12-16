<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\TechnicalTestSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use App\Models\InterviewSchedule;

class TechnicalTestScheduleController extends Controller
{
    public function index()
    {
        $techSchedules = TechnicalTestSchedule::with('position')
            ->orderByDesc('schedule_date')
            ->paginate(15);

        $interviewSchedules = InterviewSchedule::with('position')
            ->orderByDesc('schedule_start')
            ->paginate(10);

        $positions = Position::with('batch')
            ->join('batches', 'positions.batch_id', '=', 'batches.id')
            ->orderBy('batches.name')
            ->orderBy('positions.name')
            ->select('positions.*')
            ->get();

        $activeTab = 'tech';

        return view('admin.schedule.index', compact(
            'techSchedules',
            'interviewSchedules',
            'positions',
            'activeTab'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position_id'     => ['required', 'exists:positions,id'],
            'schedule_date'   => ['required', 'date'],
            'zoom_link'       => ['required', 'url'],
            'zoom_id'         => ['required', 'string', 'max:100'],
            'zoom_passcode'   => ['nullable', 'string', 'max:100'],
            'keterangan'      => ['nullable', 'string'],
            'upload_deadline' => ['nullable', 'date', 'after_or_equal:schedule_date'],
        ]);

        // ❌ VALIDATION FAIL
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal membuat schedule. Periksa kembali data yang diinput.');
        }

        try {
            $data = $validator->validated();
            $schedule = TechnicalTestSchedule::create($data);

            // LOG
            $positionName = $schedule->position->name ?? 'Unknown Position';
            $scheduleDate = Carbon::parse($schedule->schedule_date)->format('Y-m-d');

            ActivityLogger::log(
                'create',
                'Technical Test Schedule',
                auth()->user()->name . " membuat jadwal technical test untuk posisi '{$positionName}' pada tanggal {$scheduleDate}",
                "Schedule ID: {$schedule->id}"
            );

            return redirect()
                ->route('tech-schedule.index')
                ->with('success', 'Schedule berhasil dibuat.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat schedule. Silakan coba lagi.');
        }
    }

    public function update(Request $request, TechnicalTestSchedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'position_id'     => ['required', 'exists:positions,id'],
            'schedule_date'   => ['required', 'date'],
            'zoom_link'       => ['required', 'url'],
            'zoom_id'         => ['nullable', 'string', 'max:100'],
            'zoom_passcode'   => ['nullable', 'string', 'max:100'],
            'keterangan'      => ['nullable', 'string'],
            'upload_deadline' => ['nullable', 'date', 'after_or_equal:schedule_date'],
        ]);

        // ❌ VALIDATION FAIL
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui schedule. Periksa kembali data yang diinput.');
        }

        try {
            $data = $validator->validated();

            // old data for log
            $oldData = $schedule->only([
                'position_id', 'schedule_date', 'zoom_link', 'zoom_id',
                'zoom_passcode', 'keterangan', 'upload_deadline'
            ]);

            $schedule->update($data);

            // changes for log
            $changes = $schedule->getChanges();

            if (!empty($changes)) {
                $formattedChanges = collect($changes)->map(function ($new, $key) use ($oldData) {
                    $old = $oldData[$key] ?? '(kosong)';

                    if (str_contains($key, 'date') || str_contains($key, 'deadline')) {
                        try {
                            $old = $old ? Carbon::parse($old)->format('Y-m-d') : '(kosong)';
                            $new = $new ? Carbon::parse($new)->format('Y-m-d') : '(kosong)';
                        } catch (\Exception $e) {}
                    }

                    return "{$key}: '{$old}' → '{$new}'";
                })->implode(', ');

                ActivityLogger::log(
                    'update',
                    'Technical Test Schedule',
                    auth()->user()->name .
                    " memperbarui jadwal technical test untuk posisi '{$schedule->position->name}' — {$formattedChanges}",
                    "Schedule ID: {$schedule->id}"
                );
            }

            return redirect()
                ->route('tech-schedule.index')
                ->with('success', 'Schedule diperbarui.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui schedule. Silakan coba lagi.');
        }
    }

    public function destroy(TechnicalTestSchedule $schedule)
    {
        try {
            $positionName = $schedule->position->name ?? 'Unknown Position';
            $scheduleId   = $schedule->id;
            $scheduleDate = Carbon::parse($schedule->schedule_date)->format('Y-m-d');

            $schedule->delete();

            ActivityLogger::log(
                'delete',
                'Technical Test Schedule',
                auth()->user()->name . " menghapus jadwal technical test untuk posisi '{$positionName}' pada tanggal {$scheduleDate}",
                "Schedule ID: {$scheduleId}"
            );

            return redirect()
                ->route('tech-schedule.index')
                ->with('success', 'Schedule dihapus.');

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('tech-schedule.index')
                ->with('error', 'Terjadi kesalahan saat menghapus schedule. Silakan coba lagi.');
        }
    }
}
