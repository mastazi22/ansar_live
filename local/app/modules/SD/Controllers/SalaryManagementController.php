<?php

namespace App\modules\SD\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\SD\Helper\Facades\DemandConstantFacdes;
use App\modules\SD\Models\SalaryHistory;
use App\modules\SD\Models\SalarySheetHistory;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SalaryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $history = SalarySheetHistory::with('kpi');
            try {
                if ($request->month_year && Carbon::createFromFormat("F, Y", $request->month_year)) {
                    $history->where('generated_for_month', $request->month_year);
                }
            } catch (\Exception $e) {

            }
            $history->whereHas('kpi', function ($q) use ($request) {
                if ($request->range && $request->range != 'all') $q->where('division_id', $request->range);
                if ($request->unit && $request->unit != 'all') $q->where('unit_id', $request->unit);
                if ($request->thana && $request->thana != 'all') $q->where('thana_id', $request->thana);
                if ($request->kpi && $request->kpi != 'all') $q->where('id', $request->kpi);
            });
            $history = $history->paginate($request->limit?$request->limit:30);
            return view('SD::salary_sheet.view_data',compact('history'));
        }
        return view("SD::salary_sheet.index");
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
//            return $request->all();
            $rules = [
                "range" => 'required',
                "unit" => 'required',
                "thana" => 'required',
                "kpi" => 'required',
                "sheetType" => ['required','regex:/^(salary)|(bonus)$/'],
                "month_year" => 'required_if:sheetType,salary|date_format:"F, Y"|unique:sd.tbl_salary_sheet_generate_history,generated_for_month,NULL,id,generated_type,'.$request->sheetType.',kpi_id,'.$request->kpi,
                "bonusType" => 'required_if:sheetType,bonus'
            ];
            $this->validate($request, $rules,[
                'month_year.unique'=>"Salary sheet has been generated for this month for this kpi",
                'sheetType.required'=>"Please select a sheet type:salary or bonus",
                'sheetType.regex'=>"Please select a valid sheet type:salary or bonus",
            ]);
            $division_id = $request->range;
            $unit_id = $request->unit;
            $thana_id = $request->thana;
            $id = $request->kpi;
            $kpi = KpiGeneralModel::where(compact('division_id', 'unit_id', 'thana_id', 'id'));
            if ($kpi->exists()) {
                $kpi = $kpi->first();
                if ($request->sheetType == 'salary') {
                    $date = Carbon::createFromFormat('F, Y', $request->month_year);
                    $month = $date->month;
                    $year = $date->year;
                    $is_attendance_taken = 1;
                    $attendance = $kpi->attendance()->where(compact('month', 'year', 'is_attendance_taken'))
                        ->select(DB::raw("SUM(is_present=1 AND is_leave=0) as total_present"),
                            DB::raw("SUM(is_present=0 AND is_leave=0) as total_absent"),
                            DB::raw("SUM(is_present=0 AND is_leave=1) as total_leave"),
                            'ansar_id', 'month', 'year'
                        )->groupBy('ansar_id')->get();
                    $datas = [];
                    $all_daily_fee = 0;
                    foreach ($attendance as $a) {
                        $ansar = $a->ansar;
                        $total_daily_fee = floatval($ansar->designation_id == 1 ? DemandConstantFacdes::getValue("DA")->cons_value : DemandConstantFacdes::getValue("DPA")->cons_value)
                            * (intval($a->total_present) + intval($a->total_leave));
                        $total_ration_fee = floatval(DemandConstantFacdes::getValue("R")->cons_value) * (intval($a->total_present) + intval($a->total_leave));
                        $total_barber_fee = floatval(DemandConstantFacdes::getValue("CB")->cons_value) * (intval($a->total_present) + intval($a->total_leave));
                        $total_transportation_fee = floatval(DemandConstantFacdes::getValue("CV")->cons_value) * (intval($a->total_present) + intval($a->total_leave));
                        $total_medical_fee = floatval(DemandConstantFacdes::getValue("DV")->cons_value) * (intval($a->total_present) + intval($a->total_leave));
                        $welfare_fee = floatval(DemandConstantFacdes::getValue("WF")->cons_value);
                        $share_amount = floatval(DemandConstantFacdes::getValue("SA")->cons_value);
                        $all_daily_fee+=$total_daily_fee;
                        array_push($datas, [
                            'total_amount' => $total_daily_fee + $total_barber_fee + $total_ration_fee + $total_transportation_fee + $total_medical_fee,
                            'welfare_fee' => $welfare_fee,
                            'share_amount' => $share_amount,
                            'ansar_id' => $ansar->ansar_id,
                            'ansar_name' => $ansar->ansar_name_eng,
                            'ansar_rank' => $ansar->designation->code,
                            'total_present' => $a->total_present,
                            'total_leave' => $a->total_leave,
                            'total_absent' => $a->total_absent,
                            'account_no' => $ansar->account ? $ansar->account->account_no : 'n\a',
                            'bank_type' => $ansar->account ? ($ansar->account->prefer_choice=="mobile"?$ansar->mobile_bank_type:"DBBL") : 'n\a',
                        ]);
//                        return $datas;
                    }
//                    return $datas;
                    $for_month = $request->month_year;
                    $generated_type = $request->sheetType;
                    $kpi_name = $kpi->kpi_name;
                    $withWeapon = $kpi->details->with_weapon;
                    $extra = $withWeapon?(floatval($all_daily_fee*20)/100):(floatval($all_daily_fee*15)/100);
                    $extra = sprintf("%.2f",$extra);
                    $kpi_id = $kpi->id;
                    return view("SD::salary_sheet.data", compact('datas', 'for_month', 'kpi_name', 'kpi_id', 'generated_type','withWeapon','extra'));

                } else if ($request->sheetType == 'bonus') {
                    $date = Carbon::createFromFormat('F, Y', $request->month_year);
                    $month = $date->month;
                    $year = $date->year;
                    $is_attendance_taken = 1;
                    $attendance = $kpi->attendance()->where(compact('month', 'year', 'is_attendance_taken'))
                        ->select(DB::raw("SUM(is_present=1 AND is_leave=0) as total_present"),
                            DB::raw("SUM(is_present=0 AND is_leave=0) as total_absent"),
                            DB::raw("SUM(is_present=0 AND is_leave=1) as total_leave"),
                            'ansar_id', 'month', 'year'
                        )->groupBy('ansar_id')->get();
                    $datas = [];
                    foreach ($attendance as $a) {
                        $ansar = $a->ansar;
                        $total_amount = floatval($ansar->designation_id == 1 ? DemandConstantFacdes::getValue("EBA")->cons_value : DemandConstantFacdes::getValue("EBPA")->cons_value);

                        array_push($datas, [
                            'total_amount' => $total_amount,
                            'net_amount' => floor(($total_amount * (intval($a->total_present) + intval($a->total_leave))) / $date->daysInMonth),
                            'ansar_id' => $ansar->ansar_id,
                            'ansar_name' => $ansar->ansar_name_eng,
                            'ansar_rank' => $ansar->designation->code,
                            'total_present' => $a->total_present,
                            'total_leave' => $a->total_leave,
                            'total_absent' => $a->total_absent,
                            'account_no' => $ansar->account ? ($ansar->account->prefer_choice == "mobile" ? $ansar->mobile_bank_account_no : $ansar->account_no) : 'n\a',
                            'bank_type' => $ansar->account ? ($ansar->account->prefer_choice == "mobile" ? $ansar->mobile_bank_type : "DBBL") : 'n\a',
                            'bonus_for' => $request->bonusType
                        ]);
//                        return $datas;
                    }
//                    return $datas;
                    $for_month = $request->month_year;
                    $generated_type = $request->sheetType;
                    $kpi_name = $kpi->kpi_name;
                    $kpi_id = $kpi->id;
                    return view("SD::salary_sheet.data", compact('datas', 'for_month', 'kpi_name', 'kpi_id', 'generated_type'));

                }

            } else {
                return response()->json(['message' => "Kpi detail does not found"], 400);
            }
        }
        return view("SD::salary_sheet.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request->all();
        // return $request->attendance_data;
//        return view('SD::salary_sheet.export',['datas'=>$request->attendance_data]);
        $rules = [
            'kpi_id' => "required",
            'generated_for_month' => "required",
            'generated_type' => "required",
            'attendance_data' => "required",
            'summery' => "required",

        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return redirect()->route('SD.salary_management.create')->with("error_message", "Validation error");
        }
        DB::connection('sd')->beginTransaction();
        try {
            $data = [
                'kpi_id' => $request->kpi_id,
                'generated_for_month' => $request->generated_for_month,
                'generated_type' => $request->generated_type,
                'generated_date' => Carbon::now()->format('Y-m-d'),
                'action_user_id' => auth()->user()->id,
                'data' => gzencode(serialize($request->attendance_data),9),
                'summery' => gzencode(serialize($request->summery),9),

            ];

            $history = SalarySheetHistory::create($data);

            $salary_history = [];
            foreach ($request->attendance_data as $ad) {
                array_push($salary_history, [
                    'ansar_id' => $ad["ansar_id"],
                    'kpi_id' => $request->kpi_id,
                    'salary_sheet_id' => $history->id,
                    'amount' => $ad["net_amount"],
                    'status' => "pending",
                    'action_user_id' => auth()->user()->id,
                ]);
            }
            SalaryHistory::insert($salary_history);
            DB::connection('sd')->commit();
//            return $history->summery;

            /*Excel::create('salary_sheet', function ($excel) use ($request) {

                $excel->sheet('sheet1', function ($sheet) use ($request) {
                    $sheet->setAutoSize(false);
                    $sheet->setWidth('A', 5);
                    $sheet->loadView('SD::salary_sheet.export', ['datas' => $request->attendance_data, 'type' => $request->generated_type]);
                });
            })->download('xls');*/
        } catch (\Exception $e) {
            DB::connection('sd')->rollback();
            return redirect()->route('SD.salary_management.create')->with("error_message", $e->getMessage());
        }
        return redirect()->route('SD.salary_management.index')->with("success_message", "Salary sheet created successfully");
    }
    public function generate_payroll(Request $request)
    {
//        return $request->all();
        // return $request->attendance_data;
//        return view('SD::salary_sheet.export',['datas'=>$request->attendance_data]);
        $rules = [
            'kpi_id' => "required",
            'generated_for_month' => "required",
            'generated_type' => "required",
            'attendance_data' => "required",
            'summery' => "required",

        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return redirect()->route('SD.salary_management.create')->with("error_message", "Validation error");
        }
        try {
            $generated_date = Carbon::now()->format('Y-m-d');
            $generated_month = $request->generated_for_month;
            $kpi = KpiGeneralModel::with('details')->find($request->kpi_id);
            $date = Carbon::createFromFormat('F, Y', $request->generated_for_month);
            $month = $date->month;
            $year = $date->year;
            $is_attendance_taken = 1;
            $attendance = $kpi->attendance()->where(compact('month', 'year', 'is_attendance_taken'))
                ->select(DB::raw("SUM(is_present=1 AND is_leave=0) as total_present"),
                    DB::raw("SUM(is_present=0 AND is_leave=0) as total_absent"),
                    DB::raw("SUM(is_present=0 AND is_leave=1) as total_leave"),
                    'ansar_id', 'month', 'year',DB::raw("min(day) as min_day"),DB::raw("max(day) as max_day")
                )->groupBy('ansar_id')->get();
            $datas = [];
            $all_daily_fee = 0;
            foreach ($attendance as $a) {
                $ansar = $a->ansar;
                $total_daily_fee = floatval($ansar->designation_id == 1 ? DemandConstantFacdes::getValue("DA")->cons_value : DemandConstantFacdes::getValue("DPA")->cons_value)
                    * (intval($a->total_present) + intval($a->total_leave));
                $total_ration_fee = floatval(DemandConstantFacdes::getValue("R")->cons_value) * (intval($a->total_present) + intval($a->total_leave));
                $total_barber_fee = floatval(DemandConstantFacdes::getValue("CB")->cons_value) * (intval($a->total_present) + intval($a->total_leave));
                $total_transportation_fee = floatval(DemandConstantFacdes::getValue("CV")->cons_value) * (intval($a->total_present) + intval($a->total_leave));
                $total_medical_fee = floatval(DemandConstantFacdes::getValue("DV")->cons_value) * (intval($a->total_present) + intval($a->total_leave));
                $welfare_fee = floatval(DemandConstantFacdes::getValue("WF")->cons_value);
                $share_amount = floatval(DemandConstantFacdes::getValue("SA")->cons_value);
                $all_daily_fee+=$total_daily_fee;
                array_push($datas, [
                    'ansar_id'=>$ansar->ansar_id,
                    'ansar_name'=>$ansar->ansar_name_bng,
                    'father_name'=>$ansar->father_name_bng,
                    'rank'=>$ansar->designation->name_bng,
                    'total_daily_fee'=>$total_daily_fee,
                    'min_date'=>Carbon::createFromFormat('F, Y', $request->generated_for_month)->day($a->min_day)->format('d/m/Y'),
                    'max_date'=>Carbon::createFromFormat('F, Y', $request->generated_for_month)->day($a->max_day)->format('d/m/Y'),
                    'total_day'=>$a->total_present+$a->total_leave,
                    'total_ration_fee'=>$total_ration_fee,
                    'total_barber_fee'=>$total_barber_fee,
                    'total_transportation_fee'=>$total_transportation_fee,
                    'total_medical_fee'=>$total_medical_fee,
                    'reg_fee'=>$welfare_fee-5,
                    'welfare_fee'=>$welfare_fee-4,
                    'share_amount'=>$share_amount,
                    'extra'=>sprintf('%.2f',($kpi->details->with_weapon?(floatval($total_daily_fee*20)/100):(floatval($total_daily_fee*15)/100))),
                    'net_amount'=>$total_daily_fee + $total_barber_fee + $total_ration_fee + $total_transportation_fee + $total_medical_fee-($welfare_fee+$share_amount),
                    'total_amount'=>$total_daily_fee + $total_barber_fee + $total_ration_fee + $total_transportation_fee+$welfare_fee+$share_amount
                ]);
//                        return $datas;
            }
            $pdf = SnappyPdf::loadView("SD::salary_sheet.payroll_view", compact('generated_date','datas','kpi','generated_month'))
                ->setPaper('legal')
//                ->setOption('footer-left',url('/'))
//                ->setOption('footer-right',Carbon::now()->format('d-M-Y H:i:s'))
                ->setOrientation('landscape');
            return $pdf->download();
        } catch (\Exception $e) {
//            return $e;
            return redirect()->route('SD.salary_management.create')->with("error_message", $e->getMessage());
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
    public function  getSalarySheetList(Request $request){
        if($request->ajax()){
            $sh = SalarySheetHistory::querySearch($request)->select('id','summery','generated_for_month');
            return response()->json($sh->get());
        }
        return abort(403);
    }
}
