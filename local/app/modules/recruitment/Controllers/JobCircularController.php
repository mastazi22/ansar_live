<?php

namespace App\modules\recruitment\Controllers;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\recruitment\Models\CircularApplicantQuota;
use App\modules\recruitment\Models\JobCategory;
use App\modules\recruitment\Models\JobCircular;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class JobCircularController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            return $this->searchData($request);
        }
        return view('recruitment::job_circular.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $job_categories = JobCategory::pluck('category_name_eng', 'id')->prepend('--Select a job category--', '0');
        $units = District::where('id', '!=', 0)->get();
        $range = Division::where('id', '!=', 0)->get();
        $circular_quota = CircularApplicantQuota::all();
        return view('recruitment::job_circular.create', ['categories' => $job_categories, 'units' => $units, 'ranges' => $range,'circular_quota'=>$circular_quota]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $rules = [
            'circular_name' => 'required',
            'memorandum_no' => 'required',
            'pay_amount' => 'required',
            'job_category_id' => 'required|regex:/^[1-9]?[1-9]+$/',
            'start_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'end_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'circular_publish_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/']
        ];

        $this->validate($request, $rules);
        DB::beginTransaction();
        try {
            $request['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $request['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            $request['circular_publish_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            $request['applicatn_units'] = implode(',', $request->applicatn_units);
            $request['applicatn_range'] = implode(',', $request->applicatn_range);
            $request['payment_status'] = !$request->payment_status ? 'off' : $request->payment_status;
            $request['application_status'] = !$request->application_status ? 'off' : $request->application_status;
            $request['login_status'] = !$request->login_status ? 'off' : $request->login_status;
            $request['admit_card_print_status'] = !$request->admit_card_print_status ? 'off' : $request->admit_card_print_status;
            $request['submit_problem_status'] = !$request->submit_problem_status ? 'off' : $request->submit_problem_status;
            $request['circular_status'] = !$request->circular_status ? 'shutdown' : $request->circular_status;
            $request['quota_district_division'] = !$request->quota_district_division ? 'off' : $request->quota_district_division;
            $c = JobCategory::find($request->job_category_id)->circular()->create($request->except(['job_category_id', 'constraint','quota_type']));
            $c->constraint()->create(['constraint' => $request->constraint]);
            if($request['quota_type']&&count($request['quota_type'])>0)$c->applicantQuotaRelation()->attach($request['quota_type']);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
//            return redirect()->route('recruitment.circular.index')->with('session_error',"An error occur while create new circular. Please try again later");
            return redirect()->route('recruitment.circular.index')->with('session_error', $e->getMessage());
        }
        return redirect()->route('recruitment.circular.index')->with('session_success', "New circular added successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $job_categories = JobCategory::pluck('category_name_eng', 'id')->prepend('--Select a job category--', '0');
        $data = JobCircular::with(['constraint','applicantQuotaRelation'])->find($id);
        $units = District::where('id', '!=', 0)->get();
        $range = Division::where('id', '!=', 0)->get();
        $circular_quota = CircularApplicantQuota::all();
//        return $circular_quota;
        return view('recruitment::job_circular.edit', ['categories' => $job_categories, 'data' => $data, 'units' => $units, 'ranges' => $range,'circular_quota'=>$circular_quota]);

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
//        return $request->all();
        $rules = [
            'circular_name' => 'required',
            'memorandum_no' => 'required',
            'pay_amount' => 'required',
            'job_category_id' => 'required|regex:/^[1-9]?[1-9]+$/',
            'start_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'end_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'circular_publish_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/']
        ];
        $this->validate($request, $rules);
        DB::beginTransaction();
        try {
            $request['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $request['circular_publish_date'] = Carbon::parse($request->circular_publish_date)->format('Y-m-d');
            $request['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            if (!$request->exists('status')) $request['status'] = 'inactive';
            if (!$request->exists('auto_terminate')) $request['auto_terminate'] = '0';
            $request['applicatn_units'] = implode(',', $request->applicatn_units);
            $request['applicatn_range'] = implode(',', $request->applicatn_range);
            $request['payment_status'] = !$request->payment_status ? 'off' : $request->payment_status;
            $request['login_status'] = !$request->login_status ? 'off' : $request->login_status;
            $request['application_status'] = !$request->application_status ? 'off' : $request->application_status;
            $request['circular_status'] = !$request->circular_status ? 'shutdown' : $request->circular_status;
            $request['admit_card_print_status'] = !$request->admit_card_print_status ? 'off' : $request->admit_card_print_status;
            $request['submit_problem_status'] = !$request->submit_problem_status ? 'off' : $request->submit_problem_status;
            $request['quota_district_division'] = !$request->quota_district_division ? 'off' : $request->quota_district_division;
            $c = JobCircular::find($id);
            $c->update($request->except(['constraint','quota_type']));
            if ($c->constraint) $c->constraint()->update(['constraint' => $request->constraint]);
            else  $c->constraint()->create(['constraint' => $request->constraint]);
            $c->applicantQuotaRelation()->detach();
            if($request['quota_type']&&count($request['quota_type'])>0)$c->applicantQuotaRelation()->attach($request['quota_type']);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
//            return redirect()->route('recruitment.circular.index')->with('session_error',"An error occur while updating circular. Please try again later");
            return redirect()->route('recruitment.circular.index')->with('session_error', $e->getMessage());
        }
        return redirect()->route('recruitment.circular.index')->with('session_success', "Circular updated successfully");
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

    public function constraint($id)
    {
        try {
            $circular = JobCircular::findOrFail($id);
            $constraint = $circular->constraint->constraint;
            return \response()->json(compact('constraint'));
        } catch (\Exception $e) {
            return \response()->json(['message' => 'invalid circular'], 404);
        }
    }
    public function quotaList(Request $request,$id)
    {
        if($request->ajax()){
            try {
                $circular = JobCircular::findOrFail($id);
                $quotaList = $circular->applicantQuotaRelation;
                return \response()->json(\compact('quotaList'));
            } catch (\Exception $e) {
                return \response()->json(['message' => 'invalid circular'], 404);
            }
        }
        return abort(403);
    }

    private function searchData($request)
    {
        $data = '';
        if ($request->exists('q') && $request->q) {
            $q = $request->q;
            if (!$data) {
                $data = JobCircular::with('category')->where(function ($query) use ($q) {
                    $query->whereHas('category', function ($query) use ($q) {
                        $query->orWhere(function ($query) use ($q) {
                            $query->where('category_name_eng', 'like', "%{$q}%");
                            $query->where('category_name_bng', 'like', "%{$q}%");
                        });
                    });
                    $query->orWhere('circular_name', 'like', "%{$q}%");
                });
            }

        }
        $categories = auth()->user()->recruitmentCatagories->pluck('id');
        if ($request->exists('status') && $request->status != 'all') {
            if ($data) $data->where('circular_status', $request->status);
            else $data = JobCircular::with('category')->where('circular_status', $request->status);
        }
        if(auth()->user()->type==111){

            if ($request->exists('category_id') && $request->category_id&&in_array($request->category_id,$categories)) {
                if ($data) $data->where('job_category_id', $request->category_id);
                else $data = JobCircular::with('category')->where('job_category_id', $request->category_id);
            }
            else{
                $data->whereIn('job_category_id',$categories);
            }
        }
        else if ($request->exists('category_id') && $request->category_id) {
            if ($data) $data->where('job_category_id', $request->category_id);
            else $data = JobCircular::with('category')->where('job_category_id', $request->category_id);
        }

        if ($data) {
            $data = $data->orderBy("created_at", "desc")->get();
            return response()->json($data);
        } else return response()->json(JobCircular::with('category')->orderBy("created_at", "desc")->get());
    }
}
