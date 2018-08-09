<?php

namespace App\modules\SD\Controllers;

use App\modules\SD\Helper\Facades\DemandConstantFacdes;
use App\modules\SD\Models\BankAccountList;
use App\modules\SD\Models\SalaryDisburse;
use App\modules\SD\Models\SalarySheetHistory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class SalaryDisburseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                "disburseType" => ['required','regex:/^(salary)|(bonus)$/'],
                "month_year" => 'required|date_format:"F, Y"|exists:sd.tbl_salary_sheet_generate_history,generated_for_month,generated_type,'.$request->disburseType.',kpi_id,'.$request->kpi,
            ];
            $this->validate($request, $rules,[
                'month_year.exists'=>"Salary sheet doesn`t generated for this month for this kpi",
                'disburseType.required'=>"Please select a sheet type:salary or bonus",
                'disburseType.regex'=>"Please select a valid sheet type:salary or bonus",
            ]);
            $division_id = $request->range;
            $unit_id = $request->unit;
            $thana_id = $request->thana;
            $kpi_id = $request->kpi;
            $generated_for_month = $request->month_year;
            $sheet = SalarySheetHistory::where(compact('kpi_id','generated_for_month'))
                ->whereHas('kpi',function($q) use($division_id,$unit_id,$thana_id){
                   $q->where(compact('division_id','unit_id','thana_id'));
                })->first();
            if($sheet){
                $salary_histories = $sheet->salaryHistory()->where('status','pending')->get();
                return view('SD::salary_disburse.data',compact('sheet','salary_histories'));
            }
            return "<h4>No salary sheet generated for month {$request->month_year} for this kpi</h4>";
        }
        return view("SD::salary_disburse.create");
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
            "salary_sheet_id" => 'required'
        ];
        $this->validate($request,$rules);
        $id = Crypt::decrypt($request->salary_sheet_id);
        DB::connection('sd')->beginTransaction();
        try{
            $salary_sheet = SalarySheetHistory::findOrFail($id);
            $deposit = $salary_sheet->deposit;
            $append_extra = $request->append_extra;
            if($salary_sheet->disburst_status=='done') throw new \Exception("Salary Already disburse");
            if(!$deposit) throw new \Exception("No deposit found for disbursement");
            /*$salary_histories = $salary_sheet->salaryHistory()->with('ansar.account')->where('status','pending')->whereHas('ansar.account',function ($q){

            })->get();*/
            $salary_histories = $salary_sheet->salaryHistory()->with('ansar.account')->get();
            if(!count($salary_histories)) throw new \Exception("No ansar account info found for disbursement");
            $disburse_amount = $salary_histories->sum('amount')+$salary_sheet->summery["reg_amount"]+$salary_sheet->summery["share_amount"]+$salary_sheet->summery["welfare_fee"]+$salary_sheet->summery["revenue_stamp"]+($append_extra?$salary_sheet->summery["extra"]:0);
            if($disburse_amount>$deposit->paid_amount) throw new \Exception("Not enough deposit available for disbursement");
            $excel_sheet_data = [];
            $salary_sheet->update(['disburst_status'=>'done']);
            foreach ($salary_histories as $history){
                $history->update(['status'=>'paid']);
                array_push($excel_sheet_data,[
                    'ansar_id'=>$history->ansar_id,
                    'rank'=>$history->ansar->designation->name_eng,
                    'ansar_name'=>$history->ansar->ansar_name_eng,
                    'kpi_name'=>$history->kpi->kpi_name,
                    'amount'=>$history->amount,
                    'month'=>$salary_sheet->generated_for_month,
                    'account_no'=>$history->ansar->account?($history->ansar->account->prefer_choice=="general"?$history->ansar->account->acount_no:$history->ansar->account->mobile_bank_account_no):'n\a',
                    'account_type'=>$history->ansar->account?($history->ansar->account->prefer_choice=="general"?"DBBL":$history->ansar->account->mobile_bank_type):'n\a',
                ]);
            }
            $salary_sheet->disburseLog()->create([
                "kpi_id"=>$salary_sheet->kpi_id,
                "total_disburst_amount"=>$disburse_amount,
                "action_user_id"=>auth()->user()->id,
            ]);
            $collection = collect($excel_sheet_data)->groupBy('account_type');
//            return $collection;
            $files = [];
            foreach ($collection as $key=>$value) {
                $f_name = Excel::create($key=='n\a'?"no_bank_info":$key, function ($excel) use ($value) {

                    $excel->sheet('sheet1', function ($sheet) use ($value) {
                        $sheet->setAutoSize(false);
                        $sheet->setWidth('A', 5);
                        $sheet->loadView('SD::salary_disburse.export', ['datas' => $value]);
                    });
                })->save('xls',false,true);
                array_push($files,$f_name);
            }
            $distribution_to_different_account = [];
            if($append_extra) {
                $distribution_to_different_account[0]["account_name"] = "DG`s Account";
                $distribution_to_different_account[0]["account_no"] = BankAccountList::getAccount("DG");
                $distribution_to_different_account[0]["amount"] = sprintf("%.2f",(($salary_sheet->summery["extra"]*DemandConstantFacdes::getValue('DGEP')->cons_value)/100));
                $distribution_to_different_account[0]["month"] = $salary_sheet->generated_for_month;
                $distribution_to_different_account[0]["branch_name"] = "";

                $distribution_to_different_account[1]["account_name"] = "RC`s Account";
                $distribution_to_different_account[1]["account_no"] = $salary_sheet->kpi->division->rc->userProfile->bank_account_no;
                $distribution_to_different_account[1]["branch_name"] = $salary_sheet->kpi->division->rc->userProfile->branch_name;
                $distribution_to_different_account[1]["amount"] = sprintf("%.2f",(($salary_sheet->summery["extra"]*DemandConstantFacdes::getValue('RCEP')->cons_value)/100));
                $distribution_to_different_account[1]["month"] = $salary_sheet->generated_for_month;

                $distribution_to_different_account[2]["account_name"] = "DC`s Account";
                $distribution_to_different_account[2]["account_no"] = $salary_sheet->kpi->unit->dc->userProfile->bank_account_no;
                $distribution_to_different_account[2]["branch_name"] = $salary_sheet->kpi->unit->dc->userProfile->branch_name;
                $distribution_to_different_account[2]["amount"] = sprintf("%.2f",(($salary_sheet->summery["extra"]*DemandConstantFacdes::getValue('DCEP')->cons_value)/100)+$salary_sheet->summery["revenue_stamp"]);
                $distribution_to_different_account[2]["month"] = $salary_sheet->generated_for_month;

                $distribution_to_different_account[3]["account_name"] = "WELFARE Account";
                $distribution_to_different_account[3]["account_no"] = BankAccountList::getAccount("WELFARE");
                $distribution_to_different_account[3]["amount"] = $salary_sheet->summery["welfare_fee"];
                $distribution_to_different_account[3]["branch_name"] = '';
                $distribution_to_different_account[3]["month"] = $salary_sheet->generated_for_month;

                $distribution_to_different_account[4]["account_name"] = "REGIMENTAL Account";
                $distribution_to_different_account[4]["account_no"] = BankAccountList::getAccount("REGIMENTAL");
                $distribution_to_different_account[4]["amount"] = $salary_sheet->summery["reg_amount"];
                $distribution_to_different_account[4]["month"] = $salary_sheet->generated_for_month;
                $distribution_to_different_account[4]["branch_name"] = '';

                $distribution_to_different_account[5]["account_name"] = "SHARE Account";
                $distribution_to_different_account[5]["account_no"] = BankAccountList::getAccount("SHARE");
                $distribution_to_different_account[5]["amount"] = $salary_sheet->summery["share_amount"];
                $distribution_to_different_account[5]["month"] = $salary_sheet->generated_for_month;
                $distribution_to_different_account[5]["branch_name"] = '';
            }
            else{
                $distribution_to_different_account[1]["account_name"] = "WELFARE Account";
                $distribution_to_different_account[1]["account_no"] = BankAccountList::getAccount("WELFARE");
                $distribution_to_different_account[1]["amount"] = $salary_sheet->summery["welfare_fee"];
                $distribution_to_different_account[1]["month"] = $salary_sheet->generated_for_month;
                $distribution_to_different_account[1]["branch_name"] = '';

                $distribution_to_different_account[2]["account_name"] = "REGIMENTAL Account";
                $distribution_to_different_account[2]["account_no"] = BankAccountList::getAccount("REGIMENTAL");
                $distribution_to_different_account[2]["amount"] = $salary_sheet->summery["reg_amount"];
                $distribution_to_different_account[2]["month"] = $salary_sheet->generated_for_month;
                $distribution_to_different_account[2]["branch_name"] = '';

                $distribution_to_different_account[3]["account_name"] = "SHARE Account";
                $distribution_to_different_account[3]["account_no"] = BankAccountList::getAccount("SHARE");
                $distribution_to_different_account[3]["amount"] = $salary_sheet->summery["share_amount"];
                $distribution_to_different_account[3]["month"] = $salary_sheet->generated_for_month;
                $distribution_to_different_account[3]["branch_name"] = '';

                $distribution_to_different_account[0]["account_name"] = "DC`s Account";
                $distribution_to_different_account[0]["account_no"] = $salary_sheet->kpi->unit->dc->userProfile->bank_account_no;
                $distribution_to_different_account[0]["amount"] = $salary_sheet->summery["revenue_stamp"];
                $distribution_to_different_account[0]["month"] = $salary_sheet->generated_for_month;
                $distribution_to_different_account[0]["branch_name"] = $salary_sheet->kpi->unit->dc->branch_name;
            }

            $f_name = Excel::create("distribution_to_different_account", function ($excel) use ($distribution_to_different_account) {

                $excel->sheet('sheet1', function ($sheet) use ($distribution_to_different_account) {
                    $sheet->setAutoSize(false);
                    $sheet->setWidth('A', 5);
                    $sheet->loadView('SD::salary_disburse.export_other', ['datas' => $distribution_to_different_account]);
                });
            })->save('xls',false,true);
            array_push($files,$f_name);
            $zip_archive_name = "salary_sheet.zip";
            $zip = new \ZipArchive();
            if($zip->open(public_path($zip_archive_name),\ZipArchive::CREATE)===true){
                foreach ($files as $file){
                    $zip->addFile($file["full"],$file["file"]);
                }
                $zip->close();
            } else{
                throw new \Exception("Can`t create file");
            }
            foreach ($files as $file){
                unlink($file["full"]);
            }
            DB::connection('sd')->commit();
            return response()->download(public_path($zip_archive_name))->deleteFileAfterSend(true);

        }catch(\Exception $e){
            DB::connection('sd')->rollback();
//            return $e;
            return redirect()->route('SD.salary_disburse.create')->with('error_message',$e->getMessage());
        }

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
