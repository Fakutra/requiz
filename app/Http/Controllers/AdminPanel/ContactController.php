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

        $contacts = $q->orderByDesc('is_active')
                    ->orderByDesc('updated_at')
                    ->paginate(10);

        return view('admin.contact.index', compact('contacts'));
    }

    public function scopeActive($q) 
    { 
        return $q->where('is_active', true); 
    }

    protected function activeCount(): int
    {
        return Contact::where('is_active', true)->count();
    }

    public function store(Request $request)
    {
        try {
            $data = $this->validated($request);

            DB::transaction(function () use (&$data) {
                if (!empty($data['is_active'])) {
                    if ($this->activeCount() >= 3) {
                        throw ValidationException::withMessages([
                            'is_active' => 'Maksimal 3 kontak boleh aktif. Nonaktifkan salah satu sebelum mengaktifkan yang baru.',
                        ]);
                    }
                }
                Contact::create($data);
            });

            $this->bustFooterCache();
            return back()->with('ok', 'Kontak ditambahkan.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
                         ->withInput()
                         ->with('err', 'Gagal menambahkan kontak.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('err', 'Terjadi kesalahan saat menambahkan kontak.');
        }
    }


    public function update(Request $request, Contact $contact)
    {
        try {
            $data = $this->validated($request);

            DB::transaction(function () use (&$data, $contact) {
                $willActivate = !empty($data['is_active']);
                $currentlyActive = (bool) $contact->is_active;

                if ($willActivate && !$currentlyActive) {
                    if ($this->activeCount() >= 3) {
                        throw ValidationException::withMessages([
                            'is_active' => 'Maksimal 3 kontak boleh aktif. Nonaktifkan salah satu sebelum mengaktifkan yang baru.',
                        ]);
                    }
                }

                $contact->update($data);
            });

            $this->bustFooterCache();
            return back()->with('ok', 'Kontak diperbarui.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
                         ->withInput()
                         ->with('err', 'Gagal memperbarui kontak.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('err', 'Terjadi kesalahan saat memperbarui kontak.');
        }
    }


    public function destroy(Contact $contact)
    {
        try {
            if ($contact->is_active &&
                !Contact::where('id','!=',$contact->id)->where('is_active', true)->exists()) {
                return back()->with('err','Kontak aktif tidak bisa dihapus. Nonaktifkan dulu.');
            }

            $contact->delete();
            $this->bustFooterCache();

            return back()->with('ok','Kontak dihapus.');

        } catch (\Throwable $e) {
            report($e);
            return back()->with('err','Terjadi kesalahan saat menghapus kontak.');
        }
    }


    public function setActive(Contact $contact)
    {
        try {
            if ($contact->is_active) {
                return back()->with('ok','Kontak ini sudah aktif.');
            }

            if ($this->activeCount() >= 3) {
                return back()->with('err','Maksimal 3 kontak boleh aktif. Nonaktifkan salah satu dulu.');
            }

            $contact->update(['is_active' => true]);
            $this->bustFooterCache();

            return back()->with('ok','Kontak dijadikan aktif.');

        } catch (\Throwable $e) {
            report($e);
            return back()->with('err', 'Terjadi kesalahan saat mengaktifkan kontak.');
        }
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
