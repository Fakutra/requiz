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
            'type' => 'required|in:applicant,admin,vendor'
        ]);

        $setting = Setting::first() ?? new Setting();
        $file = $request->file('manual_book');
        $type = $request->type;

        // Tentukan folder penyimpanan
        $path = $file->store("manual_books/$type", 'public');

        // Tentukan kolom mana yang diisi berdasarkan type
        match ($type) {
            'applicant' => $setting->manual_book_path = $path,
            'admin'     => $setting->admin_manual_path = $path,
            'vendor'    => $setting->vendor_manual_path = $path,
        };

        $setting->user_id = auth()->id();
        $setting->save();

        return back()->with('success', 'Manual Book ' . ucfirst($type) . ' berhasil di-upload!');
    }

    public function download($type)
    {
        $setting = Setting::first();

        // 1. Tentukan path berdasarkan type (Admin, Vendor, atau Applicant)
        $path = match ($type) {
            'admin'     => $setting->admin_manual_path,
            'vendor'    => $setting->vendor_manual_path, // Pastikan kolom ini sudah ada di DB
            'applicant' => $setting->manual_book_path,
            default     => null
        };

        if (!$path) return back()->with('error', 'Path file tidak terkonfigurasi');

        // Bersihkan path dan ambil path absolut
        $path = trim(str_replace('[null]', '', $path));
        $fullPathOnServer = storage_path('app/public/' . $path);

        // 2. Cek fisik file dan tentukan nama download-nya
        if (!empty($path) && file_exists($fullPathOnServer)) {
            $namaFile = match ($type) {
                'admin'     => 'Manual_Book_Admin.pdf',
                'vendor'    => 'Manual_Book_Vendor.pdf',
                'applicant' => 'Manual_Book_Applicant.pdf',
                default     => 'Manual_Book.pdf'
            };

            return response()->download($fullPathOnServer, $namaFile, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return back()->with('error', 'File tidak ditemukan di server');
    }

    public function destroy($type)
    {
        $setting = Setting::first();
        if (!$setting) return back()->with('error', 'Data setting tidak ditemukan');

        // Pilih kolom database berdasarkan type
        $column = match ($type) {
            'admin'     => 'admin_manual_path',
            'vendor'    => 'vendor_manual_path',
            'applicant' => 'manual_book_path',
            default     => null
        };

        if (!$column) return back()->with('error', 'Role tidak dikenali');

        $path = $setting->$column;

        if ($path) {
            // Hapus file fisik dari storage/app/public/
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            // Kosongkan value di database
            $setting->$column = null;
            $setting->save();

            return back()->with('success', "Manual Book " . ucfirst($type) . " berhasil dihapus");
        }

        return back()->with('error', 'File memang sudah tidak ada');
    }
}
