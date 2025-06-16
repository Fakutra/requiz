<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\User;

class LowonganController extends Controller
{
    public function index()
    {
        $lowongans = Position::orderBy('id', 'asc')->get();
        return view('lowongan', compact('lowongans'));
    }

    public function create()
    {
        // $user = User::class;
        $lowongans = Position::orderBy('id', 'asc')->get();
        return view('apply', compact('lowongans'));
    }
}
