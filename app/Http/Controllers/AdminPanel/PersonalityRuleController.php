<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalityRuleController extends Controller
{
    public function index(Request $r)
    {
        $batches = Batch::orderBy('id')->get();
        $batchId = $r->query('batch') ?: $batches->first()?->id;

        $rules = collect();
        if ($batchId) {
            $rules = DB::table('personality_rules')
                ->where('batch_id', $batchId)
                ->orderBy('min_percentage')
                ->get();
        }

        return view('admin.personality-rules.index', compact('batches', 'batchId', 'rules'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'batch_id'       => 'required|exists:batches,id',
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100',
            'score_value'    => 'required|numeric|min:0',
        ]);

        // opsional: normalisasi jika min > max dan max diisi
        if (!is_null($data['max_percentage']) && $data['min_percentage'] > $data['max_percentage']) {
            [$data['min_percentage'], $data['max_percentage']] = [$data['max_percentage'], $data['min_percentage']];
        }

        DB::table('personality_rules')->insert([
            'batch_id'       => $data['batch_id'],
            'min_percentage' => $data['min_percentage'],
            'max_percentage' => $data['max_percentage'],
            'score_value'    => $data['score_value'],
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Rule berhasil ditambahkan.');
    }

    public function update(Request $r, $id)
    {
        $data = $r->validate([
            'batch_id'       => 'required|exists:batches,id',
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100',
            'score_value'    => 'required|numeric|min:0',
        ]);

        if (!is_null($data['max_percentage']) && $data['min_percentage'] > $data['max_percentage']) {
            [$data['min_percentage'], $data['max_percentage']] = [$data['max_percentage'], $data['min_percentage']];
        }

        DB::table('personality_rules')
            ->where('id', $id)
            ->update([
                'batch_id'       => $data['batch_id'],
                'min_percentage' => $data['min_percentage'],
                'max_percentage' => $data['max_percentage'],
                'score_value'    => $data['score_value'],
                'updated_at'     => now(),
            ]);

        return back()->with('success', 'Rule berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('personality_rules')->where('id', $id)->delete();
        return back()->with('success', 'Rule dihapus.');
    }
}
