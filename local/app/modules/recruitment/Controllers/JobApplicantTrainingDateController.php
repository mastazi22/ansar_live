<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobApplicantTrainingDate;
use App\modules\recruitment\Models\JobCircular;
use App\modules\recruitment\Models\JobCircularMarkDistribution;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobApplicantTrainingDateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mark_distributions = JobApplicantTrainingDate::with('circular')->get();
        return view('recruitment::job_applicant_training_date.index',compact('mark_distributions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $circulars = JobCircular::pluck('circular_name','id');
        $circulars = $circulars->prepend('--Select a circular--','');
        return view('recruitment::job_applicant_training_date.create',compact('circulars'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,JobApplicantTrainingDate::rules());
        DB::beginTransaction();
        try{
            $circular = JobCircular::findOrFail($request->job_circular_id);
            $circular->trainingDate()->create($request->all());
            DB::commit();
        }catch(\Exception $e){
            return redirect()->route('recruitment.training.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route('recruitment.training.index')->with('session_success','New mark distribution added successfully');
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
        $data = JobApplicantTrainingDate::find($id);
        $circulars = JobCircular::pluck('circular_name','id');
        return view('recruitment::job_applicant_training_date.edit',compact('circulars','data'));
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
        $this->validate($request,JobApplicantTrainingDate::rules($id));
        DB::beginTransaction();
        try{
            $mark_distribution = JobApplicantTrainingDate::findOrFail($id);
            $mark_distribution->update($request->all());
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.training.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route('recruitment.training.index')->with('session_success','Mark distribution updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
