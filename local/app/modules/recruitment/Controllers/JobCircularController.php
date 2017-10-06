<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobCategory;
use App\modules\recruitment\Models\JobCircular;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
        if($request->ajax()){
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
        $job_categories = JobCategory::pluck('category_name_eng','id')->prepend('--Select a job category--','0');
        return view('recruitment::job_circular.create',['categories'=>$job_categories]);
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
        $rules = [
            'circular_name'=>'required',
            'job_category_id'=>'required|regex:/^[1-9]?[1-9]+$/',
            'start_date'=>['required','regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'end_date'=>['required','regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/']
        ];

        $this->validate($request,$rules);
        DB::beginTransaction();
        try{
            $request['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $request['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            $c = JobCategory::find($request->job_category_id)->circular()->create($request->except(['job_category_id','constraint']));
            $c->constraint()->create(['constraint'=>$request->constraint]);
            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();
//            return redirect()->route('recruitment.circular.index')->with('session_error',"An error occur while create new circular. Please try again later");
            return redirect()->route('recruitment.circular.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route('recruitment.circular.index')->with('session_success',"New circular added successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $job_categories = JobCategory::pluck('category_name_eng','id')->prepend('--Select a job category--','0');
        $data = JobCircular::with('constraint')->find($id);
        return view('recruitment::job_circular.edit',['categories'=>$job_categories,'data'=>$data]);

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
            'circular_name'=>'required',
            'job_category_id'=>'required|regex:/^[1-9]?[1-9]+$/',
            'start_date'=>['required','regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'end_date'=>['required','regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/']
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try{
            $request['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $request['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            if(!$request->exists('status')) $request['status'] = 'inactive';
            if(!$request->exists('auto_terminate')) $request['auto_terminate'] = '0';
            $c = JobCircular::find($id);
            $c->update($request->except('constraint'));
            if($c->constraint) $c->constraint()->update(['constraint'=>$request->constraint]);
            else  $c->constraint()->create(['constraint'=>$request->constraint]);
            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();
//            return redirect()->route('recruitment.circular.index')->with('session_error',"An error occur while updating circular. Please try again later");
            return redirect()->route('recruitment.circular.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route('recruitment.circular.index')->with('session_success',"Circular updated successfully");
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

    private function searchData($request){
        if($request->exists('q')&&$request->q){
            $q = $request->q;
            $data = JobCircular::whereHas('category',function ($query) use($q){
                $query->where('category_name_eng','like',"%{$q}%")
                    ->orWhere('category_name_bng','like',"%{$q}%");
            })
                ->where('circular_name','like',"%{$request->q}%")
                ->orWhere('status','like',"%{$request->q}%")
                ->get();
            return response()->json($data);
        }
        return response()->json(JobCircular::with('category')->get());
    }
}
