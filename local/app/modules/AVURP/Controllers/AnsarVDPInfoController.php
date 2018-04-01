<?php

namespace App\modules\AVURP\Controllers;

use App\modules\AVURP\Models\VDPAnsarInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AnsarVDPInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('AVURP::ansar_vdp_info.create');
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
            'ansar_name_bng'=>'required',
            'ansar_name_eng'=>'required',
            'father_name_bng'=>'required',
            'mother_name_bng'=>'required',
            'designation'=>'required',
            'date_of_birth'=>'required',
            'marital_status'=>'required',
            'national_id_no'=>'required',
            'mobile_no_self'=>'required',
            'height_feet'=>'required',
            'height_inch'=>'required',
            'blood_group_id'=>'required',
            'gender'=>'required',
            'health_condition'=>'required',
            'division_id'=>'required',
            'unit_id'=>'required',
            'thana_id'=>'required',
            'union_id'=>'required',
            'union_word_id'=>'required',
            'post_office_name'=>'required',
            'village_house_no'=>'required',
            'educationInfo'=>'required',
            'training_info'=>'required',
            'educationInfo.*.education_id'=>'required',
            'educationInfo.*.institute_name'=>'required',
        ];
        $this->validate($request,$rules,[
            'educationInfo.*.education_id.required'=>'This field required',
            'educationInfo.*.institute_name.required'=>'This field required'
            ]);
        DB::beginTransaction();
        try{
            $info = VDPAnsarInfo::create($request->except('educationInfo'));
            foreach ($request->educationInfo as $education){
                $info->education()->create($education);
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['message'=>$e->getMessage()],500);
        }
        Session::flash('success_message','New entry added successfully');
        return response()->json(["success"]);
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
