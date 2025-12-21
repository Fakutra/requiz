<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\Skregis;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Throwable;
use Illuminate\Support\Facades\Validator;

class SkregisController extends Controller
{
    public function index()
    {
        $items = Skregis::orderBy('id', 'asc')->get();
        return view('admin.skregis.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content'     => ['required', Rule::in(['judul', 'list'])],
            'title'       => 'nullable|required_if:content,list|max:255',
            'description' => 'required|string',
        ]);

        // ❌ Validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menambahkan SK. Periksa kembali input kamu.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $item = Skregis::create($validator->validated());

            return response()->json([
                'status'  => true,
                'message' => 'SK berhasil ditambahkan 🔥',
                'data'    => $item
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menambahkan SK.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Skregis $skregis)
    {
        $validator = Validator::make($request->all(), [
            'content'     => ['required', Rule::in(['judul', 'list'])],
            'title'       => 'nullable|required_if:content,list|max:255',
            'description' => 'required|string',
        ]);

        // ❌ Validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengupdate SK. Periksa kembali input.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $skregis->update($validator->validated());

            return response()->json([
                'status'  => true,
                'message' => 'SK berhasil diupdate 🚀',
                'data'    => $skregis
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat mengupdate SK.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Skregis $skregis)
    {
        try {
            $skregis->delete();

            return response()->json([
                'status'  => true,
                'message' => 'SK berhasil dihapus 💀'
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus SK.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
