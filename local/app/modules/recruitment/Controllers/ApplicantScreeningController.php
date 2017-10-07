<?php

namespace App\modules\recruitment\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ApplicantScreeningController extends Controller
{
    public function index(){
        return view('recruitment::applicant.index');
    }
}
