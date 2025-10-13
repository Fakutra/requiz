<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:jobs,name',
        ]);

        Job::create(['name' => $request->name]);

        return back()->with('success', 'Jabatan baru berhasil ditambahkan.');
    }
}
