<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Position;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $latestPositions = Position::with(['batch'])
            ->withCount('applicants')
            ->where('status', 'Active')
            ->whereHas('batch', fn($q) => $q->where('status', 'Active'))
            ->latest()
            ->take(3)
            ->get();

        $appliedBatchIds = Applicant::where('user_id', auth()->id())
            ->pluck('batch_id')
            ->toArray();

        return view('welcome', compact('latestPositions', 'appliedBatchIds'));
    }
}
