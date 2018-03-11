<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobApplicantPoints;
use App\modules\recruitment\Models\JobCircular;
use App\modules\recruitment\Models\JobEducationInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApplicantMarksRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $points = JobApplicantPoints::with('circular')->get();
        return view('recruitment::applicant_point.index',compact('points'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rules_name = JobApplicantPoints::rulesName();
        $rules_for = JobApplicantPoints::rulesFor();
        $circulars = JobCircular::pluck('circular_name','id')->prepend('--Select a circular','');
        $educations = JobEducationInfo::select(DB::raw('GROUP_CONCAT(education_deg_bng) as education_name'),'priority','id')
        ->groupBy('priority')->get();
//        return $education;
        return view('recruitment::applicant_point.create',compact('circulars','rules_name','rules_for','educations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        return $request->all();
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
        //
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
