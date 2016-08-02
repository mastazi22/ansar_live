<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Facades\GlobalParameterFacades;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\SendSms;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\ServiceExtensionModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\TransferAnsar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class EmbodimentController extends Controller
{
    public function kpiName(Request $request)
    {
        if (Input::exists('division')) {
            $kpi = KpiGeneralModel::where('division_id', '=', Input::get('division'))->where('status_of_kpi', 1)->where('withdraw_status', 0)->get();
        } else if (Input::exists('unit')) {
            $kpi = KpiGeneralModel::where('unit_id', '=', Input::get('unit'))->where('status_of_kpi', 1)->where('withdraw_status', 0)->get();
        } else if (Input::exists('id')) {
            $id = $request->input('id');
            $kpi = KpiGeneralModel::where('thana_id', '=', $id)->where('status_of_kpi', 1)->where('withdraw_status', 0)->get();
        } else
            $kpi = KpiGeneralModel::where('status_of_kpi', 1)->where('withdraw_status', 0)->get();

        return Response::json($kpi);
    }

    public function newEmbodimentView()
    {
        $user_type = Auth::user()->type;
        $user_unit = Auth::user()->district_id;
        $user_thanas = Thana::where('unit_id', $user_unit)->get();
        $kpi_names = KpiGeneralModel::all();
        return view('HRM::Embodiment.new_embodiment_entry')->with(['user_type' => $user_type, 'user_thanas' => $user_thanas, 'kpi_names' => $kpi_names, 'user_unit' => $user_unit]);
    }

    public function loadAnsarForEmbodiment(Request $request)
    {
        $rules=['ansar_id'=> 'required|numeric|regex:/^[0-9]+$/'];
        $valid=Validator::make($request->all(), $rules);
        if($valid->fails()){
            return response('No Ansar Found', 400);
        }
        $ansar_id = Input::get('ansar_id');
        $ansar_from_sms_offer = DB::table('tbl_sms_offer_info')->where('ansar_id', $ansar_id)->select('tbl_sms_offer_info.ansar_id')->first();
        $ansar_from_sms_receive = DB::table('tbl_sms_receive_info')->where('ansar_id', $ansar_id)->select('tbl_sms_receive_info.ansar_id')->first();

        if (!is_null($ansar_from_sms_offer)) {

            $ansarPersonalDetail = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_designations.id',
                    'tbl_units.unit_name_bng', 'tbl_units.id as unit_id', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_designations.name_bng', 'tbl_ansar_parsonal_info.mobile_no_self')->first();

            $ansarStatusInfo = DB::table('tbl_ansar_status_info')
                ->where('block_list_status', '=', 0)
                ->where('black_list_status', '=', 0)
                ->where('ansar_id', $ansar_id)
                ->select('*')
                ->first();

            $ansarPanelInfo = DB::table('tbl_panel_info_log')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_panel_info_log.ansar_id')
                ->where('tbl_panel_info_log.ansar_id', Input::get('ansar_id'))
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->orderBy('tbl_panel_info_log.id', 'desc')
                ->select('tbl_panel_info_log.panel_date', 'tbl_panel_info_log.old_memorandum_id as memorandum_id')->first();

            $ansar_details = DB::table('tbl_sms_offer_info')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_offer_info.district_id')
                ->where('tbl_sms_offer_info.ansar_id', '=', $ansar_id)
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->select('tbl_sms_offer_info.ansar_id', 'tbl_sms_offer_info.sms_send_datetime as offerDate', 'tbl_units.unit_name_bng as offerUnit')
                ->first();

            return Response::json(['apd' => $ansarPersonalDetail, 'asi' => $ansarStatusInfo, 'api' => $ansarPanelInfo, 'aoi' => $ansar_details]);
        }
        else {
//        if(!is_null($ansar_from_sms_receive)){
            $ansarPersonalDetail = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_designations.id',
                    'tbl_units.unit_name_bng', 'tbl_units.id as unit_id', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_designations.name_bng', 'tbl_ansar_parsonal_info.mobile_no_self')->first();

            $ansarStatusInfo = DB::table('tbl_ansar_status_info')
                ->where('ansar_id', $ansar_id)
                ->where('block_list_status', '=', 0)
                ->where('black_list_status', '=', 0)
                ->select('*')->first();


            $ansarPanelInfo = DB::table('tbl_panel_info_log')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_panel_info_log.ansar_id')
                ->where('tbl_panel_info_log.ansar_id', Input::get('ansar_id'))
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->orderBy('tbl_panel_info_log.id', 'desc')
                ->select('tbl_panel_info_log.panel_date', 'tbl_panel_info_log.old_memorandum_id as memorandum_id')->first();

            $ansar_details = DB::table('tbl_sms_receive_info')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_receive_info.offered_district')
                ->where('tbl_sms_receive_info.ansar_id', '=', $ansar_id)
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->select('tbl_sms_receive_info.ansar_id', 'tbl_sms_receive_info.sms_send_datetime as offerDate', 'tbl_units.unit_name_bng as offered_district')
                ->first();
            return Response::json(['apd' => $ansarPersonalDetail, 'asi' => $ansarStatusInfo, 'api' => $ansarPanelInfo, 'aoi' => $ansar_details]);
        }
//        }
    }

    public function newEmbodimentEntry(Request $request)
    {
        $ansar_id = $request->input('ansar_id');
        $kpi_id = $request->input('kpi_id');

        if(Auth::user()->type==22){
            $rules=[
                'ansar_id'=> 'required|numeric|regex:/^[0-9]+$/|is_eligible:ansar_id,kpi_id',
                'memorandum_id'=>'required|unique:hrm.tbl_memorandum_id',
                'division_name_eng'=>'required|numeric|regex:/^[0-9]+$/',
                'thana_name_eng'=>'required|numeric|regex:/^[0-9]+$/',
                'kpi_id'=>'required|numeric|regex:/^[0-9]+$/',
            ];
            $ansar_rank=PersonalInfo::where('tbl_ansar_parsonal_info.ansar_id',$ansar_id)->select('designation_id')->first();
            if($ansar_rank->designation_id==1){
                $message=[
                    'ansar_id.required' =>'Ansar ID is required',
                    'ansar_id.is_eligible' =>'Ansar Cannot be Embodied',
                    'memorandum_id.required' =>'Memorandum ID is required',
                    'division_name_eng.required' =>'Division  is required',
                    'thana_name_eng.required' =>'Thana is required',
                    'kpi_id.required' =>'KPI is required',
                    'ansar_id.numeric' =>'Ansar ID must be numeric',
                    'ansar_id.regex' =>'Ansar ID must be numeric',
                    'memorandum_id.unique' =>'Memorandum ID has already been taken',
                    'division_name_eng.numeric' =>'Division format is invalid',
                    'division_name_eng.regex' =>'Division format is invalid',
                    'thana_name_eng.numeric' =>'Thana format is invalid',
                    'thana_name_eng.regex' =>'Thana format is invalid',
                    'kpi_id.numeric' =>'KPI format is invalid',
                    'kpi_id.regex' =>'KPI format is invalid',
                ];
            }
        }else{
            $rules=[
                'kpi_id'=>'required|numeric|regex:/^[0-9]+$/',
                'ansar_id'=> 'required|numeric|regex:/^[0-9]+$/|is_eligible:ansar_id,kpi_id',
                'memorandum_id'=>'required|unique:hrm.tbl_memorandum_id',
                'reporting_date'=>['required','regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/'],
                'joining_date'=>['required','regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/'],
                'division_name_eng'=>'required|numeric|regex:/^[0-9]+$/',
                'thana_name_eng'=>'required|numeric|regex:/^[0-9]+$/',

            ];
            $ansar_rank=PersonalInfo::where('tbl_ansar_parsonal_info.ansar_id',$ansar_id)->select('designation_id')->first();
            if($ansar_rank->designation_id==1){
                $message=[
                    'ansar_id.required' =>'Ansar ID is required',
                    'ansar_id.is_eligible' =>'This Ansar Cannot be Embodied. Because the total number of Ansars in this KPI already exceed. First Transfer or Disembodied Ansar from this selected KPI.',
                    'memorandum_id.required' =>'Memorandum ID is required',
                    'reporting_date.required' =>'Reporting Date is required',
                    'joining_date.required' =>'Joining Date is required',
                    'division_name_eng.required' =>'Division  is required',
                    'thana_name_eng.required' =>'Thana is required',
                    'kpi_id.required' =>'KPI is required',
                    'ansar_id.numeric' =>'Ansar ID must be numeric',
                    'ansar_id.regex' =>'Ansar ID must be numeric',
                    'memorandum_id.unique' =>'Memorandum ID has already been taken',
                    'reporting_date.regex' =>'Reporting Date format is invalid',
                    'joining_date.regex' =>'Joining Date format is invalid',
                    'division_name_eng.numeric' =>'Division format is invalid',
                    'division_name_eng.regex' =>'Division format is invalid',
                    'thana_name_eng.numeric' =>'Thana format is invalid',
                    'thana_name_eng.regex' =>'Thana format is invalid',
                    'kpi_id.numeric' =>'KPI format is invalid',
                    'kpi_id.regex' =>'KPI format is invalid',
                ];
            }elseif($ansar_rank->designation_id==2){
                $message=[
                    'ansar_id.required' =>'Ansar ID is required',
                    'ansar_id.is_eligible' =>'APC Cannot be Embodied. Because the total number of APC in this KPI already exceed. First Transfer or Disembodied Ansar from this selected KPI.',
                    'memorandum_id.required' =>'Memorandum ID is required',
                    'reporting_date.required' =>'Reporting Date is required',
                    'joining_date.required' =>'Joining Date is required',
                    'division_name_eng.required' =>'Division  is required',
                    'thana_name_eng.required' =>'Thana is required',
                    'kpi_id.required' =>'KPI is required',
                    'ansar_id.numeric' =>'Ansar ID must be numeric',
                    'ansar_id.regex' =>'Ansar ID must be numeric',
                    'memorandum_id.unique' =>'Memorandum ID has already been taken',
                    'reporting_date.regex' =>'Reporting Date format is invalid',
                    'joining_date.regex' =>'Joining Date format is invalid',
                    'division_name_eng.numeric' =>'Division format is invalid',
                    'division_name_eng.regex' =>'Division format is invalid',
                    'thana_name_eng.numeric' =>'Thana format is invalid',
                    'thana_name_eng.regex' =>'Thana format is invalid',
                    'kpi_id.numeric' =>'KPI format is invalid',
                    'kpi_id.regex' =>'KPI format is invalid',
                ];
            }elseif($ansar_rank->designation_id==3){
                $message=[
                    'ansar_id.required' =>'Ansar ID is required',
                    'ansar_id.is_eligible' =>'PC Cannot be Embodied. Because the total number of PC in this KPI already exceed. First Transfer or Disembodied Ansar from this selected KPI.',
                    'memorandum_id.required' =>'Memorandum ID is required',
                    'reporting_date.required' =>'Reporting Date is required',
                    'joining_date.required' =>'Joining Date is required',
                    'division_name_eng.required' =>'Division  is required',
                    'thana_name_eng.required' =>'Thana is required',
                    'kpi_id.required' =>'KPI is required',
                    'ansar_id.numeric' =>'Ansar ID must be numeric',
                    'ansar_id.regex' =>'Ansar ID must be numeric',
                    'memorandum_id.unique' =>'Memorandum ID has already been taken',
                    'reporting_date.regex' =>'Reporting Date format is invalid',
                    'joining_date.regex' =>'Joining Date format is invalid',
                    'division_name_eng.numeric' =>'Division format is invalid',
                    'division_name_eng.regex' =>'Division format is invalid',
                    'thana_name_eng.numeric' =>'Thana format is invalid',
                    'thana_name_eng.regex' =>'Thana format is invalid',
                    'kpi_id.numeric' =>'KPI format is invalid',
                    'kpi_id.regex' =>'KPI format is invalid',
                ];
            }
        }
        $valid=Validator::make($request->all(), $rules, $message);
        if($valid->fails()){
            return Redirect::back()->withInput(Input::all())->withErrors($valid);
        }
        if(Auth::user()->type==22){
            $modified_joining_date = Carbon::today()->format('Y-m-d');
            $modified_reporting_date = Carbon::today()->format('Y-m-d');
        }else{
            $joining_date = $request->input('joining_date');
            $reporting_date = $request->input('reporting_date');
            $modified_joining_date = Carbon::parse($joining_date)->format('Y-m-d');
            $modified_reporting_date = Carbon::parse($reporting_date)->format('Y-m-d');
        }
        $memorandum_id = $request->input('memorandum_id');
        $global_value = GlobalParameterFacades::getValue("embodiment_period");
        $global_unit = GlobalParameterFacades::getUnit("embodiment_period");


        DB::beginTransaction();
        try {
            $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
            $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

            $embodiment_entry = new EmbodimentModel();
            $embodiment_entry->ansar_id = $ansar_id;
            if (!is_null($sms_offer_info)) {
                $embodiment_entry->received_sms_id = $sms_offer_info->id;
            } else {
                $embodiment_entry->received_sms_id = $sms_receive_info->id;
            }
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $memorandum_id;
            $memorandum_entry->save();
            $embodiment_entry->memorandum_id = $memorandum_id;

            $embodiment_entry->kpi_id = $kpi_id;
            $embodiment_entry->reporting_date = $modified_reporting_date;
            $embodiment_entry->joining_date = $modified_joining_date;
            $embodiment_entry->emboded_status = "Emboded";
            $embodiment_entry->transfered_date = $modified_joining_date;

            if (strcasecmp($global_unit, "Year") == 0) {
                $service_ending_period = $global_value;
                $embodiment_entry->service_ended_date = Carbon::parse($joining_date)->addYear($service_ending_period)->subDay(1);
            } elseif (strcasecmp($global_unit, "Month") == 0) {
                $service_ending_period = $global_value;
                $embodiment_entry->service_ended_date = Carbon::parse($joining_date)->addMonth($service_ending_period)->subDay(1);
            } elseif (strcasecmp($global_unit, "Day") == 0) {
                $service_ending_period = $global_value;
                $embodiment_entry->service_ended_date = Carbon::parse($joining_date)->addDay($service_ending_period)->subDay(1);
            }

            $embodiment_entry->action_user_id = Auth::user()->id;
            $embodiment_entry->save();

            $mobile_no = DB::table('tbl_ansar_parsonal_info')->where('ansar_id', $ansar_id)->select('tbl_ansar_parsonal_info.mobile_no_self')->first();
            if (!is_null($sms_offer_info)) {

                $sms_log_save = new OfferSmsLog();
                $sms_log_save->ansar_id = $ansar_id;
                $sms_log_save->sms_offer_id = $sms_offer_info->id;
                $sms_log_save->mobile_no = $mobile_no->mobile_no_self;

                //$sms_log_save->offer_status=;
                $sms_log_save->reply_type = "No Reply";
                $sms_log_save->offered_district = $sms_offer_info->district_id;
                $sms_log_save->offered_date = $sms_offer_info->sms_send_datetime;
                $sms_log_save->action_date = Carbon::now();
                $sms_log_save->action_user_id = Auth::user()->id;
                $sms_log_save->save();

                $sms_offer_info->delete();

            } elseif (!is_null($sms_receive_info)) {
                $sms_log_save = new OfferSmsLog();
                $sms_log_save->ansar_id = $ansar_id;
                $sms_log_save->sms_offer_id = $sms_receive_info->id;
                $sms_log_save->mobile_no = $mobile_no->mobile_no_self;
                //$sms_log_save->offer_status=;
                $sms_log_save->reply_type = "Yes";
                $sms_log_save->action_date = $sms_receive_info->sms_received_datetime;
                $sms_log_save->offered_district = $sms_receive_info->offered_district;
                $sms_log_save->offered_date = $sms_receive_info->sms_send_datetime;
                $sms_log_save->action_user_id = Auth::user()->id;
                $sms_log_save->save();

                $sms_receive_info->delete();
            }
            AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 1, 'pannel_status' => 0, 'freezing_status' => 0]);

            DB::commit();
            CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'EMBODIED', 'from_state' => 'OFFER', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
            $this->dispatch(new SendSms($ansar_id));

        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('go_to_new_embodiment_page')->with('success_message', 'Ansar is Embodied Successfully!');
    }

    public function transferProcessView()
    {
        return View::make('HRM::Transfer.transfer_ansar');
    }
    function completeTransferProcess()
    {
        $rules = [
           'transfer_date'=>['required','date_format:d-M-Y','regex:/^[0-9]{2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/'] ,
            'kpi_id.0'=>'required|numeric|regex:/^[0-9]+$/',
            'kpi_id.1'=>'required|numeric|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make(Input::all(),$rules);
        if($valid->fails()){
            return response($valid->messages()->toJson(),400,['Content-Type'=>'application/json']);
        }
        $m_id = Input::get('memorandum_id');
        $t_date = Input::get('transfer_date');
        $kpi_id = Input::get('kpi_id');
        $transferred_ansar = Input::get('transferred_ansar');
        $status = array('success' => array('count' => 0, 'data' => array()), 'error' => array('count' => 0, 'data' => array()));
        DB::beginTransaction();
        try {
            $memorandum = new MemorandumModel;
            $memorandum->memorandum_id = $m_id;
            $memorandum->save();
            foreach ($transferred_ansar as $ansar) {
                DB::beginTransaction();
                try {
                    $e_id = EmbodimentModel::where('ansar_id', $ansar['ansar_id'])->first();
                    $e_id->kpi_id = $kpi_id[1];
                    $e_id->transfered_date = Carbon::createFromFormat("d-M-Y",$t_date)->format("Y-m-d");
                    $e_id->save();
                    $transfer = new TransferAnsar;
                    $transfer->ansar_id = $ansar['ansar_id'];
                    $transfer->embodiment_id = $e_id->id;
                    $transfer->transfer_memorandum_id = $m_id;
                    $transfer->present_kpi_id = $kpi_id[0];
                    $transfer->transfered_kpi_id = $kpi_id[1];
                    $transfer->present_kpi_join_date = $ansar['joining_date'];
                    $transfer->transfered_kpi_join_date = Carbon::createFromFormat("d-M-Y",$t_date)->format("Y-m-d");
                    $transfer->action_by = Auth::user()->id;
                    $transfer->save();
                    DB::commit();
                    $status['success']['count']++;
                    array_push($status['success']['data'], $ansar['ansar_id']);
                    CustomQuery::addActionlog(['ansar_id' => $ansar['ansar_id'], 'action_type' => 'TRANSFER', 'from_state' => $kpi_id[0], 'to_state' => $kpi_id[1], 'action_by' => auth()->user()->id]);
                } catch (Exception $e) {
                    DB::rollback();
                    $status['error']['count']++;
                    array_push($status['error']['data'], $ansar['ansar_id']);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status['error']['count'] = count($transferred_ansar);
            //return Response::json(['status'=>false,'message'=>'Can`t transfer ansar. There is an error.Please try again later']);
        }
        return Response::json(['status' => true, 'data' => $status]);
    }
    public function getEmbodiedAnsarOfKpi()
    {
        $kpi_id = Input::get('kpi_id');
        return Response::json(CustomQuery::getEmbodiedAnsar($kpi_id));
    }

    public function getEmbodiedAnsarOfKpiV()
    {
        $u_id = Input::get('thana_id');
        $t_id = Input::get('unit_id');
        return Response::json(CustomQuery::getEmbodiedAnsarV($t_id, $u_id));
    }

    public function downloadBankForm($id)
    {
        $ansar = PersonalInfo::where('ansar_id', $id)->first();
        //return View::make('template.bank_form_view')->with(['ansar' => $ansar]);
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadView('template.bank_form_view', ['ansar' => $ansar]);
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-bottom', '1mm');
        return $pdf->download($id . '.pdf');
    }

    public function generateBankForm()
    {
//        $ansar = PersonalInfo::where('ansar_id', $id)->first();
//        //return View::make('template.bank_form_view')->with(['ansar' => $ansar]);
//        $pdf = App::make('snappy.pdf.wrapper');
//        $pdf->loadView('template.bank_form_view', ['ansar' => $ansar]);
//        $pdf->setOption('margin-top', '1mm');
//        $pdf->setOption('margin-bottom', '1mm');
        $thana = Input::get('thana_id');
        $unit = Input::get('unit_id');
        $file = public_path() . '/bank/' . District::find($unit)->unit_name_eng . '/';
        if (!file_exists($file)) mkdir($file, 0777, true);
        if (file_exists($file . Thana::find($thana)->thana_name_eng . '.xls')) return Response::json(['status' => true]);
//        $status = $pdf->save($file . $id . '.pdf');
        $header = ['Ansar Id', 'Ansar Name', 'Mobile', 'Father Name', 'Mother Name', 'Nationality', 'Birth Date', 'Sex', 'Occupation', 'National Id No', 'permDivision', 'permDistrict', 'permThana', 'permUnion', 'MailOffice', 'MailDivision', 'MailDistrict', 'MailThana', 'SL. NO', 'Nominee Nema', 'Relationship', 'Share', 'SL. NO', 'Nominee Nema', 'Relationship', 'Share'];
        $ansar = CustomQuery::getEmbodiedAnsarV($unit, $thana);
        //return $ansar;
        Excel::create(Thana::find($thana)->thana_name_eng, function ($excel) use ($ansar, $header) {
            $excel->sheet('Ansar Detail', function ($sheet) use ($ansar, $header) {
                $sheet->row(1, $header);
                $c = 2;
                foreach ($ansar as $a) {
                    $ansarn = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_amsar_nominee_info', 'tbl_amsar_nominee_info.annsar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', $a->ansar_id)->take(2)
                        ->select('tbl_amsar_nominee_info.name_of_nominee as nn', 'tbl_amsar_nominee_info.relation_with_nominee as rn')
                        ->get();
                    $p = count($ansarn) >= 2 ? 50 : 100;
                    $r = [$a->ansar_id, $a->name, $a->phone, $a->fatherName, $a->motherName, 'Bangladeshi', $a->birthDate, $a->sex, 'Public Service', $a->id ? $a->id : "NULL", $a->division_name_eng, $a->unit_name_eng, $a->thana_name_eng, $a->union_name_eng, 'DCA`s Office', $a->kdd, $a->kuu, $a->ktt];
                    $i = 1;
                    foreach ($ansarn as $b) {
                        array_push($r, $i);
                        array_push($r, $b->nn);
                        array_push($r, $b->rn);
                        array_push($r, $p);
                        $i++;
                    }
                    echo $c . "INSERT<br>";
                    $sheet->row($c, $r);
                    $c++;

                }


            });
        })->store('xls', $file);
        return Response::json(['status' => true]);
    }

    public function downloadAllBankForm()
    {
        $file = public_path() . '/bank/bank_receipt.zip';
        //return Response::json(['status'=>file_exists($file)]);
        return Response::download($file);
    }

    public function makingZipAllBankForm()
    {
        //return View::make('template.bank_form_view')->with(['ansar' => $ansar]);
        $zip = new \ZipArchive();
        $file = public_path() . "/bank/";
        if (file_exists($file . 'bank_receipt')) unlink($file . 'bank_receipt.zip');
        $status = $zip->open($file . 'bank_receipt.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        //if(file_exists($file))unlink($file);
        $this->getZipFile($zip, $file);
        //$this->removeAllFile($file);
        $zip->close();
        if (!$status) {
            return Response::json(['status' => false]);
        }
        return Response::json(['status' => true]);
    }

    public function getZipFile(&$zip, $path)
    {
        $allFile = scandir($path);
        for ($i = 0; $i < count($allFile); $i++) {
            if (strcasecmp($allFile[$i], '.') == 0 || strcasecmp($allFile[$i], '..') == 0) {

            } else {
                if (is_dir($path . $allFile[$i])) {
                    //echo str_replace(public_path().'/bank/','',$path.$allFile[$i]).'<br>';
                    $this->getZipFile($zip, $path . $allFile[$i] . '/');
                } else {
                    if (strcasecmp(pathinfo($allFile[$i], PATHINFO_EXTENSION), 'zip')) $zip->addFile($path . $allFile[$i], str_replace(public_path() . '/bank/', '', $path . $allFile[$i]));
                }
            }
        }
    }

    public function bankRecipt()
    {
        return View::make('template.bank_from');
    }


    public function removeAllFile($path)
    {
        $allFile = scandir($path);
        for ($i = 0; $i < count($allFile); $i++) {
            if (strcasecmp($allFile[$i], '.') == 0 || strcasecmp($allFile[$i], '..') == 0) {

            } else {
                if (is_dir($path . $allFile[$i])) {
                    //echo str_replace(public_path().'/bank/','',$path.$allFile[$i]).'<br>';
                    $this->removeAllFile($path . $allFile[$i] . '/');
                } else {
                    if (strcasecmp(pathinfo($allFile[$i], PATHINFO_EXTENSION), 'zip')) unlink($path . $allFile[$i]);
                }
            }
        }
        if (strcasecmp($path, public_path() . '/bank/')) rmdir($path);
    }

    public function newDisembodimentView()
    {
        return view('HRM::Embodiment.new_disembodiment_rough');
    }

    public function loadAnsarForDisembodiment()
    {
        $reasons = DB::table('tbl_disembodiment_reason')->select('tbl_disembodiment_reason.id', 'tbl_disembodiment_reason.reason_in_bng')->get();
        $kpi_id = Input::get('kpi_id');
        $thana_id = Input::get('thana_id');
        $status = "Emboded";

//        if ($kpi_id == 0) {
//            $ansar_infos = DB::table('tbl_kpi_info')
//                ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
//                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
//                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
//                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
//                ->where('tbl_kpi_info.thana_id', '=', $thana_id)->where('tbl_embodiment.emboded_status', '=', $status)
//                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
//                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
//                ->distinct()
//                ->select('tbl_kpi_info.kpi_name', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_designations.name_eng')
//                ->get();
//            if(count($ansar_infos)<=0) return Response::json(array('result' => true));
//            return view('embodiment.selected_view_disembodiment')->with(['ansar_infos' => $ansar_infos, 'type' => 0, 'reasons' => $reasons]);
//        } else {
        $ansar_infos = DB::table('tbl_kpi_info')
            ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->where('tbl_kpi_info.thana_id', '=', $thana_id)
            ->where('tbl_embodiment.emboded_status', '=', $status)
            ->where('tbl_embodiment.kpi_id', '=', $kpi_id)
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0)
            ->distinct()
            ->select('tbl_kpi_info.kpi_name', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_units.unit_name_bng', 'tbl_thana.thana_name_bng', 'tbl_designations.name_bng')
            ->get();
        if (count($ansar_infos) <= 0) return Response::json(array('result' => true));
        return view('HRM::Embodiment.selected_view_disembodiment')->with(['ansar_infos' => $ansar_infos, 'type' => 1, 'reasons' => $reasons]);
//        }
    }

    public function disembodimentEntry(Request $request)
    {
        DB::beginTransaction();
        $user = [];
        try {
            if ($request->ajax()) {
                $disembodiment_reason = Input::get('reason');
                $selected_ansars = $request->input('selected-ansar_id');
                $memorandum_id = $request->input('memorandum_id');
                $disembodiment_date = $request->input('disembodiment_date');
                $modified_disembodiment_date = Carbon::parse($disembodiment_date)->format('Y-m-d');
                $disembodiment_comment = $request->input('disembodiment_comment');
                $global_value = GlobalParameterFacades::getValue("rest_period");
                $global_unit = GlobalParameterFacades::getUnit("rest_period");
                //return $disembodiment_reason."<br>".$selected_ansars."<br>".$memorandum_id."<br>".$disembodiment_date."<br>".$disembodiment_comment;

                if (!is_null($disembodiment_reason)) {
                    $memorandum_entry = new MemorandumModel();
                    $memorandum_entry->memorandum_id = $memorandum_id;
                    $memorandum_entry->save();
                    for ($i = 0; $i < count($disembodiment_reason); $i++) {
                        $embodiment_infos = EmbodimentModel::where('ansar_id', $selected_ansars[$i])->first();

                        $rest_entry = new RestInfoModel();
                        $rest_entry->ansar_id = $selected_ansars[$i];
                        $rest_entry->old_embodiment_id = $embodiment_infos->id;
                        $rest_entry->memorandum_id = $memorandum_id;
                        $rest_entry->rest_date = $modified_disembodiment_date;

                        if (strcasecmp($global_unit, "Year") == 0) {
                            $rest_period = $global_value;
                            $rest_entry->active_date = Carbon::parse($modified_disembodiment_date)->addYear($rest_period);
                        } elseif (strcasecmp($global_unit, "Month") == 0) {
                            $rest_period = $global_value;
                            $rest_entry->active_date = Carbon::parse($modified_disembodiment_date)->addMonth($rest_period);
                        } elseif (strcasecmp($global_unit, "Day") == 0) {
                            $rest_period = $global_value;
                            $rest_entry->active_date = Carbon::parse($modified_disembodiment_date)->addDay($rest_period);
                        }

//                    $rest_entry->active_date = Carbon::parse($disembodiment_date)->addDay(182)->addHour(6);
                        $joining_date = Carbon::parse($embodiment_infos->joining_date);

                        $disembodiment_date_converted = Carbon::parse($modified_disembodiment_date)->addDay(1);
                        $service_days = $disembodiment_date_converted->diffInDays($joining_date);
                        $rest_entry->total_service_days = $service_days;
                        $rest_entry->disembodiment_reason_id = $disembodiment_reason[$i];
                        $rest_entry->rest_form = "Regular";
                        $rest_entry->action_user_id = Auth::user()->id;
                        $rest_entry->comment = $disembodiment_comment;
                        $rest_entry->save();

                        $embodiment_log_update = new EmbodimentLogModel();
                        $embodiment_log_update->old_embodiment_id = $embodiment_infos->id;
                        $embodiment_log_update->old_memorandum_id = $embodiment_infos->memorandum_id;
                        $embodiment_log_update->ansar_id = $selected_ansars[$i];
                        $embodiment_log_update->kpi_id = $embodiment_infos->kpi_id;
                        $embodiment_log_update->reporting_date = $embodiment_infos->reporting_date;
                        $embodiment_log_update->joining_date = $embodiment_infos->joining_date;
                        $embodiment_log_update->transfered_date = $embodiment_infos->transfered_date;
                        $embodiment_log_update->release_date = $modified_disembodiment_date;
                        $embodiment_log_update->disembodiment_reason_id = $disembodiment_reason[$i];
                        $embodiment_log_update->move_to = "Rest";
                        $embodiment_log_update->service_extension_status = $embodiment_infos->service_extension_status;
                        $embodiment_log_update->comment = $disembodiment_comment;
                        $embodiment_log_update->action_user_id = Auth::user()->id;
                        $embodiment_log_update->save();

                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 1, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);

                        $embodiment_infos->delete();
                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'DISEMBODIMENT', 'from_state' => 'EMBODIED', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
                    }
                }
            }
            DB::commit();
            CustomQuery::addActionlog($user, true);
        } catch
        (Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' => "Ansar/s not disemboded"]);
        }

        return Response::json(['status' => true, 'message' => "Ansar/s disemboded successfully"]);
    }

    public function serviceExtensionView()
    {
        return view('HRM::Embodiment.service_extension_view');
    }

    public function loadAnsarDetail()
    {
        $ansar_id = Input::get('ansar_id');
        $ansar_check = DB::table('tbl_ansar_status_info')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
            ->where('tbl_ansar_status_info.rest_status', '=', 0)
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0)
            ->select('tbl_ansar_status_info.ansar_id')
            ->first();

        if (!is_null($ansar_check)) {
            $ansar_details = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->where('tbl_embodiment.service_extension_status', '=', 0)
                ->select('tbl_embodiment.*', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_kpi_info.kpi_name', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng')
                ->first();
            return Response::json($ansar_details);
        }
    }

    public function serviceExtensionEntry(Request $request)
    {
        $rules=[
            'ansar_id'=>'required|numeric|regex:/^[0-9]+$/',
            'extended_period'=>'required|numeric|min:1|max:12',
            'service_extension_comment'=>'required|regex:/^[a-zA-Z0-9 ]+$/',
            'ansarExist'=>'numeric|min:0|max:1'
        ];
        $message=[
            'ansar_id.required'=>'Ansar ID is required',
            'ansar_id.numeric'=>'Ansar ID must be numeric',
            'ansar_id.regex'=>'Ansar ID must be numeric',
            'extended_period.required'=>'Extended Period is required',
            'extended_period.numeric'=>'Extended Period must be numeric',
            'extended_period.min'=>'Extended Period Cannot be less than 1 Months',
            'extended_period.max'=>'Extended Period Cannot be more than 12 Months',
            'service_extension_comment.required'=>'Comment is required',
            'service_extension_comment.regex'=>'Comment must contain Alphabets, Numbers and Space Characters',
        ];
        $valid=Validator::make(Input::all(), $rules, $message);
        if($valid->fails()){
            return Redirect::back()->withInput(Input::all())->withErrors($valid);
        }
        $ansar_id = $request->input('ansar_id');
        $extended_period = $request->input('extended_period');
        $service_extension_comment = $request->input('service_extension_comment');
        $ansarExist = $request->input('ansarExist');
        if($ansarExist==1){
            DB::beginTransaction();
            try {
                $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();

                $serviceExtenstionEntry = new ServiceExtensionModel();
                $serviceExtenstionEntry->embodiment_id = $embodiment_info->id;
                $serviceExtenstionEntry->ansar_id = $ansar_id;
                $serviceExtenstionEntry->pre_service_ended_date = $embodiment_info->service_ended_date;
                $serviceExtenstionEntry->new_extended_date = Carbon::parse($embodiment_info->service_ended_date)->addMonth($extended_period);
                $serviceExtenstionEntry->service_extension_comment = $service_extension_comment;
                $serviceExtenstionEntry->action_user_id = Auth::user()->id;
                $serviceExtenstionEntry->save();

                $embodiment_info->service_ended_date = Carbon::parse($embodiment_info->service_ended_date)->addMonth($extended_period);
                $embodiment_info->service_extension_status = 1;
                $embodiment_info->save();

                DB::commit();
                return Redirect::route('service_extension_view')->with('success_message', 'Service Date for Ansar Extended Successfully!');
            } catch (\Exception $e) {
                return Redirect::route('service_extension_view')->with('error_message', 'Service Date for Ansar has not been Extended!');
            }
        }
    }

    public function disembodimentDateCorrectionView()
    {
        return view('HRM::Embodiment.disembodiment_date_correction_view');
    }

    public function loadAnsarForDisembodimentDateCorrection()
    {
        $ansar_id = Input::get('ansar_id');
        $ansar_details = DB::table('tbl_rest_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_rest_info.ansar_id', '=', $ansar_id)
            ->select('tbl_rest_info.ansar_id as id', 'tbl_rest_info.rest_date as r_date', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.data_of_birth as dob', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')
            ->first();
        return Response::json($ansar_details);
    }

    public function newDisembodimentDateEntry(Request $request)
    {
        $rules=[
            'ansarExist'=>'numeric|min:0|max:1',
            'ansar_id'=> 'required|numeric|regex:/^[0-9]+$/',
            'new_disembodiment_date'=>['required','regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/'],
        ];
        $message=[
            'ansar_id.required' =>'Ansar ID is required',
            'new_disembodiment_date.required' =>'New Disembodiment Date is required',
            'ansar_id.numeric' =>'Ansar ID must be numeric',
            'ansar_id.regex' =>'Ansar ID must be numeric',
            'new_disembodiment_date.regex' =>'New Disembodiment Date format is invalid',
        ];
        $valid=Validator::make(Input::all(), $rules, $message);
        if($valid->fails()){
            return Redirect::back()->withInput(Input::all())->withErrors($valid);
        }
        $ansar_id = $request->input('ansar_id');
        $new_disembodiment_date = $request->input('new_disembodiment_date');
        $modified_new_disembodiment_date = Carbon::parse($new_disembodiment_date)->format('Y-m-d');

        DB::beginTransaction();
        try {
            $global_value = GlobalParameterFacades::getValue("rest_period");
            $global_unit = GlobalParameterFacades::getUnit("rest_period");

            $rest_info_update = RestInfoModel::where('ansar_id', $ansar_id)->first();
            $rest_info_update->rest_date = $modified_new_disembodiment_date;

            if (strcasecmp($global_unit, "Year") == 0) {
                $rest_period = $global_value;
                $rest_info_update->active_date = Carbon::parse($modified_new_disembodiment_date)->addYear($rest_period)->addHour(6);
            } elseif (strcasecmp($global_unit, "Month") == 0) {
                $rest_period = $global_value;
                $rest_info_update->active_date = Carbon::parse($modified_new_disembodiment_date)->addMonth($rest_period)->addHour(6);
            } elseif (strcasecmp($global_unit, "Day") == 0) {
                $rest_period = $global_value;
                $rest_info_update->active_date = Carbon::parse($modified_new_disembodiment_date)->addDay($rest_period)->addHour(6);
            }
            $rest_info_update->save();

            $embodiment_log_update = EmbodimentLogModel::where('ansar_id', $ansar_id)->where('created_at', $rest_info_update->created_at)->first();
            $embodiment_log_update->release_date = $modified_new_disembodiment_date;
            $embodiment_log_update->save();

            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Redirect::route('disembodiment_date_correction_view')->with('success_message', 'Dis-Embodiment Date is corrected Successfully!');
    }

    public function embodimentMemorandumIdCorrectionView()
    {
        return view('HRM::Embodiment.embodiment_memorandum_id_correction');
    }

    public function loadAnsarForEmbodimentMemorandumIdCorrection()
    {
        $ansar_id = Input::get('ansar_id');
        $ansar_details = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
            ->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.memorandum_id as m_id', 'tbl_kpi_info.kpi_name as kpi', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')
            ->first();
        return Response::json($ansar_details);
    }

    public function newMemorandumIdCorrectionEntry(Request $request)
    {
        DB::beginTransaction();
        try {
            $ansar_id = $request->input('ansar_id');
            $new_memorandum_id = $request->input('memorandum_id');

            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $new_memorandum_id;
            $memorandum_entry->save();

            $mem_id_updated = EmbodimentModel::where('ansar_id', $ansar_id)->first();
            $mem_id_updated->memorandum_id = $new_memorandum_id;
            $mem_id_updated->save();

            DB::commit();
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('embodiment_memorandum_id_correction_view')->with('success_message', 'Memorandum ID Corrected Successfully');
    }

    public function getKpiDetail()
    {
        $id = Input::get('id');

        $detail = KpiDetailsModel::where('kpi_id', $id)->select('total_ansar_given', 'no_of_ansar', 'no_of_apc', 'no_of_pc')->first();
        if(Input::exists('ansar_id')) {
            $a_id = Input::get('ansar_id');
            $embodiment_detail = DB::table('tbl_embodiment')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.kpi_id', $id)->where('tbl_designations.id', $a_id)
                ->select(DB::raw('count(tbl_embodiment.ansar_id) as total'))->first();
        }
        else{
            $ansar = DB::table('tbl_embodiment')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.kpi_id', $id)->where('tbl_designations.id', 1)
                ->select(DB::raw('count(tbl_embodiment.ansar_id) as total'));
            $apc = DB::table('tbl_embodiment')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.kpi_id', $id)->where('tbl_designations.id', 2)
                ->select(DB::raw('count(tbl_embodiment.ansar_id) as total'));
            $pc = DB::table('tbl_embodiment')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.kpi_id', $id)->where('tbl_designations.id', 3)
                ->select(DB::raw('count(tbl_embodiment.ansar_id) as total'));
            $embodiment_detail = $ansar->unionAll($apc)->unionAll($pc)->get();
        }
        return Response::json(['detail' => $detail, 'ansar_count' => $embodiment_detail]);
    }
}
