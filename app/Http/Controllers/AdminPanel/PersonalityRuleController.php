<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalityRuleController extends Controller
{
    public function index()
    {
        $rules = DB::table('personality_rules')
            ->orderBy('min_percentage')
            ->get();

        return view('admin.personality-rules.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100',
            'score_value'    => 'required|integer|min:0',
        ]);

        DB::table('personality_rules')->insert([
            'min_percentage' => $request->min_percentage,
            'max_percentage' => $request->max_percentage,
            'score_value'    => $request->score_value,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Rule berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100',
            'score_value'    => 'required|integer|min:0',
        ]);

        DB::table('personality_rules')->where('id', $id)->update([
            'min_percentage' => $request->min_percentage,
            'max_percentage' => $request->max_percentage,
            'score_value'    => $request->score_value,
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Rule berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('personality_rules')->where('id', $id)->delete();
        return back()->with('success', 'Rule berhasil dihapus.');
    }
}
