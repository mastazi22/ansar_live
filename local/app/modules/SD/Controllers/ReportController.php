<?php

namespace App\modules\SD\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function avubShareReport(Request $request){
        if(strcasecmp($request->method(),'get')==0){
            return view("SD::reports.avub_share_report");
        } else if(strcasecmp($request->method(),'post')==0){

        }
    }
}
