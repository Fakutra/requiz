<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date');

        $logs = collect(); // default kosong

        if ($date) {
            $logs = ActivityLog::with('user')
                ->whereDate('created_at', $date)
                ->orderByDesc('created_at')
                ->paginate(50)
                ->appends(['date' => $date]);
        }

        return view('admin.logs.index', compact('logs', 'date'));
    }
}
