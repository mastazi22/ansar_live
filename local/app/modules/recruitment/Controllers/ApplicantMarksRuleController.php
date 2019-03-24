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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){

            if($request->circular_id!='all') {
                $points = JobApplicantPoints::with('circular')->whereHas('circular', function ($q) use ($request) {
                    $q->where('id', $request->circular_id);
                })->get();
                return view('recruitment::applicant_point.part_rules_view', compact('points'));
            }
            $points = JobApplicantPoints::with('circular')->get();
            return view('recruitment::applicant_point.part_rules_view', compact('points'));

        }
        $points = JobApplicantPoints::with('circular')->get();
//        foreach (\get_object_vars($points->rules->{'0'}) as $k=>$value){
//            return $k;
//        }
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
        $circulars = JobCircular::pluck('circular_name', 'id')->prepend('--Select a circular--', '');
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
            'job_circular_id' => 'required|exists:job_circular,id',
            'point_for' => 'required',
            'rule_name' => 'required',
        ];
//        return $request->all();
        $this->validate($request, $rules);
        $circular = JobCircular::find($request->job_circular_id);
        $constraint = json_decode($circular->constraint->constraint);
        $rules['job_circular_id'] = 'required|unique:job_applicant_points,job_circular_id,NULL,id,rule_name,' . $request->rule_name;
        if ($request->rule_name === 'education') {
            $rules['quota.*.edu_point.*.priority'] = 'required';
            $rules['quota.*edu_point.*.point'] = 'required';
            $rules['quota.*edu_p_count'] = 'required';
        }
        if ($request->rule_name === 'age') {
            foreach ($constraint as $key=>$value){
                $rules["quota.$key.min_age_years"] = "required|in:".$value->age->min;
                $rules["quota.$key.min_age_point"] = "required|numeric";
                $rules["quota.$key.max_age_years"] = "required|in:".$value->age->max;
                $rules["quota.$key.max_age_point"] = "required|numeric";
            }
        }
        if ($request->rule_name === 'height') {
            foreach ($constraint as $key=>$value){
                if($value->gender->male) {
                    $rules["quota.$key.male.min_height_feet"] = 'required|in:'.$value->height->male->feet;
                    $rules["quota.$key.male.min_height_inch"] = "required|in:".$value->height->male->inch;
                    $rules["quota.$key.male.min_point"] = "required";
                    $rules["quota.$key.male.max_height_feet"] = "required";
                    $rules["quota.$key.male.max_height_inch"] = "required";
                    $rules["quota.$key.male.max_point"] = "required";
                }
                if($value->gender->female) {
                    $rules["quota.*.$key.female.*.min_height_feet"] = 'required|in:'.$value->height->female->feet;
                    $rules["quota.*.$key.female.*.min_height_inch"] = 'required|in:'.$value->height->male->inch;
                    $rules["quota.*.$key.female.*.min_point"] = "required";
                    $rules["quota.*.$key.female.*.max_height_feet"] = "required";
                    $rules["quota.*.$key.female.*.max_height_inch"] = "required";
                    $rules["quota.*.$key.female.*.max_point"] = "required";
                }
            }
        }
        if ($request->rule_name === 'training') {
            $rules['quota.*.training_point'] = 'required';
        }
        if ($request->rule_name === 'experience') {
            $rules['quota.*.min_experience_years'] = 'required|numeric';
            $rules['quota.*.min_exp_point'] = 'required|numeric';
            $rules['quota.*.max_experience_years'] = 'required|numeric';
            $rules['quota.*.max_exp_point'] = 'required|numeric';
        }
        $this->validate($request, $rules);
        $data = [];
        if ($request->rule_name === 'education') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only('quota'));

        }
        if ($request->rule_name === 'height') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only('quota'));
        }
        if ($request->rule_name === 'training') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only('quota'));
        }
        if ($request->rule_name === 'experience') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only('quota'));
        }
        if ($request->rule_name === 'age') {
            $data['job_circular_id'] = $request->job_circular_id;
            $data['point_for'] = $request->point_for;
            $data['rule_name'] = $request->rule_name;
            $data['rules'] = json_encode($request->only('quota'));
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
        $quota = $data->rules;
        $data = collect($data)->merge(compact('quota'));
        unset($data['rules']);
//        return $data;
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
        if ($request->rule_name === 'age') {
            $rules['min_age_years'] = 'required|numeric';
            $rules['min_age_point'] = 'required|numeric';
            $rules['max_age_years'] = 'required|numeric';
            $rules['max_age_point'] = 'required|numeric';
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