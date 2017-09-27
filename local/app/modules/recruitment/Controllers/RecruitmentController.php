<?php

namespace App\modules\recruitment\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RecruitmentController extends Controller
{
    //

    public function index(Request $request)
    {
        return view('recruitment::index');
    }
}
