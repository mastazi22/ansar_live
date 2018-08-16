<?php

namespace App\modules\SD\Controllers;

use App\Helper\EmailHelper;
use App\Http\Controllers\Controller;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\SD\Helper\Facades\DemandConstantFacdes;
use App\modules\SD\Models\DemandConstant;
use App\modules\SD\Models\DemandLog;
use App\modules\SD\Models\UserActionLog;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SDController extends Controller
{
    use EmailHelper;
    public function index()
    {
        return view('SD::index');
    }



    public function attendanceSheet()
    {
        return "This is attendance sheet";
    }



    public function salarySheet()
    {
        return "This is salary sheet";
    }


    function test()
    {

        return $this->sendEmail('SD::mail.salary_disburse_template',[],"arafat@shurjomukhi.com.bd","Request to disburse allowances to the Ansars of 2KPIs at Naogaon");
//        return view('SD::test');
        //return SnappyPdf::loadView('SD::test')->setPaper('a4')->setOption('margin-right',0)->setOption('margin-left',0)->stream();
    }

    function demandHistory()
    {
//        return DemandLog::get();
        $logs = DemandLog::with('kpi')->paginate(50);
        return view('SD::Demand.demand_history', ['logs' => $logs]);
    }

    function viewDemandSheet($id)
    {
        $log = DemandLog::find($id);
        $path = storage_path('DemandSheet/' . $log->kpi_id . '/' . $log->sheet_name);
        return response(file_get_contents($path), 200, ['content-type' => 'application/pdf', 'content-disposition' => 'inline;filename="' . $log->sheet_name . '"']);
    }
}
