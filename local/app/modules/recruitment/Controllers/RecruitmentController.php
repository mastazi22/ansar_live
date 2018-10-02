<?php

namespace App\modules\recruitment\Controllers;

use App\Http\Controllers\Controller;
use App\modules\recruitment\Models\JobApplicationInstruction;
use App\modules\recruitment\Models\JobEducationInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecruitmentController extends Controller
{
    //

    public function index(Request $request)
    {
        return view('recruitment::index');
    }

    public function educationList()
    {
        return JobEducationInfo::pluck('education_deg_bng', 'id');
    }

    public function aplicationInstruction(Request $request)
    {
        $data = JobApplicationInstruction::all();
        return view('recruitment::instruction.index', ['instructions' => $data]);
    }

    public function editApplicationInstruction(Request $request,$id)
    {
        if(strcasecmp($request->method(),'GET')==0){
            $data = JobApplicationInstruction::find($id);
            return view('recruitment::instruction.edit',compact('data'));
        }else if(strcasecmp($request->method(),'POST')==0) {
            $rules = [
                'type'=>'required|unique:recruitment.job_application_instruction,type,'.$id,
                'instruction'=>'required'
            ];
            $this->validate($request,$rules);
            DB::connection('recruitment')->beginTransaction();
            try{
                $data = JobApplicationInstruction::find($id);
                $data->instruction = $request->instruction;
                $data->save();
                DB::connection('recruitment')->commit();
            }catch(\Exception $e){
                DB::connection('recruitment')->rollback();
                return redirect()->route('recruitment.instruction')->with('error',$e->getMessage());
            }
            return redirect()->route('recruitment.instruction')->with('success','Instruction updated successfully');
        }

    }
    public function createApplicationInstruction(Request $request)
    {
       if(strcasecmp($request->method(),'GET')==0){
           return view('recruitment::instruction.create');
       }else if(strcasecmp($request->method(),'POST')==0){
           $rules = [
               'type'=>'required|unique:recruitment.job_application_instruction',
               'instruction'=>'required'
           ];
           $this->validate($request,$rules);
           DB::connection('recruitment')->beginTransaction();
           try{
               JobApplicationInstruction::create($request->only(['type','instruction']));
               DB::connection('recruitment')->commit();
           }catch(\Exception $e){
               DB::connection('recruitment')->rollback();
               return redirect()->route('recruitment.instruction')->with('error',$e->getMessage());
           }
           return redirect()->route('recruitment.instruction')->with('success','Instruction created successfully');
       }

    }
}
