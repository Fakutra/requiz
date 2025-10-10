<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Placement;

class PlacementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:placements,name',
        ]);

        Placement::create(['name' => $request->name]);

        return back()->with('success', 'Penempatan baru berhasil ditambahkan.');
    }
}
