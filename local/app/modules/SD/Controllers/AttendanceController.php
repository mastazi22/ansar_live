<?php

namespace App\modules\SD\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\TransferAnsar;
use App\modules\SD\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
                /*"range" => 'required',
                "unit" => 'required',
                "thana" => 'required',
                "kpi" => 'required',
                "month" => 'required',
                "year" => 'required',*/
                "month" => 'required',
                "range" => 'required_if:ansar_id,' . null,
                "unit" => 'required_if:ansar_id,' . null,
                "thana" => 'required_if:ansar_id,' . null,
                "kpi" => 'required_if:ansar_id,' . null,
                "year" => 'required|regex:/^[0-9]{4}$/',
            ];
            $this->validate($request, $rules);
            $d = $request->day;
            $m = $request->month;
            $y = $request->year;
            $ansar_id = $request->ansar_id;
            $attendance = KpiGeneralModel::with(['attendance' => function ($q) use ($d, $m, $y, $ansar_id) {
                if ($d && $d > 0) $q->where('day', $d);
                if ($ansar_id) $q->where('ansar_id', $ansar_id);
                $q->where('month', $m);
                $q->where('year', $y);
                $q->where('is_attendance_taken', 0);
                if ((!$d || $d < 0) && !$ansar_id) {
                    $q->select('ansar_id', 'id', 'kpi_id', DB::raw("group_concat(concat(year,'-',lpad(month,2,'0'),'-',lpad(day,2,'0'))) as dates"));
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
                $attendance->whereHas('attendance', function ($q) use ($ansar_id) {
                    $q->where('ansar_id', $ansar_id);
                });
            }

            DB::enableQueryLog();
            $data = $attendance->first();
//            return DB::getQueryLog();
//    return $data;
            $date = $request->only(['day', 'month', 'year', 'ansar_id']);
//            return compact('date', 'data');
            return view('SD::attendance.create_data', compact('date', 'data'));

        }
        return view('SD::attendance.create_b');
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
        $type = $request->type;
//        return $attendance_datas;
        switch ($type) {
            case 'day_wise':
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
                    return redirect()->route("SD.attendance.create")->with('error_message', "An error occur while submitting attendance. please try again later or contact with system admin");
                }
                return redirect()->route("SD.attendance.create")->with('success_message', "Attendance taken successfully");
            case 'month_wise':
                DB::connection('sd')->beginTransaction();
                try {
                    $kpi_id = $request->kpi_id;
                    $month = $request->month;
                    Attendance::where(compact('kpi_id', 'month'))->update(['is_attendance_taken' => 1]);
                    foreach ($attendance_datas as $attendance_data) {
                        $ansar_id = $attendance_data['ansar_id'];

                        $present_dates = explode(',', $attendance_data['present_dates']);
                        $leave_dates = explode(',', $attendance_data['leave_dates']);
                        if (count($present_dates) <= 0 && count($leave_dates) <= 0) continue;
                        foreach ($present_dates as $present_date) {
                            $d = Carbon::parse($present_date);
                            $day = $d->day;
                            $month = $d->month;
                            $year = $d->year;
                            $attendance = Attendance::where(compact('ansar_id', 'kpi_id', 'day', 'month', 'year'))->first();
                            $attendance->update(['is_present' => 1, 'is_attendance_taken' => 1, 'is_leave' => 0]);
                        }
                        foreach ($leave_dates as $leave_date) {
                            $d = Carbon::parse($leave_date);
                            $day = $d->day;
                            $month = $d->month;
                            $year = $d->year;
                            $attendance = Attendance::where(compact('ansar_id', 'kpi_id', 'day', 'month', 'year'))->first();
                            $attendance->update(['is_leave' => 1, 'is_attendance_taken' => 1, 'is_present' => 0]);
                        }

                        //$attendance->update($attendance_data);
                    }
                    DB::connection('sd')->commit();
                } catch (\Exception $e) {
                    DB::connection('sd')->rollback();
                    return redirect()->route("SD.attendance.create")->with('error_message', "An error occur while submitting attendance. please try again later or contact with system admin");
                }
                return redirect()->route("SD.attendance.create")->with('success_message', "Attendance taken successfully");
        }
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
        DB::connection("sd")->beginTransaction();
        try {
            $attendance = Attendance::findOrFail($id);
            $data = [];
            if ($request->status == "present" && ((!$attendance->is_present && !$attendance->is_leave) || (!$attendance->is_present && $attendance->is_leave))) {
                $data["is_present"] = 1;
                $data["is_leave"] = 0;
            } else if ($request->status == "absent" && ($attendance->is_present || $attendance->is_leave)) {
                $data["is_present"] = 0;
                $data["is_leave"] = 0;
            } else if ($request->status == "leave" && ((!$attendance->is_present && !$attendance->is_leave) || ($attendance->is_present && !$attendance->is_leave))) {
                $data["is_present"] = 0;
                $data["is_leave"] = 1;
            }
            $attendance->update($data);
            DB::connection("sd")->commit();
        } catch (\Exception $e) {
            DB::connection("sd")->rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
        return response()->json(['status' => true, 'message' => "Attendance updated successfully"]);
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


    public function viewAttendance(Request $request)
    {
//        return "sadasd";
        if ($request->ajax()) {
            $rules = [
                "date" => 'required|regex:/^[0-9]{1,2}$/',
                "month" => 'required',
                "range" => 'required',
                "unit" => 'required',
                "thana" => 'required',
                "kpi" => 'required',
                "year" => 'required|regex:/^[0-9]{4}$/',
            ];
            $valid = Validator::make($request->all(), $rules);
            if ($valid->fails()) {
                return "";
            }
            $kpi = KpiGeneralModel::find($request->kpi);
            $present_list = Attendance::with(['ansar' => function ($q) {
                $q->with(["embodiment" => function ($qq) {
                    $qq->with("kpi")->select("ansar_id", "kpi_id");
                }])->select('ansar_id', 'ansar_name_bng', 'division_id', 'unit_id', 'thana_id');
            }])->where('day', $request->date)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('kpi_id', $request->kpi)
                ->where('is_attendance_taken', 1)
                ->where('is_leave', 0)
                ->where('is_present', 1)
                ->get();
            $absent_list = Attendance::with(['ansar' => function ($q) {
                $q->with(["embodiment" => function ($qq) {
                    $qq->with("kpi")->select("ansar_id", "kpi_id");
                }])->select('ansar_id', 'ansar_name_bng', 'division_id', 'unit_id', 'thana_id');
            }])->where('day', $request->date)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('kpi_id', $request->kpi)
                ->where('is_attendance_taken', 1)
                ->where('is_leave', 0)
                ->where('is_present', 0)
                ->get();
            $leave_list = Attendance::with(['ansar' => function ($q) {
                $q->with(["embodiment" => function ($qq) {
                    $qq->with("kpi")->select("ansar_id", "kpi_id");
                }])->select('ansar_id', 'ansar_name_bng', 'division_id', 'unit_id', 'thana_id');
            }])->where('day', $request->date)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('kpi_id', $request->kpi)
                ->where('is_attendance_taken', 1)
                ->where('is_leave', 1)
                ->where('is_present', 0)
                ->get();
            $title = "Attendance of <br>" . $kpi->kpi_name . "<br> Date: " . Carbon::create($request->year, $request->month, $request->date)->format('d-M-Y');
            return view('SD::attendance.view', compact('title', 'present_list', 'absent_list', 'leave_list'));
        }
        abort(401);

    }

    public function loadDataForPlanB(Request $request)
    {
        if (!$request->ajax()) return abort(403);
        $ansar_id = $request->ansar_id;
        $personalDetails = PersonalInfo::with(['embodiment.kpi', 'designation'])->whereHas('status', function ($q) {
            $q->where('embodied_status', 1);
            $q->where('block_list_status', 0);
            $q->where('black_list_status', 0);
        })->whereHas('embodiment',function($q){
            $q->whereMonth('joining_date','<=',7);
            $q->whereYear('joining_date','<=',2018);
        })->where('ansar_id', $ansar_id)->first();
        $data = [];
        if ($personalDetails) {

            if ($personalDetails->embodiment->joining_date != $personalDetails->embodiment->transfered_date) {

                $row = [];
                $row["kpi_id"] = $personalDetails->embodiment->kpi->id;
                $row["kpi_name"] = $personalDetails->embodiment->kpi->kpi_name;
                $row["dates"] = [];
                $t_date = Carbon::parse($personalDetails->embodiment->transfered_date);
                if ($t_date->month < 7 || $t_date->month > 7) {
                    $t_date = Carbon::create(2018, 7, 1);
                    for ($i = 0; $i < $t_date->daysInMonth; $i++) {
                        $modified_date = $t_date->addDays($i == 0 ? 0 : 1);
                        array_push($row['dates'], [
                            'day' => $modified_date->day,
                            'month' => $modified_date->month - 1,
                            'year' => $modified_date->year,
                        ]);
                    }
                } else {
                    for ($i = 0; $i < $t_date->daysInMonth - $t_date->day; $i++) {
                        $modified_date = $t_date->addDays($i == 0 ? 0 : 1);
                        array_push($row['dates'], [
                            'day' => $modified_date->day,
                            'month' => $modified_date->month - 1,
                            'year' => $modified_date->year,
                        ]);
                    }
                }

                array_push($data, $row);
                $transfer_history = TransferAnsar::where('ansar_id', $ansar_id)
                    ->where('embodiment_id', $personalDetails->embodiment->id)
                    ->whereMonth('present_kpi_join_date','<=', 7)
                    ->whereYear('present_kpi_join_date','<=', 2018)
                    ->whereMonth('transfered_kpi_join_date','=', 7)
                    ->whereYear('transfered_kpi_join_date','=', 2018)
                    ->orderBy('present_kpi_join_date', 'desc')
                    ->get();
//                return $transfer_history;
                foreach ($transfer_history as $th) {
                    $row = [];
                    $row["kpi_id"] = $th->present_kpi_id;
                    $row["kpi_name"] = $th->presentKpi->kpi_name;
                    $row['dates'] = [];
                    $pd = Carbon::parse($th->present_kpi_join_date);
                    $tdd = Carbon::parse($th->transfered_kpi_join_date);
                    for ($i = $pd->month<7?0:$pd->day; $i < $tdd->day; $i++) {
                        array_push($row['dates'], [
                            'day' => $i,
                            'month' => 6,
                            'year' => 2018,
                        ]);
                    }
                    array_push($data, $row);
                }
            } else {
                $row = [];
                $row["kpi_id"] = $personalDetails->embodiment->kpi->id;
                $row["kpi_name"] = $personalDetails->embodiment->kpi->kpi_name;
                $row["dates"] = [];
                $first_date = Carbon::create(2018, 7, 1);
                for ($i = 0; $i < $first_date->daysInMonth; $i++) {
                    $modified_date = $first_date->addDays($i == 0 ? 0 : 1);
                    array_push($row['dates'], [
                        'day' => $modified_date->day,
                        'month' => $modified_date->month - 1,
                        'year' => $modified_date->year,
                    ]);
                }
                array_push($data, $row);
            }
        }
        return response()->json(compact('personalDetails', 'data'));


    }

    public function storePlanB(Request $request)
    {
        $rules = [
            "ansar_id" => "required",
            "selectedDates" => "required",
            "selectedDates.*.kpi_id" => "required|exists:hrm.tbl_kpi_info,id"
        ];
        $this->validate($request, $rules);
        $data = [];
        DB::connection('sd')->beginTransaction();
        try{
            foreach ($request->selectedDates as $date) {

                foreach ($date["present"] as $present){
                    $row = $query = [];
                    $row['ansar_id'] = $query['ansar_id'] = $request->ansar_id;
                    $row['kpi_id'] = $query['kpi_id'] = $date["kpi_id"];
                    $row['day'] = $query['day'] = $present["day"];
                    $row['month'] = $query['month'] = intval($present["month"])+1;
                    $row['year'] = $query['year'] = intval($present["year"]);
                    $row['is_present'] = 1;
                    $row['is_leave'] = 0;
                    $row['is_attendance_taken'] = 1;
                    $att = Attendance::where($query)->first();
                    if(!$att)array_push($data,$row);
                    else if(!$att->is_attendance_taken){
                        $att->update($row);
                    }
                }
                foreach ($date["absent"] as $absent){
                    $row = $query = [];
                    $row['ansar_id'] = $query['ansar_id'] = $request->ansar_id;
                    $row['kpi_id'] = $query['kpi_id'] = $date["kpi_id"];
                    $row['day'] = $query['day'] = $absent["day"];
                    $row['month'] = $query['month'] = intval($absent["month"])+1;
                    $row['year'] = $query['year'] = intval($absent["year"]);
                    $row['is_present'] = 0;
                    $row['is_leave'] = 0;
                    $row['is_attendance_taken'] = 1;
                    $att = Attendance::where($query)->first();
                    if(!$att)array_push($data,$row);
                    else if(!$att->is_attendance_taken){
                        $att->update($row);
                    }
                }
                foreach ($date["leave"] as $leave){
                    $row = $query = [];
                    $row['ansar_id'] = $query['ansar_id'] = $request->ansar_id;
                    $row['kpi_id'] = $query['kpi_id'] = $date["kpi_id"];
                    $row['day'] = $query['day'] = $leave["day"];
                    $row['month'] = $query['month'] = intval($leave["month"])+1;
                    $row['year'] = $query['year'] = intval($leave["year"]);
                    $row['is_present'] = 0;
                    $row['is_leave'] = 1;
                    $row['is_attendance_taken'] = 1;
                    $att = Attendance::where($query)->first();
                    if(!$att)array_push($data,$row);
                    else if(!$att->is_attendance_taken){
                        $att->update($row);
                    }
                }
            }
            Attendance::insert($data);
            DB::connection('sd')->commit();
        }catch(\Exception $e){
            DB::connection('sd')->rollback();
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }
        if(count($data)==0){
            return response()->json(['status'=>false,'message'=>'Attendance already taken']);
        }
        return response()->json(['status'=>true,'message'=>'Attendance taken successfully']);
    }


}
