<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\User;

class LowonganController extends Controller
{
    public function index()
    {
        // Hitung jumlah pelamar per posisi
        $lowongans = Position::withCount('applicant')
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();

        return view('lowongan', compact('lowongans'));
    }

    public function create()
    {
        // $user = User::class;
        $lowongans = Position::orderBy('id', 'asc')->get();
        return view('apply', compact('lowongans'));
    }
}
