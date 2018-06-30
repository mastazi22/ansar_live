<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobCircular;
use App\modules\recruitment\Models\JobCircularMarkDistribution;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobCircularMarkDistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mark_distributions = JobCircularMarkDistribution::with('circular')->get();
        return view('recruitment::job_circular_mark_distribution.index', compact('mark_distributions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $circulars = JobCircular::pluck('circular_name', 'id');
        $circulars = $circulars->prepend('--Select a circular--', '');
        return view('recruitment::job_circular_mark_distribution.create', compact('circulars'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, JobCircularMarkDistribution::rules());
        DB::beginTransaction();
        try {
            $circular = JobCircular::findOrFail($request->job_circular_id);
            $circular->markDistribution()->create($this->sanitizeFormData($request->all()));
            DB::commit();
        } catch (\Exception $e) {
            return redirect()->route('recruitment.mark_distribution.index')->with('session_error', $e->getMessage());
        }
        return redirect()->route('recruitment.mark_distribution.index')->with('session_success', 'New mark distribution added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
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
        $data = JobCircularMarkDistribution::find($id);
        $circulars = JobCircular::pluck('circular_name', 'id');
        return view('recruitment::job_circular_mark_distribution.edit', compact('circulars', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, JobCircularMarkDistribution::rules($id));
        DB::beginTransaction();
        try {
            $mark_distribution = JobCircularMarkDistribution::findOrFail($id);
            $mark_distribution->update($this->sanitizeFormData($request->all()));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('recruitment.mark_distribution.index')->with('session_error', $e->getMessage());
        }
        return redirect()->route('recruitment.mark_distribution.index')->with('session_success', 'Mark distribution updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function sanitizeFormData($formData = array())
    {
        $defaultData = array(
            "physical" => null,
            "edu_training" => null,
            "edu_experience" => null,
            "written" => null,
            "convert_written_mark" => null,
            "written_pass_mark" => null,
            "viva" => null,
            "viva_pass_mark" => null,
            "physical_age" => null,
        );
        if ($formData == null || !is_array($formData)) return $defaultData;
        //set default value for disabled form fields
        foreach ($defaultData as $key => $value) {
            if (!array_key_exists($key, $formData)) {
                $formData = array_merge($formData, array($key => $value));
            }
        }
        //bypass empty fields with null value
        foreach ($formData as $key => $value) {
            if (empty($formData[$key])) {
                $formData[$key] = null;
            }
        }
        //unset checkbox fields
        unset($formData["is_physical_checkbox"],$formData["is_physical_and_age_checkbox"], $formData["is_education_and_training_checkbox"], $formData["is_education_and_experience_checkbox"], $formData["is_written_checkbox"], $formData["is_viva_checkbox"]);
        return $formData;
    }
}