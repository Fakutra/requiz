<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function index()
    {
        $items = AboutUs::orderBy('id')->get();
        return view('admin.about.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'layout'      => 'required|in:image_left,image_right,full_image',
            'image'       => 'nullable|image|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('about', 'public');
            }

            AboutUs::create($data);

            return redirect()
                ->route('admin.about.index')
                ->with('success', 'Blok “Tentang Kami” berhasil dibuat.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat blok Tentang Kami. Silakan coba lagi.');
        }
    }

    public function update(Request $request, AboutUs $about)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'layout'      => 'required|in:image_left,image_right,full_image',
            'image'       => 'nullable|image|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($about->image_path && Storage::disk('public')->exists($about->image_path)) {
                    Storage::disk('public')->delete($about->image_path);
                }

                $data['image_path'] = $request->file('image')->store('about', 'public');
            }

            $about->update($data);

            return redirect()
                ->route('admin.about.index')
                ->with('success', 'Blok berhasil diperbarui.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui blok. Silakan coba lagi.');
        }
    }

    public function destroy(AboutUs $about)
    {
        try {
            if ($about->image_path) {
                Storage::disk('public')->delete($about->image_path);
            }

            $about->delete();

            return back()->with('success', 'Blok berhasil dihapus.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Gagal menghapus blok. Silakan coba lagi.');
        }
    }
}
