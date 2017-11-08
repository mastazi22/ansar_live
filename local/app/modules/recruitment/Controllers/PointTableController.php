<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobApplicantPoints;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PointTableController extends Controller
{
    //
    public function index(){
        return view('recruitment::applicant_point.index');
    }
    public function getPointsField(Request $request){
        if($request->ajax()){
            return response()->json(DB::connection('recruitment')->getSchemaBuilder()->getColumnListing("job_applicant"));
        }
        return abort(401);
    }

    public function store(Request $request){

        $rules = [
            'min_value'=>'required',
            'min_point'=>'required',
            'per_unit_point'=>'required',
            'field_name'=>'required'
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try{
            $p = JobApplicantPoints::create($request->except(['_token','type','action_user_id']));
            DB::commit();
            return response()->json(['status'=>true,'message'=>'point added successfully','data'=>$p]);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }
    }
    public function delete($id,Request $request){

        DB::beginTransaction();
        try{
            $p = JobApplicantPoints::find($id);
            $p->delete();
            return response()->json(['status'=>true,'message'=>'point deleted successfully']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>true,'message'=>$e->getMessage()]);
        }
    }
}
