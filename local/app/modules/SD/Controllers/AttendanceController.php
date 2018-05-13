<?php

namespace App\modules\SD\Controllers;

use App\modules\SD\Models\Attendance;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $rules = [
                "month"=>'required',
                "range"=>'required_if:ansar_id,'.null,
                "unit"=>'required_if:ansar_id,'.null,
                "thana"=>'required_if:ansar_id,'.null,
                "kpi"=>'required_if:ansar_id,'.null,
                "year"=>'required|regex:/^[0-9]{4}$/',
            ];
            $this->validate($request,$rules);
            $attendance = Attendance::with(['kpi'])
            ->whereHas('kpi',function ($q) use($request){
                if($request->range&&$request->range!='all'){
                    $q->where('division_id',$request->range_id);
                }
                if($request->unit&&$request->unit!='all'){
                    $q->where('unit_id',$request->unit_id);
                }
                if($request->thana&&$request->thana!='all'){
                    $q->where('thana_id',$request->thana_id);
                }
                if($request->kpi&&$request->kpi!='all'){
                    $q->where('id',$request->kpi_id);
                }
            });
            if($request->ansar_id){
                $attendance->where('ansar_id',$request->ansar_id);
            }
            if($request->month){
                $attendance->whereMonth('attendance_date','=',$request->month);
            }
            if($request->year){
                $attendance->whereYear('attendance_date','=',$request->year);
            }
            return response()->json($attendance->get());

        }
        return view('SD::attendance.index');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
