<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobApplicationInstruction;
use App\modules\recruitment\Models\JobEducationInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class RecruitmentController extends Controller
{
    //

    public function index(Request $request)
    {
        return view('recruitment::index');
    }

    public function educationList(){
        return JobEducationInfo::pluck('education_deg_bng','id');
    }
    public function aplicationInstruction(Request $request){
        $data = JobApplicationInstruction::all()->first();
        if(strcasecmp($request->method(),'post')==0){
            if(!$data){
                $data = new JobApplicationInstruction;
            }
            $data->instruction = $request->instruction;
            $data->save();
            if($data){
                return redirect()->back()->with(['success'=>'Instruction write successfully']);
            }
            return redirect()->back()->with(['error'=>'An error occur while writing. Please try again later']);

        }
        return view('recruitment::instruction',['data'=>$data]);
    }
}
