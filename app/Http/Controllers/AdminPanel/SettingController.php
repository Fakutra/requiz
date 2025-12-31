<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data settings
        $setting = Setting::first();

        return view('admin/setting/manualbook', compact('setting'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'manual_book' => 'required|mimes:pdf|max:10000',
            'type' => 'required|in:applicant,admin'
        ]);

        $setting = Setting::first() ?? new Setting();
        $file = $request->file('manual_book');

        // Tentukan folder dan kolom database berdasarkan type
        if ($request->type === 'applicant') {
            $path = $file->store('manual_books/applicant', 'public');
            $setting->manual_book_path = $path;
        } else {
            $path = $file->store('manual_books/admin', 'public');
            $setting->admin_manual_path = $path;
        }

        $setting->save();

        return back()->with('success', 'Manual Book ' . ucfirst($request->type) . ' berhasil diperbarui!');
    }

    public function download($type)
    {
        $setting = Setting::first();
        $path = ($type === 'admin') ? $setting->admin_manual_path : $setting->manual_book_path;

        // dd($path, storage_path('app/public/' . $path), file_exists(storage_path('app/public/' . $path)));

        // Bersihkan path
        $path = trim(str_replace('[null]', '', $path));

        // Ambil path absolut di server
        $fullPathOnServer = storage_path('app/public/' . $path);

        // Gunakan fungsi PHP native file_exists untuk tes
        if (!empty($path) && file_exists($fullPathOnServer)) {
            $namaFile = ($type === 'admin') ? 'Manual_Book_Admin.pdf' : 'Manual_Book_Applicant.pdf';
            return response()->download($fullPathOnServer, $namaFile, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return back()->with('error', 'File tidak ditemukan di: ' . $fullPathOnServer);
    }

    public function destroy($type)
    {
        $setting = Setting::first();
        if (!$setting) return back()->with('error', 'Data tidak ditemukan.');

        // Pilih kolom berdasarkan type
        $column = ($type === 'admin') ? 'admin_manual_path' : 'manual_book_path';
        $path = $setting->$column;

        if ($path) {
            // Hapus file dari storage
            Storage::disk('public')->delete($path);

            // Kosongkan kolom di database
            $setting->$column = null;
            $setting->save();

            return back()->with('success', "Manual Book " . ucfirst($type) . " berhasil dihapus.");
        }

        return back()->with('error', 'File tidak ada');
    }
}
