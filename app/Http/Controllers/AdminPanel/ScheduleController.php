<?php

// app/Http/Controllers/AdminPanel/ScheduleController.php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\TechnicalTestSchedule;
use App\Models\InterviewSchedule;
use App\Models\Position;

class ScheduleController extends Controller
{
    public function index()
    {
        $techSchedules = TechnicalTestSchedule::with('position')->latest()->paginate(10);
        $interviewSchedules = InterviewSchedule::with('position')->latest()->paginate(10);
        $positions = Position::with('batch')->orderBy('name')->get();

        return view('admin.schedule.index', compact(
            'techSchedules',
            'interviewSchedules',
            'positions'
        ));
    }
}

