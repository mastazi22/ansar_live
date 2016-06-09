<?php

namespace App\modules\SD\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SDController extends Controller
{
    public function index(){
        return view('SD::index');
    }
    public function demandSheet(){
        return "This is demand sheet";
    }
    public function attendanceSheet(){
        return "This is attendance sheet";
    }
    public function sdConstant(){
        return "This is constant sheet";
    }
    public function salarySheet(){
        return "This is salary sheet";
    }

}
