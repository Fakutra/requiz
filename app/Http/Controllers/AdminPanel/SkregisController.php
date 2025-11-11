<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\Skregis;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class SkregisController extends Controller
{
    // nampilin halaman index (list data & modal)
    public function index()
    {
        $items = Skregis::orderBy('id', 'asc')->get();
        return view('admin.skregis.index', compact('items'));
    }

    // create data (modal add)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content'     => ['required', Rule::in(['judul', 'list'])],
            'title'       => 'nullable|required_if:content,list|max:255',
            'description' => 'required|string',
            // 'order'       => 'nullable|integer'
        ]);

        $item = Skregis::create([
            'content'     => $validated['content'],
            'title'       => $validated['title'],
            'description' => $validated['description'],
            // 'order'       => $validated['order'] ?? (Skregis::max('order') + 1),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'SK berhasil ditambahkan 🔥',
            'data'    => $item
        ]);
    }

    // update data (modal edit)
    public function update(Request $request, Skregis $skregis)
    {
        $validated = $request->validate([
            'content'     => ['required', Rule::in(['judul', 'list'])],
            'title'       => 'nullable|required_if:content,list|max:255',
            'description' => 'required|string',
            // 'order'       => 'nullable|integer'
        ]);

        $skregis->update([
            'content'     => $validated['content'],
            'title'       => $validated['title'],
            'description' => $validated['description'],
            // 'order'       => $validated['order'] ?? $skregis->order,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'SK berhasil diupdate 🚀',
            'data'    => $skregis
        ]);
    }

    // delete data
    public function destroy(Skregis $skregis)
    {
        $skregis->delete();

        return response()->json([
            'status'  => true,
            'message' => 'SK berhasil dihapus 💀'
        ]);
    }
}
