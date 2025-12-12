<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::orderBy('name')->get();

        return view('admin.master.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('admin.master.jobs.form', [
            'job' => new Job(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:jobs,name',
        ]);

        Job::create($validated);

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Job $job)
    {
        return view('admin.master.jobs.form', [
            'job' => $job,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:jobs,name,' . $job->id,
        ]);

        $job->update($validated);

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Job $job)
    {
        $job->delete();

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
