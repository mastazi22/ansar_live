<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            return $this->getData($request);
        }
        return view('recruitment::job_category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('recruitment::job_category.create');
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
            'category_name_eng'=>'required',
            'category_name_bng'=>'required'
        ];
        $this->validate($request,$rules);

        if(!$request->exists('status')) $request['status']='inactive';
        DB::beginTransaction();
        try{

            $c = JobCategory::create($request->all());
            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.category.index')->with('session_error',"An error occur while create new category. Please try agin later");
//            return redirect()->route('recruitment.category.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route('recruitment.category.index')->with('session_success',"New category added successfully");
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
        $data = JobCategory::find($id);
        return view('recruitment::job_category.edit',compact('data'));
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
        $rules = [
            'category_name_eng'=>'required'
        ];
        $this->validate($request,$rules);

        if(!$request->exists('status')) $request['status']='inactive';
        DB::beginTransaction();
        try{

            $c = JobCategory::find($id);
            $c->update($request->all());
            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
//            return redirect()->route('recruitment.category.index')->with('session_error',"An error occur while create new category. Please try agin later");
            return redirect()->route('recruitment.category.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route('recruitment.category.index')->with('session_success',"New category added successfully");
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

    private function getData($request){
        if($request->exists('q')&&$request->q){
            $data = JobCategory::where('category_name_eng','like',"%{$request->q}%")
                ->orWhere('category_name_bng','like',"%{$request->q}%")
                ->orWhere('category_description','like',"%{$request->q}%")
                ->orWhere('status','=',$request->q)
                ->get();
            return response()->json($data);
        }
        return response()->json(JobCategory::all());
    }
}
