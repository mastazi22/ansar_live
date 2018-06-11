<?php

namespace App\modules\SD\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\SD\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        if ($request->ajax()) {
            $rules = [
                "month" => 'required',
                "range" => 'required_if:ansar_id,' . null,
                "unit" => 'required_if:ansar_id,' . null,
                "thana" => 'required_if:ansar_id,' . null,
                "kpi" => 'required_if:ansar_id,' . null,
                "year" => 'required|regex:/^[0-9]{4}$/',
            ];
            $this->validate($request, $rules);
            $attendance = Attendance::with(['kpi'])
                ->whereHas('kpi', function ($q) use ($request) {
                    if ($request->range && $request->range != 'all') {
                        $q->where('division_id', $request->range);
                    }
                    if ($request->unit && $request->unit != 'all') {
                        $q->where('unit_id', $request->unit);
                    }
                    if ($request->thana && $request->thana != 'all') {
                        $q->where('thana_id', $request->thana);
                    }
                    if ($request->kpi && $request->kpi != 'all') {
                        $q->where('id', $request->kpi);
                    }
                });
            if ($request->ansar_id) {
                $attendance->where('ansar_id', $request->ansar_id);
            }
            if ($request->month) {
                $attendance->where('month', '=', $request->month);
            }
            if ($request->year) {
                $attendance->where('year', '=', $request->year);
            }
            $attendance->where('is_attendance_taken', '=', 1);
            if (!$request->ansar_id) {
                $type = "count";
                $data = collect($attendance->select(DB::raw("SUM(is_present=1) as total_present"), DB::raw("SUM(is_present=0 AND is_leave=0) as total_absent"), DB::raw("SUM(is_leave=1) as total_leave"), 'day')
                    ->groupBy('day')
                    ->get());
            } else {
                $type = "view";
                $data = $attendance->get();
                $ansar_id = $request->ansar_id;
            }
            $first_date = Carbon::parse("01-{$request->month}-{$request->year}");
            return view('SD::attendance.data', compact('first_date', 'data', 'type', 'ansar_id'));

        }
        return view('SD::attendance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                "range" => 'required',
                "unit" => 'required',
                "thana" => 'required',
                "kpi" => 'required',
                "month" => 'required',
                "year" => 'required',
            ];
            $this->validate($request, $rules);
            $d = $request->day;
            $m = $request->month;
            $y = $request->year;
            $ansar_id = $request->ansar_id;
            $attendance = KpiGeneralModel::with(['attendance' => function ($q) use ($d, $m, $y,$ansar_id) {
                if($d&&$d>0)$q->where('day', $d);
                $q->where('month', $m);
                $q->where('year', $y);
                $q->where('is_attendance_taken', 0);
                if((!$d||$d<0)&&!$ansar_id){
                    $q->select('ansar_id','id','kpi_id',DB::raw("group_concat(CONCAT(id,':',concat(year,'-',month,'-',day))) as id_date"));
                    $q->groupBy('ansar_id');
                }

            }]);
            if ($request->range && $request->range != 'all') {
                $attendance->where('division_id', $request->range);
            }
            if ($request->unit && $request->unit != 'all') {
                $attendance->where('unit_id', $request->unit);
            }
            if ($request->thana && $request->thana != 'all') {
                $attendance->where('thana_id', $request->thana);
            }
            if ($request->kpi && $request->kpi != 'all') {
                $attendance->where('id', $request->kpi);
            }
            if ($request->ansar_id) {
                $attendance->where('ansar_id', $request->ansar_id);
            }
            DB::enableQueryLog();
            $data = $attendance->first();
//            return DB::getQueryLog();
//    return $data;
            $date = $request->only(['day','month','year','ansar_id']);
//            return compact('date', 'data');
            return view('SD::attendance.create_data', compact('date', 'data'));

        }
        return view('SD::attendance.create');
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
//        return $request->all();
        $attendance_datas = $request->get("attendance_data");
//        return $attendance_datas;
        DB::connection('sd')->beginTransaction();
        try {
            foreach ($attendance_datas as $attendance_data) {
//                dump($attendance_data);
                $id = $attendance_data['id'];
                unset($attendance_data['id']);
                $attendance = Attendance::findOrFail($id);
                $attendance->update($attendance_data);
            }
            DB::connection('sd')->commit();
        } catch (\Exception $e) {
            DB::connection('sd')->rollback();
            return $e->getTraceAsString();
        }
        return "success";
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
}
