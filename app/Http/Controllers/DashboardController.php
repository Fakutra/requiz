<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Position;
use App\Models\Faq;
use App\Models\AboutUs;
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

        $faqs = Faq::where('is_active', true)
            ->latest('id')
            ->get();

        $aboutBlocks = AboutUs::orderBy('id')->get();


        return view('welcome', compact('latestPositions', 'appliedBatchIds','faqs','aboutBlocks'));
    }
}
