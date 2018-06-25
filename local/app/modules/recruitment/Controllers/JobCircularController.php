<?php

namespace App\modules\recruitment\Controllers;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
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
        $units = District::where('id','!=',0)->get();
        $range = Division::where('id','!=',0)->get();
        return view('recruitment::job_circular.create', ['categories' => $job_categories,'units'=>$units,'ranges'=>$range]);
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
            'pay_amount' => 'required',
            'job_category_id' => 'required|regex:/^[1-9]?[1-9]+$/',
            'start_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'end_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/']
        ];

        $this->validate($request, $rules);
        DB::beginTransaction();
        try {
            $request['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $request['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            $request['applicatn_units'] = implode(',',$request->applicatn_units);
            $request['applicatn_range'] = implode(',',$request->applicatn_range);
            $request['payment_status'] = !$request->payment_status?'off':$request->payment_status;
            $request['application_status'] = !$request->application_status?'off':$request->application_status;
            $request['login_status'] = !$request->login_status?'off':$request->login_status;
            $request['circular_status'] = !$request->circular_status?'shutdown':$request->circular_status;
            $request['quota_district_division']=!$request->quota_district_division?'off':$request->quota_district_division;
            $c = JobCategory::find($request->job_category_id)->circular()->create($request->except(['job_category_id', 'constraint']));
            $c->constraint()->create(['constraint' => $request->constraint]);
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
        $data = JobCircular::with('constraint')->find($id);
        $units = District::where('id','!=',0)->get();
        $range = Division::where('id','!=',0)->get();
        return view('recruitment::job_circular.edit', ['categories' => $job_categories, 'data' => $data,'units'=>$units,'ranges'=>$range]);

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
        $rules = [
            'circular_name' => 'required',
            'pay_amount' => 'required',
            'job_category_id' => 'required|regex:/^[1-9]?[1-9]+$/',
            'start_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'end_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/']
        ];
        $this->validate($request, $rules);
        DB::beginTransaction();
        try {
            $request['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $request['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            if (!$request->exists('status')) $request['status'] = 'inactive';
            if (!$request->exists('auto_terminate')) $request['auto_terminate'] = '0';
            $request['applicatn_units'] = implode(',',$request->applicatn_units);
            $request['applicatn_range'] = implode(',',$request->applicatn_range);
            $request['payment_status'] = !$request->payment_status?'off':$request->payment_status;
            $request['login_status'] = !$request->login_status?'off':$request->login_status;
            $request['application_status'] = !$request->application_status?'off':$request->application_status;
            $request['circular_status'] = !$request->circular_status?'shutdown':$request->circular_status;
            $request['quota_district_division']=!$request->quota_district_division?'off':$request->quota_district_division;
            $c = JobCircular::find($id);
            $c->update($request->except('constraint'));
            if ($c->constraint) $c->constraint()->update(['constraint' => $request->constraint]);
            else  $c->constraint()->create(['constraint' => $request->constraint]);
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
    public function constraint($id){
        try{
            $circular = JobCircular::findOrFail($id);
            $constraint  = $circular->constraint->constraint;
            return $constraint;
        }catch(\Exception $e){
            return Response::json(['message'=>'invalid circular'],404);
        }
    }

    private function searchData($request)
    {
        $data = '';
        if ($request->exists('q') && $request->q) {
            $q = $request->q;
            if(!$data){
                $data = JobCircular::with('category')->where(function ($query) use($q){
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
        if($request->exists('status')&&$request->status!='all'){
            if($data) $data->where('circular_status',$request->status);
            else $data = JobCircular::with('category')->where('circular_status',$request->status);
        }
        if($request->exists('category_id')&&$request->category_id){
            if($data) $data->where('job_category_id',$request->category_id);
            else $data = JobCircular::with('category')->where('job_category_id',$request->category_id);
        }
        if($data) {
            $data = $data->get();
            return response()->json($data);
        }
        else return response()->json(JobCircular::with('category')->get());
    }
}
