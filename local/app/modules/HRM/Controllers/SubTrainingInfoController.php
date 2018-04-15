<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\MainTrainingInfo;
use App\modules\HRM\Models\SubTrainingInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SubTrainingInfoController extends Controller
{
    public function index()
    {
        $data = SubTrainingInfo::all();
        return view('HRM::sub_training_info.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $main_training = MainTrainingInfo::pluck('training_name_bng','id');
        $main_training->prepend('--Select main training--','');
        return view('HRM::sub_training_info.create',compact('main_training'));
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
            'main_training_info_id'=>'required',
//            'training_name_eng'=>'required',
            'training_name_bng'=>'required',
        ];
        $this->validate($request,$rules);
        DB::connection('hrm')->beginTransaction();
        try{
            $t = SubTrainingInfo::create($request->all());
            DB::connection('hrm')->commit();
        }catch(\Exception $e){
            DB::connection('hrm')->rollback();
            return redirect()->back()->with('error_message',$e->getMessage());
        }catch(\Throwable $e){
            DB::connection('hrm')->rollback();
            return redirect()->back()->with('error_message',$e->getMessage());
        }
        return redirect()->route('HRM.sub_training.index')->with('success_message','Information insert complete');
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
        $data = SubTrainingInfo::find($id);
        $main_training = MainTrainingInfo::pluck('training_name_bng','id');
        $main_training->prepend('--Select main training--','');
        return view('HRM::sub_training_info.edit',compact('data','main_training'));
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
            'main_training_info_id'=>'required',
//            'training_name_eng'=>'required',
            'training_name_bng'=>'required',
        ];
        $this->validate($request,$rules);
        DB::connection('hrm')->beginTransaction();
        try{
            $t = SubTrainingInfo::find($id);
            if(!$t) throw new \Exception("No training found");
            $t->update($request->all());
            DB::connection('hrm')->commit();
        }catch(\Exception $e){
            DB::connection('hrm')->rollback();
            return redirect()->back()->with('error_message',$e->getMessage());
        }
        return redirect()->route('HRM.sub_training.index')->with('success_message','Information update complete');
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
    public function getAllTraining($id)
    {
        $data = MainTrainingInfo::find($id);
        if($data) return response()->json($data->subTraining);
        return response()->json([]);
    }
}
