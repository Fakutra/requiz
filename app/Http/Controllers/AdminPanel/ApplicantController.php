<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;

class ApplicantController extends Controller
{
    public function index()
    {
        $applicant = Applicant::orderBy('id', 'asc')->get();
        return view('admin.applicant.index', compact('applicant'));
    }
}
