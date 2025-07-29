<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $applicants = Applicant::with('position')
                        ->where('user_id', auth()->id())
                        ->latest()
                        ->get();

        return view('history', compact('applicants'));
    }
}
