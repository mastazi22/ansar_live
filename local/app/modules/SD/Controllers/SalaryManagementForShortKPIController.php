<?php

namespace App\modules\SD\Controllers;

use App\modules\AVURP\Models\KpiInfo;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\SD\Helper\DemandConstant;
use App\modules\SD\Helper\Facades\DemandConstantFacdes;
use App\modules\SD\Models\SalaryHistory;
use App\modules\SD\Models\SalaryHistoryShort;
use App\modules\SD\Models\SalarySheetHistoryShort;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SalaryManagementForShortKPIController extends Controller
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
            $history = SalarySheetHistoryShort::with('kpi');
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
            return view('SD::salary_sheet_short.view_data',compact('history'));
        }
        return view("SD::salary_sheet_short.index");
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
                "shortKpi" => 'required',
                "month_year" => 'required_if:sheetType,salary|date_format:"F, Y"'
            ];
            $this->validate($request, $rules);
            $division_id = $request->range;
            $unit_id = $request->unit;
            $thana_id = $request->thana;
            $id = $request->shortKpi;
            $kpi = KpiInfo::with('embodiment')->where(compact('division_id', 'unit_id', 'thana_id', 'id'));
            if ($kpi->exists()) {
                $kpi = $kpi->first();
                $date = Carbon::createFromFormat('F, Y', $request->month_year);
                $month = $date->month;
                $year = $date->year;
                $is_attendance_taken = 1;
                $attendance = $kpi->embodiment;
                $datas = [];
                foreach ($attendance as $a) {
                    $ansar = $a->vdp;
                    $total_daily_fee = floatval($ansar->designation == "পিসি"||$ansar->designation == "এপিসি" ? DemandConstantFacdes::getValue("DPAS")->cons_value : DemandConstantFacdes::getValue("DVAS")->cons_value)
                        * (intval($a->duration));
                    $other_fee = $request->other_amount?floatval(DemandConstantFacdes::getValue("OAS")->cons_value):0;
                    $deduct_fee = $request->deduct_amount?floatval(DemandConstantFacdes::getValue("DAS")->cons_value):0;
                    array_push($datas, [
                        'ansar_id' => $ansar->geo_id,
                        'ansar_name' => $ansar->ansar_name_eng?$ansar->ansar_name_eng:$ansar->ansar_name_bng,
                        'ansar_rank' => $ansar->designation,
                        'total_duration' => $a->duration,
                        'total_daily_fee'=>$total_daily_fee,
                        'other_fee'=>$other_fee,
                        'deduct_fee'=>$deduct_fee,
                        'account_no' => $ansar->account ? ($ansar->account->prefer_choice=="mobile"?$ansar->account->mobile_bank_account_no:$ansar->account->account_no) : 'n\a',
                        'bank_type' => $ansar->account ? ($ansar->account->prefer_choice=="mobile"?$ansar->account->mobile_bank_type:"DBBL") : 'n\a',

                    ]);
//                        return $datas;
                }
//                    return $datas;
                $for_month = $request->month_year;
                $kpi_name = $kpi->kpi_name;
                $kpi_id = $kpi->id;
//                return $datas;
                return view("SD::salary_sheet_short.data", compact('datas', 'for_month', 'kpi_name', 'kpi_id'));


            } else {
                return response()->json(['message' => "Kpi detail does not found"], 400);
            }
        }
        return view("SD::salary_sheet_short.create");
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
//        return view('SD::salary_disburse.export',['datas'=>$request->attendance_data]);
        $rules = [
            'kpi_id' => "required",
            'generated_for_month' => "required",
            'salary_data' => "required",

        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return redirect()->route('SD.salary_management_short.create')->with("error_message", "Validation error");
        }
//        return $request->all();
//        $data_collection = collect($request->salary_data)->groupBy("bank_type");
//        dd($data_collection);
        DB::beginTransaction();
        try {
            $data = [
                'kpi_id' => $request->kpi_id,
                'generated_for_month' => $request->generated_for_month,
                'generated_date' => Carbon::now()->format('Y-m-d'),
                'action_user_id' => auth()->user()->id,
                'data' => gzcompress(serialize($request->salary_data)),

            ];

            $history = SalarySheetHistoryShort::create($data);
            $salary_history = [];
            foreach ($request->salary_data as $ad){
                array_push($salary_history,[
                   'ansar_id'=>$ad["ansar_id"],
                   'kpi_id'=>$request->kpi_id,
                   'salary_sheet_id'=>$history->id,
                   'amount'=>$ad["net_amount"],
                   'status'=>"pending",
                   'action_user_id'=>auth()->user()->id,
                ]);
            }
            SalaryHistoryShort::insert($salary_history);
            DB::commit();
            $data_collection = collect($request->salary_data)->groupBy("bank_type");
//            dd($data_collection);
            $files = [];
//            return $data_collection;
            foreach ($data_collection as $key=>$value) {
                $f_name = Excel::create($key=='n\a'?"no_bank_info":$key, function ($excel) use ($value) {

                    $excel->sheet('sheet1', function ($sheet) use ($value) {
                        $sheet->setAutoSize(false);
                        $sheet->setWidth('A', 5);
                        $sheet->loadView('SD::salary_sheet_short.export', ['datas' => $value]);
                    });
                })->save('xls',false,true);
                array_push($files,$f_name);
            }
        } catch (\Exception $e) {
            DB::rollback();
//            return $e;
            return redirect()->route('SD.salary_management_short.create')->with("error_message", $e->getMessage());
        }
        $zip_archive_name = "salary_sheet.zip";
        $zip = new \ZipArchive();
        if($zip->open(public_path($zip_archive_name),\ZipArchive::CREATE)===true){
            foreach ($files as $file){
                $zip->addFile($file["full"],$file["file"]);
            }
            $zip->close();
        }
        return response()->download(public_path($zip_archive_name));
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
