<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterviewSchedule;
use App\Models\Position;

class InterviewScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = InterviewSchedule::with('position')
            ->orderByDesc('schedule_start')
            ->paginate(10)
            ->withQueryString();

        $positions = Position::with('batch')
                    ->join('batches', 'positions.batch_id', '=', 'batches.id')
                    ->orderBy('batches.name')
                    ->orderBy('positions.name')
                    ->select('positions.*')
                    ->get();

        return view('admin.interview-schedule.index', compact('schedules', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'position_id'    => ['required','exists:positions,id'],
            'schedule_start' => ['required','date'],
            'schedule_end'   => ['required','date','after:schedule_start'],
            'zoom_link'      => ['nullable','url'],
            'zoom_id'        => ['nullable','string','max:191'],
            'zoom_passcode'  => ['nullable','string','max:191'],
            'keterangan'     => ['nullable','string'],
        ]);

        InterviewSchedule::create($data);

        return back()->with('success', 'Interview schedule berhasil dibuat.');
    }

    public function update(Request $request, InterviewSchedule $schedule)
    {
        $data = $request->validate([
            'position_id'    => ['required','exists:positions,id'],
            'schedule_start' => ['required','date'],
            'schedule_end'   => ['required','date','after:schedule_start'],
            'zoom_link'      => ['nullable','url'],
            'zoom_id'        => ['nullable','string','max:191'],
            'zoom_passcode'  => ['nullable','string','max:191'],
            'keterangan'     => ['nullable','string'],
        ]);

        $schedule->update($data);

        return back()->with('success', 'Interview schedule berhasil diperbarui.');
    }

    public function destroy(InterviewSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Interview schedule dihapus.');
    }
}
