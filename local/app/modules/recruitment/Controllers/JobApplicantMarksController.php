<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobApplicantMarks;
use App\modules\recruitment\Models\JobAppliciant;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobApplicantMarksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $applicants = JobAppliciant::with(['marks'=>function($q){
                $q->select(DB::raw('*,(written+edu_training+physical+viva) as total'));
            }])->whereHas('selectedApplicant',function ($q){

            });
            if($request->exists('range')&&$request->range!='all'){
                $applicants->where('division_id',$request->range);
            }
            if($request->exists('unit')&&$request->unit!='all'){
                $applicants->where('unit_id',$request->unit);
            }
            if($request->exists('thana')&&$request->thana!='all'){
                $applicants->where('thana_id',$request->thana);
            }
            if($request->exists('q')&&$request->q){
                $applicants->where(function($q) use($request){
                   $q->orWhere('mobile_no_self',$request->q);
                   $q->orWhere('applicant_id',$request->q);
                   $q->orWhere('national_id_no',$request->q);
                });
            }
            $applicants->where('job_circular_id',$request->circular);
            $applicants->where('status','selected');
            return view('recruitment::applicant_marks.part_mark',['applicants'=>$applicants->paginate($request->limit?$request->limit:50)]);
        }
        return view('recruitment::applicant_marks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'applicant_id'=>'required',
            'written'=>'required|numeric',
//            'edu_training'=>'required|numeric',
//            'physical'=>'required|numeric',
            'viva'=>'required|numeric',
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try{
            $a = JobAppliciant::where('applicant_id',$request->applicant_id)->first();
            if($a){
                if(!$a->marks)$a->marks()->create($request->except('appicant_id'));
                else throw new \Exception('Applicant mark alredy exists');
            }
            else throw new \Exception('No applicant found');
            DB::commit();
            return response()->json(['status'=>true,'message'=>'Mark inserted successfully']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $mark = JobAppliciant::with('marks')->where('applicant_id',$id)->first();
//        return $mark;
        if($mark->marks){
            //return url()->route('recruitment.marks.update',['id'=>$mark->marks->id]);
            return view('recruitment::applicant_marks.form',['data'=>$mark->marks]);
        }
        return view('recruitment::applicant_marks.form',['applicant'=>$mark]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'written'=>'required|numeric',
//            'edu_training'=>'required|numeric',
//            'physical'=>'required|numeric',
            'viva'=>'required|numeric',
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try{
            $a = JobApplicantMarks::find($id);
            if($a){
                $a->update($request->except('applicant_id'));
            }
            else throw new \Exception('No applicant found');
            DB::commit();
            return response()->json(['status'=>true,'message'=>'Mark updated successfully']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $a = JobApplicantMarks::find($id);
            if($a){
                $a->delete();
            }
            else throw new \Exception('No applicant found');
            DB::commit();
            return response()->json(['status'=>true,'message'=>'Mark deleted successfully']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }
    }
}
