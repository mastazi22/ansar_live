<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return View::make('HRM::Unit.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return View::make('HRM::Unit.create',['range'=>Division::pluck('division_name_bng','id')]);
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
            'division_id' => 'required|numeric|integer|min:0',
            'unit_name_eng' => 'required|regex:/^[a-zA-Z0-9\s_-]+$/',
            'unit_name_bng' => 'required',
            'unit_code' => 'required|numeric|integer',
        ];
        $valid  = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return Redirect::back()->withErrors($valid)->withInput($request->except('action_user_id'));
        }
        District::create($request->except('action_user_id'));
        return Redirect::route('HRM.unit.index')->with('success_message','Unit Created Successfully');
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
        return View::make('HRM::Unit.edit',['unit_info'=>District::find($id),'division'=>Division::pluck('division_name_bng','id')]);
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
            'division_id' => 'required|numeric|integer|min:0',
            'unit_name_eng' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
            'unit_name_bng' => 'required',
            'unit_code' => 'required|numeric|integer',
        ];
        $valid  = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return Redirect::back()->withErrors($valid)->withInput($request->all());
        }
        DB::beginTransaction();
        try {
            District::find($id)->update($request->all());
            DB::statement("call update_info(:did,:uid)",['did'=>$request->division_id,'uid'=>$id]);
            DB::commit();
            //Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
        } catch
        (\Exception $e) {
            DB::rollback();
            return Redirect::route('HRM.unit.index')->with('error_message', $e->getMessage());
        }
        return Redirect::route('HRM.unit.index')->with('success_message','Unit Updated Successfully');
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

    //other route

    public function allUnit(Request $request){
//        return "asadadadsad";

        DB::enableQueryLog();
        $rules = [
            'division'=>['required','regex:/^(all)|[0-9]+$/'],
            'offset'=>'required|numeric',
            'limit'=>'required|numeric'
        ];
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return Response::json(['status'=>false,'message'=>'Invalid Request']);
        }
        $q = District::with('division');
        $t = clone $q;
        if(strcasecmp($request->division,'all')){
            $data =  $q->where('division_id',$request->division)->skip($request->offset)->take($request->limit)->get();
            $total = $t->where('division_id',$request->division)->count();
            return Response::json(['total'=>$total,'index'=>$request->offset,'units'=>$data]);
        }
        $data =  $q->skip($request->offset)->take($request->limit)->get();
        $total = $t->count();
//        return DB::getQueryLog();
        return Response::json(['total'=>$total,'index'=>$request->offset,'units'=>$data]);
    }
}
