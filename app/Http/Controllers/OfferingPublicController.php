<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offering;

class OfferingPublicController extends Controller
{
    public function show($token)
    {
        $offering = Offering::where('token', $token)->with('applicant')->firstOrFail();
        return view('offering.detail', compact('offering'));
    }

    public function submit(Request $request, $token)
    {
        $offering = Offering::where('token', $token)->with('applicant')->firstOrFail();

        $data = $request->validate(['action'=>'required|in:accept,decline']);

        if ($data['action'] === 'accept') {
            $offering->response = 'accepted';
            $offering->response_at = now();
            $offering->applicant->status = 'Menerima Offering';
        } else {
            $offering->response = 'declined';
            $offering->response_at = now();
            $offering->applicant->status = 'Menolak Offering';
        }

        $offering->save();
        $offering->applicant->save();

        return view('offering.result',['status'=>$offering->response]);
    }
}
