<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $q = Contact::query();

        if ($s = $request->get('q')) {
            $q->where(function($w) use ($s) {
                $w->where('narahubung','like',"%$s%")
                ->orWhere('email','like',"%$s%")
                ->orWhere('phone','like',"%$s%");
            });
        }

        // ⬇️ FIX: nggak pakai priority lagi
        $contacts = $q->orderByDesc('is_active')
                    ->orderByDesc('updated_at')
                    ->paginate(10);

        return view('admin.contact.index', compact('contacts'));
    }

    public function scopeActive($q) 
    { 
        return $q->where('is_active', true); 
    }

    // hitung aktif sekarang
    protected function activeCount(): int
    {
        return Contact::where('is_active', true)->count();
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use (&$data) {
            if (!empty($data['is_active'])) {
                if ($this->activeCount() >= 3) {
                    // sudah 3 aktif → tolak mengaktifkan yang baru
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'is_active' => 'Maksimal 3 kontak boleh aktif. Nonaktifkan salah satu sebelum mengaktifkan yang baru.',
                    ]);
                }
            }
            Contact::create($data);
        });

        $this->bustFooterCache();

        return back()->with('ok', 'Kontak ditambahkan.');
    }


    public function update(Request $request, Contact $contact)
    {
        $data = $this->validated($request);

        DB::transaction(function () use (&$data, $contact) {
            $willActivate   = !empty($data['is_active']);
            $currentlyActive = (bool) $contact->is_active;

            if ($willActivate && !$currentlyActive) {
                // mau mengaktifkan yang tadinya nonaktif
                if ($this->activeCount() >= 3) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'is_active' => 'Maksimal 3 kontak boleh aktif. Nonaktifkan salah satu sebelum mengaktifkan yang baru.',
                    ]);
                }
            }
            // Catatan: kalau mau nonaktifkan, langsung update aja (gak ada batas minimum)
            $contact->update($data);
        });

        $this->bustFooterCache();

        return back()->with('ok', 'Kontak diperbarui.');
    }


    public function destroy(Contact $contact)
    {
        if ($contact->is_active &&
            !Contact::where('id','!=',$contact->id)->where('is_active', true)->exists()) {
            return back()->with('err','Aktif tidak bisa dihapus. Nonaktifkan terlebih dahulu.');
        }

        $contact->delete();
        $this->bustFooterCache();

        return back()->with('ok','Kontak dihapus.');
    }

    // Action ringkas untuk tombol "Jadikan Aktif"
    public function setActive(Contact $contact)
    {
        if ($contact->is_active) {
            return back()->with('ok','Kontak ini sudah aktif.');
        }

        if ($this->activeCount() >= 3) {
            return back()->with('err','Maksimal 3 kontak boleh aktif. Nonaktifkan salah satu dulu.');
        }

        $contact->update(['is_active' => true]);

        $this->bustFooterCache();

        return back()->with('ok','Kontak dijadikan aktif.');
    }


    // ==== helpers ====
    protected function validated(Request $request): array
    {
        return $request->validate([
            'narahubung' => ['nullable','string','max:100'],
            'email'      => ['nullable','email','max:255'],
            'phone'      => ['nullable','string','max:30'],
            'jam_operasional' => ['nullable','string','max:100'],
            'is_active'  => ['sometimes','boolean'],
        ]);
    }


    protected function bustFooterCache(): void
    {
        Cache::forget('footer_contacts');
    }
}
