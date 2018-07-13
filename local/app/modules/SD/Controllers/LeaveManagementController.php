<?php

namespace App\modules\SD\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\SD\Models\ConstantSettings;
use App\modules\SD\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('SD::leave_management.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $has_active_leave = Leave::where('ansar_id', $request->ansar_id)->where('status', 'active')->exists();
            if (!$has_active_leave) {
                $personal_details = PersonalInfo::with(['leave' => function ($q) {
                    $q->withCount('detailsc')->select('id', 'ansar_id');
                }, 'embodiment' => function ($q) {
                    $q->with('kpi')->select('ansar_id', 'kpi_id');
                }, 'designation'])->whereHas('embodiment', function ($qq) {

                })->whereHas('status', function ($qq) {
                    $qq->where('embodied_status', 1);
                    $qq->where('block_list_status', 0);
                    $qq->where('black_list_status', 0);
                })->where('ansar_id', $request->ansar_id)->first();
                $total_leave = intval(ConstantSettings::where('constant_name', 'total_leave_days_in_a_year')->first()->constant_value);

                if ($personal_details) {
                    foreach ($personal_details->leave as $l) {
                        $total_leave -= intval($l->detailscCount);
                    }
                    return response()->json(['status' => true, 'data' => compact('personal_details', 'total_leave')]);
                }
                return response()->json(['status' => false, 'message' => 'This ansar is not embodied']);
            }
            return response()->json(['status' => false, 'message' => 'This ansar is already in leave']);
        }
        return view('SD::leave_management.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            DB::connection("sd")->beginTransaction();
            try {
                $ansar = PersonalInfo::where('ansar_id', $request->ansar_id)
                    ->whereHas('embodiment', function ($qq) use ($request) {
                        $qq->where('kpi_id', $request->kpi_id);
                    })->whereHas('status', function ($qq) {
                        $qq->where('embodied_status', 1);
                        $qq->where('block_list_status', 0);
                        $qq->where('black_list_status', 0);
                    })->where('ansar_id', $request->ansar_id);
                if ($ansar->exists()) {
                    $ansar = $ansar->first();
                    $leave = $ansar->leave()->create([
                        'ansar_id' => $request->ansar_id,
                        'kpi_id' => $request->kpi_id,
                        'status' => 'active',
                    ]);
                    $dates = [];
                    foreach ($request->selectedDates as $date) {
                        $date["month"] = intval($date["month"]) + 1;
                        if ($request->leave_type == "holiday") {
                            $d = Carbon::create($date["year"], $date["month"], $date["day"]);
                            if ($d->isFriday() || $d->isSaturday()) {
                                $date["leave_type"] = "holiday";
                            } else {
                                $date["leave_type"] = "regular";
                            }
                        } else {
                            $date["leave_type"] = "regular";
                        }
                        $leave->details()->create($date);
                    }
                    DB::connection("sd")->commit();
                    return response()->json(['status'=>true,"message"=>"Leave request complete successfully"]);
                }
                throw new \Exception("Invalid ansar details");
            } catch (\Exception $e) {
                DB::connection("sd")->rollback();
                return response()->json(['status'=>false,"message"=>$e->getMessage()]);
            }
        }
        abort(403);
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
