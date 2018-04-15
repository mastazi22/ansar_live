<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\MainTrainingInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MainTrainingInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = MainTrainingInfo::all();
        return view('HRM::main_training_info.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('HRM::main_training_info.create');
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
//            'training_name_eng'=>'required',
            'training_name_bng'=>'required',
        ];
        $this->validate($request,$rules);
        DB::connection('hrm')->beginTransaction();
        try{
            $t = MainTrainingInfo::create($request->all());
            DB::connection('hrm')->commit();
        }catch(\Exception $e){
            DB::connection('hrm')->rollback();
            return redirect()->back()->with('error_messsage',$e->getMessage());
        }
        return redirect()->route('HRM.main_training.index')->with('success_messsage','Information insert complete');
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
        $data = MainTrainingInfo::find($id);
        return view('HRM::main_training_info.edit',compact('data'));
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
//            'training_name_eng'=>'required',
            'training_name_bng'=>'required',
        ];
        $this->validate($request,$rules);
        DB::connection('hrm')->beginTransaction();
        try{
            $t = MainTrainingInfo::find($id);
            if(!$t) throw new \Exception("No training found");
            $t->update($request->all());
            DB::connection('hrm')->commit();
        }catch(\Exception $e){
            DB::connection('hrm')->rollback();
            return redirect()->back()->with('error_messsage',$e->getMessage());
        }
        return redirect()->route('HRM.main_training.index')->with('success_messsage','Information update complete');
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
    public function getAllTraining(){
        $data = MainTrainingInfo::all();
        return response()->json($data);
    }
}
