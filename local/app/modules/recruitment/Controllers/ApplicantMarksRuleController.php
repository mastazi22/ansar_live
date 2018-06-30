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
        return view('recruitment::applicant_point.index', compact('points'));
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
        $circulars = JobCircular::pluck('circular_name', 'id')->prepend('--Select a circular', '');
        $educations = JobEducationInfo::select(DB::raw('GROUP_CONCAT(education_deg_bng) as education_name'), 'priority', 'id')
            ->groupBy('priority')->get();
//        return $education;
        return view('recruitment::applicant_point.create', compact('circulars', 'rules_name', 'rules_for', 'educations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'job_circular_id' => 'required',
            'point_for' => 'required',
            'rule_name' => 'required',
        ];
        $this->validate($request, $rules);
        $rules['job_circular_id'] = 'required|unique:job_applicant_points,job_circular_id,NULL,id,rule_name,' . $request->rule_name;
        if ($request->rule_name === 'education') {
            $rules['edu_point.*.priority'] = 'required';
            $rules['edu_point.*.point'] = 'required';
            $rules['edu_p_count'] = 'required';
        }
        if ($request->rule_name === 'height') {
            $rules['min_height_feet'] = 'required';
            $rules['min_height_inch'] = 'required';
            $rules['min_point'] = 'required';
            $rules['max_height_feet'] = 'required';
            $rules['max_height_inch'] = 'required';
            $rules['max_point'] = 'required';
        }
        if ($request->rule_name === 'training') {
            $rules['training_point'] = 'required';
        }
        if ($request->rule_name === 'experience') {
            $rules['min_experience_years'] = 'required|numeric';
            $rules['min_exp_point'] = 'required|numeric';
            $rules['max_experience_years'] = 'required|numeric';
            $rules['max_exp_point'] = 'required|numeric';
        }
        $this->validate($request, $rules);
        $data = [];
        if ($request->rule_name === 'education') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['edu_point', 'edu_p_count']));

        }
        if ($request->rule_name === 'height') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['min_height_feet', 'min_height_inch', 'min_point', 'max_height_feet', 'max_height_inch', 'max_point']));
        }
        if ($request->rule_name === 'training') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['training_point']));
        }
        if ($request->rule_name === 'experience') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['min_experience_years', 'min_exp_point', 'max_experience_years', 'max_exp_point']));
        }
        if ($request->rule_name === 'age') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['min_age_years', 'min_age_point', 'max_age_years', 'max_age_point']));
        }
        DB::beginTransaction();
        try {
            JobApplicantPoints::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('recruitment.marks_rules.index')->with('session_error', $e->getMessage());
        }
        return redirect()->route('recruitment.marks_rules.index')->with('session_success', 'Rules added  successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = JobApplicantPoints::find($id);
        $data = collect($data)->merge($data->rules);
        $rules_name = JobApplicantPoints::rulesName();
        $rules_for = JobApplicantPoints::rulesFor();
        $circulars = JobCircular::pluck('circular_name', 'id')->prepend('--Select a circular', '');
        $educations = JobEducationInfo::select(DB::raw('GROUP_CONCAT(education_deg_bng) as education_name'), 'priority', 'id')
            ->groupBy('priority')->get();
//        return $education;
        return view('recruitment::applicant_point.edit', compact('data', 'circulars', 'rules_name', 'rules_for', 'educations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'job_circular_id' => 'required',
            'point_for' => 'required',
            'rule_name' => 'required',
        ];
        $this->validate($request, $rules);
        $rules['job_circular_id'] = 'required|unique:job_applicant_points,job_circular_id,' . $id . ',id,rule_name,' . $request->rule_name;
        if ($request->rule_name === 'education') {

            $rules['edu_point.*.priority'] = 'required';
            $rules['edu_point.*.point'] = 'required';
            $rules['edu_p_count'] = 'required';
        }
        if ($request->rule_name === 'height') {
            $rules['min_height_feet'] = 'required';
            $rules['min_height_inch'] = 'required';
            $rules['min_point'] = 'required';
            $rules['max_height_feet'] = 'required';
            $rules['max_height_inch'] = 'required';
            $rules['max_point'] = 'required';
        }
        if ($request->rule_name === 'training') {
            $rules['training_point'] = 'required';
        }
        if ($request->rule_name === 'experience') {
            $rules['min_experience_years'] = 'required|numeric';
            $rules['min_exp_point'] = 'required|numeric';
            $rules['max_experience_years'] = 'required|numeric';
            $rules['max_exp_point'] = 'required|numeric';
        }
        $this->validate($request, $rules);
        $data = [];
        if ($request->rule_name === 'education') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['edu_point', 'edu_p_count']));

        }
        if ($request->rule_name === 'height') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['min_height_feet', 'min_height_inch', 'min_point', 'max_height_feet', 'max_height_inch', 'max_point']));
        }
        if ($request->rule_name === 'training') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['training_point']));
        }
        if ($request->rule_name === 'experience') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only(['min_experience_years', 'min_exp_point', 'max_experience_years', 'max_exp_point']));
        }
        DB::beginTransaction();
        try {
            $p = JobApplicantPoints::findOrFail($id);
            $p->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('recruitment.marks_rules.index')->with('session_error', $e->getMessage());
        }
        return redirect()->route('recruitment.marks_rules.index')->with('session_success', 'Rules updated  successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }
}