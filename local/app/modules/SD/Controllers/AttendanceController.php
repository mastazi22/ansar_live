<?php

namespace App\modules\SD\Controllers;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\SD\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
                $attendance->where('month','=',$request->month);
            }
            if($request->year){
                $attendance->where('year','=',$request->year);
            }
            if(!$request->ansar_id ){
                $type = "count";
                $data = collect($attendance->select(DB::raw("SUM(is_present=1) as total_present"),DB::raw("SUM(is_present=0) as total_absent"),DB::raw("SUM(is_leave=1) as total_leave"),'day')
                    ->groupBy('day')
                    ->get());
            }else{
                $type = "view";
                $data = $attendance->get();
                $ansar_id = $request->ansar_id;
            }
            $first_date = Carbon::parse("01-{$request->month}-{$request->year}");
            return view('SD::attendance.data',compact('first_date','data','type','ansar_id'));

        }
        return view('SD::attendance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->ajax()){
            $rules = [
                "range"=>'required',
                "unit"=>'required',
                "thana"=>'required',
                "kpi"=>'required',
                "attendance_date"=>'required',
            ];
            $this->validate($request,$rules);
            $attendance = KpiGeneralModel::with(['attendance']);
                if($request->range&&$request->range!='all'){
                    $attendance->where('division_id',$request->range);
                }
                    if($request->unit&&$request->unit!='all'){
                        $attendance->where('unit_id',$request->unit);
                    }
                    if($request->thana&&$request->thana!='all'){
                        $attendance->where('thana_id',$request->thana);
                    }
                    if($request->kpi&&$request->kpi!='all'){
                        $attendance->where('id',$request->kpi);
                    }
            if($request->attendance_date) {
                $d = Carbon::parse($request->attendance_date)->format('d');
                $m = Carbon::parse($request->attendance_date)->format('m');
                $y = Carbon::parse($request->attendance_date)->format('Y');
                $attendance->whereHas('attendance',function ($q) use($d,$m,$y){
                    $q->where('day', $d);
                    $q->where('month', $m);
                    $q->where('year', $y);
                    $q->where('is_attendance_taken', 0);

                });
            }
            DB::enableQueryLog();
            $data = $attendance->first();
                    /*return DB::getQueryLog();
            return $data;*/
            $date = $request->attendance_date;
            return view('SD::attendance.create_data',compact('date','data'));

        }
        return view('SD::attendance.create');
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
