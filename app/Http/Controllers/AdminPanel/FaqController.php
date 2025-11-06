<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $data = $request->validate([
            'question'  => ['required', 'string', 'max:255'],
            'answer'    => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // default aktif jika tidak dikirim
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        Faq::create($data);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question'  => ['required', 'string', 'max:255'],
            'answer'    => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // jika checkbox tidak dicentang, field tidak terkirim → false
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $faq->update($data);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil diperbarui.');
    }


    /**
     * Delete
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil dihapus.');
    }
}
