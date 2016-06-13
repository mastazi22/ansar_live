<?php

namespace App\modules\SD\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\SD\Helper\Facades\DemandConstantFacdes;
use App\modules\SD\Models\DemandConstant;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SDController extends Controller
{
    public function index()
    {
        return view('SD::index');
    }

    public function demandSheet()
    {
        $user = auth()->user();
        if($user->type==22){
            return view('SD::demand_sheet',['kpis'=>KpiGeneralModel::where('unit_id',$user->district_id)->select('id','kpi_name')->get()]);
        }
        else{
            return view('SD::demand_sheet',['units'=>District::all(['id','unit_name_bng'])]);
        }
    }
    public function generateDemandSheet(Request $request){
        $rules = [
            'kpi'=>'required',
            'form_date'=>'required|date_format:d-M-Y',
            'to_date'=>'required|date_format:d-M-Y|after:form_date',
            'other_date'=>'required|date_format:d-M-Y|after:form_date',
        ];
        $messages = [
          'required'=>'This field is required',
          'date_format'=>'Invalid date format',
        ];
        $validator = Validator::make($request->all(),$rules,$messages);
        if($validator->fails()){
            return Response::json(['status'=>false,'messages'=>$validator->messages()]);
        }
        $total_days = Carbon::parse($request->get('form_date'))->diffInDays(Carbon::parse($request->get('to_date')));
        $total_ansars = DB::connection('hrm')->table('tbl_kpi_info')->join('tbl_embodiment','tbl_embodiment.kpi_id','=','tbl_kpi_info.id')->join('tbl_ansar_parsonal_info','tbl_embodiment.ansar_id','=','tbl_ansar_parsonal_info.ansar_id')->where('tbl_kpi_info.id',$request->get('kpi'))->groupBy('tbl_ansar_parsonal_info.designation_id')->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as count'),'tbl_ansar_parsonal_info.designation_id')->get();
        $with_weapon = KpiDetailsModel::where('kpi_id',$request->get('kpi'))->pluck('with_weapon');
        $address = KpiGeneralModel::find($request->get('kpi'))->address;
        $total_pc = 0;
        $total_apc = 0;
        $total_ansar = 0;
        foreach($total_ansars as $ansar){
            if($ansar->designation_id==1) $total_ansar = $ansar->count;
            else if($ansar->designation_id==2) $total_apc = $ansar->count;
            else if($ansar->designation_id==3) $total_pc = $ansar->count;
        }
        $to = Carbon::parse($request->get('to_date'))->format('d-m-Y');
        $form = Carbon::parse($request->get('form_date'))->format('d-m-Y');
        $payment_date = Carbon::parse($request->get('other_date'))->format('d-m-Y');
        $st1 = ($total_pc+$total_apc)*$total_days*DemandConstantFacdes::getValue('DPA')->cons_value;
        $st2 = $total_ansar*$total_days*DemandConstantFacdes::getValue('DA')->cons_value;
        $st3 = $st1+$st2;
        $st4 = $with_weapon?($st3*20)/100:($st3*15)/100;
        $st5 = ($total_pc+$total_apc+$total_ansar)*$total_days*DemandConstantFacdes::getValue('R')->cons_value;
        $st6 = ($total_pc+$total_apc+$total_ansar)*$total_days*DemandConstantFacdes::getValue('CB')->cons_value;
        $st7 = ($total_pc+$total_apc+$total_ansar)*$total_days*DemandConstantFacdes::getValue('CV')->cons_value;
        $st8 = ($total_pc+$total_apc+$total_ansar)*$total_days*DemandConstantFacdes::getValue('DV')->cons_value;
        $st9 = DemandConstantFacdes::getValue('MV')->cons_value;
        $path = public_path('test.pdf');
        SnappyPdf::loadView('SD::test',['address'=>$address,'total_pc'=>$total_pc,'total_apc'=>$total_apc,'total_ansar'=>$total_ansar,'to'=>$to,'form'=>$form,'p_date'=>$payment_date,'total_day'=>$total_days,'st1'=>$st1,'st2'=>$st2,'st3'=>$st3,'st4'=>$st4,'st5'=>$st5,'st6'=>$st6,'st7'=>$st7,'st8'=>$st8,'st9'=>$st9])->setOption('margin-left',0)->setOption('margin-right',0)->save($path);
    }
    public function attendanceSheet()
    {
        return "This is attendance sheet";
    }

    public function demandConstant()
    {
        return view("SD::demand_constant")->with(['constants' => DemandConstant::all()]);
    }

    public function salarySheet()
    {
        return "This is salary sheet";
    }

    public function updateConstant(Request $request)
    {
        $rules = [];
        $messages = [
            'required' => 'This field can`t be empty',
            'numeric' => 'This field must be numeric',
            'min' => 'Value must be greater then 0'
        ];
        foreach ($request->except(['_token']) as $key => $value) {
            $rules[$key] = 'required|numeric|min:1';
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('SD/demandconstant')->withErrors($validator)->withInput($request->except(['_token']));
        }
        $demandConstant = new DemandConstant();
        $demandConstant->where('cons_name', 'ration_fee')->update(['cons_value' => $request->get('ration_fee')]);
        $demandConstant->where('cons_name', 'barber_and_cleaner_fee')->update(['cons_value' => $request->get('barber_and_cleaner_fee')]);
        $demandConstant->where('cons_name', 'transportation')->update(['cons_value' => $request->get('transportation')]);
        $demandConstant->where('cons_name', 'medical_fee')->update(['cons_value' => $request->get('medical_fee')]);
        $demandConstant->where('cons_name', 'margha_fee')->update(['cons_value' => $request->get('margha_fee')]);
        $demandConstant->where('cons_name', 'per_day_salary_ansar')->update(['cons_value' => $request->get('per_day_salary_ansar')]);
        $demandConstant->where('cons_name', 'per_day_salary_pc_and_apc')->update(['cons_value' => $request->get('per_day_salary_pc_and_apc')]);
        // return ['statys'=>$demandConstant->save()];
        return Redirect::to('SD/demandconstant')->with('constant_update_success', 'Demand constant update successfully');


    }

    function test()
    {
//        return view('SD::test');
        return SnappyPdf::loadView('SD::test')->setPaper('a4')->setOption('margin-right',0)->setOption('margin-left',0)->stream();
    }
}
