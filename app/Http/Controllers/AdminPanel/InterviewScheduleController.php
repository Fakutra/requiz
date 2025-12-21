<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterviewSchedule;
use App\Models\Position;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use App\Models\TechnicalTestSchedule;
use Illuminate\Support\Facades\Validator;

class InterviewScheduleController extends Controller
{
    public function index(Request $request)
    {
        // data untuk TAB TECH
        $techSchedules = TechnicalTestSchedule::with('position')
            ->orderByDesc('schedule_date')
            ->paginate(15);

        // data untuk TAB INTERVIEW
        $interviewSchedules = InterviewSchedule::with('position')
            ->orderByDesc('schedule_start')
            ->paginate(10)
            ->withQueryString();

        $positions = Position::with('batch')
            ->join('batches', 'positions.batch_id', '=', 'batches.id')
            ->orderBy('batches.name')
            ->orderBy('positions.name')
            ->select('positions.*')
            ->get();

        $activeTab = 'interview'; // ⬅️ langsung buka tab Interview

        return view('admin.schedule.index', compact(
            'techSchedules',
            'interviewSchedules',
            'positions',
            'activeTab'
        ));
    }

    public function store(Request $request)
    {
        // 🔍 VALIDASI MANUAL
        $validator = Validator::make($request->all(), [
            'position_id'    => ['required','exists:positions,id'],
            'schedule_start' => ['required','date'],
            'schedule_end'   => ['required','date','after:schedule_start'],
            'zoom_link'      => ['required','url'],
            'zoom_id'        => ['nullable','string','max:191'],
            'zoom_passcode'  => ['nullable','string','max:191'],
            'keterangan'     => ['nullable','string'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal membuat jadwal interview. Periksa kembali data yang diinput.');
        }

        try {
            $data = $validator->validated();

            $schedule = InterviewSchedule::create($data);

            // ✅ LOG CREATE
            $positionName = $schedule->position->name ?? 'Unknown Position';
            $start = Carbon::parse($schedule->schedule_start)->format('Y-m-d H:i');
            $end   = Carbon::parse($schedule->schedule_end)->format('Y-m-d H:i');

            ActivityLogger::log(
                'create',
                'Interview Schedule',
                auth()->user()->name . " membuat jadwal interview untuk posisi '{$positionName}' pada {$start} s.d. {$end}",
                "Schedule ID: {$schedule->id}"
            );

            return back()->with('success', 'Interview schedule berhasil dibuat.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat jadwal interview. Silakan coba lagi.');
        }
    }

    public function update(Request $request, InterviewSchedule $schedule)
    {
        // 🔍 VALIDASI MANUAL
        $validator = Validator::make($request->all(), [
            'position_id'    => ['required','exists:positions,id'],
            'schedule_start' => ['required','date'],
            'schedule_end'   => ['required','date','after:schedule_start'],
            'zoom_link'      => ['nullable','url'],
            'zoom_id'        => ['nullable','string','max:191'],
            'zoom_passcode'  => ['nullable','string','max:191'],
            'keterangan'     => ['nullable','string'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui jadwal interview. Periksa kembali data yang diinput.');
        }

        try {
            $data = $validator->validated();

            // ✅ Simpan data lama untuk perbandingan
            $oldData = $schedule->only([
                'position_id', 'schedule_start', 'schedule_end',
                'zoom_link', 'zoom_id', 'zoom_passcode', 'keterangan'
            ]);

            // ✅ Update dan ambil perubahan
            $schedule->update($data);
            $changes = $schedule->getChanges();

            if (!empty($changes)) {
                $formattedChanges = collect($changes)->map(function ($new, $key) use ($oldData) {
                    $old = $oldData[$key] ?? '(kosong)';

                    // Format tanggal biar rapi di log
                    if (str_contains($key, 'schedule_start') || str_contains($key, 'schedule_end')) {
                        try {
                            $old = $old ? Carbon::parse($old)->format('Y-m-d H:i') : '(kosong)';
                            $new = $new ? Carbon::parse($new)->format('Y-m-d H:i') : '(kosong)';
                        } catch (\Exception $e) {}
                    }

                    return "{$key}: '{$old}' → '{$new}'";
                })->implode(', ');

                $positionName = $schedule->position->name ?? 'Unknown Position';
                ActivityLogger::log(
                    'update',
                    'Interview Schedule',
                    (auth()->user()->name ?? 'System') .
                    " memperbarui jadwal interview untuk posisi '{$positionName}' — {$formattedChanges}",
                    "Schedule ID: {$schedule->id}"
                );
            }

            return back()->with('success', 'Interview schedule berhasil diperbarui.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui jadwal interview. Silakan coba lagi.');
        }
    }

    public function destroy(InterviewSchedule $schedule)
    {
        try {
            $positionName = $schedule->position->name ?? 'Unknown Position';
            $start = Carbon::parse($schedule->schedule_start)->format('Y-m-d H:i');
            $end   = Carbon::parse($schedule->schedule_end)->format('Y-m-d H:i');
            $id    = $schedule->id;

            $schedule->delete();

            // ✅ LOG DELETE
            ActivityLogger::log(
                'delete',
                'Interview Schedule',
                auth()->user()->name . " menghapus jadwal interview untuk posisi '{$positionName}' pada {$start} s.d. {$end}",
                "Schedule ID: {$id}"
            );

            return back()->with('success', 'Interview schedule dihapus.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus jadwal interview. Silakan coba lagi.');
        }
    }
}
