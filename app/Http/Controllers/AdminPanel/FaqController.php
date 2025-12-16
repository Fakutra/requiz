<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    /**
     * List + Create form (pakai satu halaman)
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $faqs = Faq::when($q, function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('question', 'like', "%{$q}%")
                            ->orWhere('answer', 'like', "%{$q}%");
                    });
                })
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString();

        return view('admin.faq.index', compact('faqs', 'q'));
    }

    public function store(Request $request)
    {
        // 🔍 VALIDASI MANUAL
        $validator = Validator::make($request->all(), [
            'question'  => ['required', 'string', 'max:255'],
            'answer'    => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan FAQ. Periksa kembali input kamu.');
        }

        try {
            $data = $validator->validated();

            // default active
            $data['is_active'] = (bool) ($data['is_active'] ?? true);

            Faq::create($data);

            return redirect()
                ->route('admin.faq.index')
                ->with('success', 'FAQ berhasil ditambahkan.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan FAQ. Silakan coba lagi.');
        }
    }

    public function update(Request $request, Faq $faq)
    {
        // 🔍 VALIDASI MANUAL
        $validator = Validator::make($request->all(), [
            'question'  => ['required', 'string', 'max:255'],
            'answer'    => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui FAQ. Periksa kembali data yang diinput.');
        }

        try {
            $data = $validator->validated();

            // checkbox unchecked berarti false
            $data['is_active'] = (bool) ($data['is_active'] ?? false);

            $faq->update($data);

            return redirect()
                ->route('admin.faq.index')
                ->with('success', 'FAQ berhasil diperbarui.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui FAQ. Silakan coba lagi.');
        }
    }

    /**
     * Delete
     */
    public function destroy(Faq $faq)
    {
        try {
            $faq->delete();

            return redirect()
                ->route('admin.faq.index')
                ->with('success', 'FAQ berhasil dihapus.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Gagal menghapus FAQ. Silakan coba lagi.');
        }
    }
}
