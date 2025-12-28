<?php

namespace App\Livewire;

use App\Models\Position;
use Livewire\Component;
use Livewire\WithPagination;

class JobSearch extends Component
{

    use WithPagination;

    public $q = '';
    public $edu = '';

    // Reset halaman ke 1 setiap kali filter berubah
    public function updatingQ()
    {
        $this->resetPage();
    }
    public function updatingEdu()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Pindahkan logika controller ke sini
        $positions = Position::query()
            ->withCount('applicants')
            ->where('status', 'Active')
            ->whereHas('batch', function ($query) {
                $query->where('status', 'Active');
            })
            // Filter teks (Gunakan properti $this->q)
            ->when($this->q, function ($query) {
                $query->where(function ($qq) {
                    $q = trim($this->q);
                    $qq->where('name', 'ILIKE', "%{$q}%")
                        ->orWhere('slug', 'ILIKE', "%{$q}%")
                        ->orWhere('description', 'ILIKE', "%{$q}%");
                });
            })
            // Filter pendidikan (Gunakan properti $this->edu)
            ->when($this->edu, function ($query) {
                $query->where('pendidikan_minimum', $this->edu);
            })
            ->orderBy('id', 'asc')
            ->paginate(9);

        return view('livewire.job-search', [
            'positions' => $positions
        ]);
    }
}
