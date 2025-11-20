<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        // nanti diisi list vendor (paginate/search dsb)
        return view('admin.vendor.index');
    }
}
