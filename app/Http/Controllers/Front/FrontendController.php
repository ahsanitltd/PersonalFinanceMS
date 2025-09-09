<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    function dashboard()
    {
        return view('admin.dashboard');
    }

    function company()
    {
        return view('admin.company.index');
    }

    function investmentEntity()
    {
        return view('admin.investmentEntity.index');
    }
}
