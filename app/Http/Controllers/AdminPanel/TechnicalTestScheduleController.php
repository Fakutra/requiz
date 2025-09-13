<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\TechnicalTestSchedule;
use Illuminate\Http\Request;

class TechnicalTestScheduleController extends Controller
{
    public function index()
    {
        // Ambil daftar schedule dan posisi (untuk dropdown pada modal create/edit)
        $schedules = TechnicalTestSchedule::with('position')
            ->orderByDesc('schedule_date')
            ->paginate(15);

        $positions = Position::orderBy('name')->get(['id','name']);

        return view('admin.tech_schedule.index', compact('schedules', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'position_id'     => ['required','exists:positions,id'],
            'schedule_date'   => ['required','date'],
            'zoom_link'       => ['required','url'],
            'zoom_id'         => ['nullable','string','max:100'],
            'zoom_passcode'   => ['nullable','string','max:100'],
            'keterangan'      => ['nullable','string'],
            'upload_deadline' => ['nullable','date','after_or_equal:schedule_date'],
        ]);

        TechnicalTestSchedule::create($data);

        return redirect()->route('tech-schedule.index')->with('success', 'Schedule berhasil dibuat.');
    }

    public function update(Request $request, TechnicalTestSchedule $schedule)
    {
        $data = $request->validate([
            'position_id'     => ['required','exists:positions,id'],
            'schedule_date'   => ['required','date'],
            'zoom_link'       => ['required','url'],
            'zoom_id'         => ['nullable','string','max:100'],
            'zoom_passcode'   => ['nullable','string','max:100'],
            'keterangan'      => ['nullable','string'],
            'upload_deadline' => ['nullable','date','after_or_equal:schedule_date'],
        ]);

        $schedule->update($data);

        return redirect()->route('tech-schedule.index')->with('success', 'Schedule diperbarui.');
    }

    public function destroy(TechnicalTestSchedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('tech-schedule.index')->with('success', 'Schedule dihapus.');
    }
}
