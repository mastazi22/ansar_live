<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobApplicantMarks;
use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\JobCircular;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobApplicantMarksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            DB::enableQueryLog();
            $applicants = JobAppliciant::with(['marks' => function ($q) {
                $q->select(DB::raw('*,(ifnull(written,0)+ifnull(edu_training,0)+ifnull(edu_experience,0)+ifnull(physical,0)+ifnull(viva,0)+ifnull(physical_age,0)) as total'));

            }]);
            if($request->type=='fail'){
                /*$applicants->whereHas('marks.failedApplicants', function ($q) {
                });*/
                $applicants->join('job_applicant_marks','job_applicant_marks.applicant_id','=','job_applicant.applicant_id')
                ->join('job_circular','job_circular.id','=','job_applicant.job_circular_id')
                    ->join('job_circular_mark_distribution','job_circular_mark_distribution.job_circular_id','=','job_circular.id')
                ->where(function($q){
                    $q->where(DB::raw('(convert_written_mark*written_pass_mark)/100'),'>',DB::raw('job_applicant_marks.written'));
                    $q->orWhere(DB::raw('(job_circular_mark_distribution.viva*viva_pass_mark)/100'),'>',DB::raw('job_applicant_marks.viva'));
                });
            }
            else if($request->type=='pass'){
                /*$applicants->whereHas('marks.passedApplicants', function ($q) {
                });*/
                $applicants->join('job_applicant_marks','job_applicant_marks.applicant_id','=','job_applicant.applicant_id')
                    ->join('job_circular','job_circular.id','=','job_applicant.job_circular_id')
                    ->join('job_circular_mark_distribution','job_circular_mark_distribution.job_circular_id','=','job_circular.id')
                    ->where(function($q){
                        $q->where(DB::raw('(convert_written_mark*written_pass_mark)/100'),'<=',DB::raw('job_applicant_marks.written'));
                        $q->where(DB::raw('(job_circular_mark_distribution.viva*viva_pass_mark)/100'),'<=',DB::raw('job_applicant_marks.viva'));
                    });
            } else if($request->type=="mark_not_entry"){
                $applicants->whereNotIn('job_applicant.applicant_id',JobApplicantMarks::pluck('applicant_id'));
//                $applicants->whereHas('selectedApplicant', function ($q) {
//                });
                $applicants->leftJoin('job_applicant_marks as marks','marks.applicant_id','=','job_applicant.applicant_id');
            }else{
//                $applicants->whereHas('selectedApplicant', function ($q) {
//                });
                $applicants->leftJoin('job_applicant_marks as marks','marks.applicant_id','=','job_applicant.applicant_id');
            }
            if ($request->exists('range') && $request->range != 'all') {
                $applicants->whereEqualIn('division_id', $request->range);
            }
            if ($request->exists('unit') && $request->unit != 'all') {
                $applicants->whereEqualIn('unit_id', $request->unit);
            }
            if ($request->exists('thana') && $request->thana != 'all') {
                $applicants->whereEqualIn('thana_id', $request->thana);
            }
            if ($request->exists('q') && $request->q) {
                $applicants->where(function ($q) use ($request) {
                    $q->orWhere('mobile_no_self', $request->q);
                    $q->orWhere('job_applicant.applicant_id', '=', $request->q);
                    $q->orWhere('national_id_no', '=', $request->q);
                    $q->orWhere(DB::raw('CAST(ansar_id AS CHAR)'), '=', $request->q);
                });
            }
            $applicants->where('job_applicant.job_circular_id', $request->circular);
            $mark_distribution = JobCircular::find($request->circular)->markDistribution;
//            return $mark_distribution;
//            dd($applicants->get());
            $applicants->where('job_applicant.status', 'selected')->select('job_applicant.*');
//            $d = $applicants->get();
//            return DB::getQueryLog();
            return view('recruitment::applicant_marks.part_mark', ['applicants' => $applicants->paginate($request->limit ? $request->limit : 50),'mark_distribution'=>$mark_distribution]);
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $rules = [
//            'applicant_id' => 'required',
//            'written' => 'required|numeric',
//            'edu_training' => 'required|numeric',
//            'physical' => 'required|numeric',
//            'viva' => 'required|numeric',
//        ];
        $rules = [
            'applicant_id' => 'required',
            'written' => 'numeric',
            'edu_training' => 'numeric',
            'physical' => 'numeric',
            'viva' => 'numeric',
        ];
        $this->validate($request, $rules);
        DB::beginTransaction();
        try {
            $a = JobAppliciant::where('applicant_id', $request->applicant_id)->first();
            if ($a) {
                if (!$a->marks) {
//                    $a->marks()->create($this->sanitizeFormData($request->except('applicant_id')));
                    $a->marks()->create($this->sanitizeFormData($request->all()));
                } else
                    throw new \Exception('Applicant mark alredy exists');
            } else
                throw new \Exception('No applicant found');
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Mark inserted successfully']);
        } catch (\Exception $e) {
            DB::rollback();
//            return $e;
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    private function sanitizeFormData($formData = array())
    {
        $defaultData = array(
            "physical" => 0,
            "edu_training" => 0,
            "written" => 0,
            "viva" => 0,
            'additional_marks' => null,
            'total_aditional_marks' => 0
        );

        if ($formData == null || !is_array($formData)) return $defaultData;
        if (isset($formData['additional_marks'])) {
            $tadm = 0;
            foreach ($formData['additional_marks'] as $key => $adm) {
                $tadm += floatval(array_values($adm)[0]);
            }
            $formData['additional_marks'] = serialize($formData['additional_marks']);
            $formData['total_aditional_marks'] = $tadm;
        }
        //set default value for disabled form fields
        foreach ($defaultData as $key => $value) {
            if (!array_key_exists($key, $formData)) {
                $formData = array_merge($formData, array($key => $value));
            }
        }

        //bypass empty fields with null value
        foreach ($formData as $key => $value) {
            if (empty($formData[$key])) {
                $formData[$key] = 0;
            }
        }

        return $formData;
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
        //
        $mark = JobAppliciant::with(['marks', 'circular' => function ($q) {
            $q->with('markDistribution');
        }])->where('applicant_id', $id)->first();
//        return $mark;
        if ($mark->marks) {
            //return url()->route('recruitment.marks.update',['id'=>$mark->marks->id]);
            return view('recruitment::applicant_marks.form', ['data' => $mark->marks, 'mark_distribution' => $mark->circular->markDistribution]);
        }
        return view('recruitment::applicant_marks.form', ['applicant' => $mark, 'mark_distribution' => $mark->circular->markDistribution]);
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
//        $rules = [
//            'written' => 'required|numeric',
//            'edu_training' => 'required|numeric',
//            'physical' => 'required|numeric',
//            'viva' => 'required|numeric',
//        ];
        $rules = [
            'written' => 'numeric',
            'edu_training' => 'numeric',
            'physical' => 'numeric',
            'viva' => 'numeric',
        ];
        $this->validate($request, $rules);
        DB::beginTransaction();
        try {
            $a = JobApplicantMarks::find($id);
            if ($a) {
                $a->update($this->sanitizeFormData($request->except('applicant_id')));
            } else throw new \Exception('No applicant found');
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Mark updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $a = JobApplicantMarks::find($id);
            if ($a) {
                $a->delete();
            } else throw new \Exception('No applicant found');
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Mark deleted successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
