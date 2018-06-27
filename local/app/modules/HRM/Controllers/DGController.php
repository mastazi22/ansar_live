<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Facades\GlobalParameterFacades;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\models\User;
use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\BlackListInfoModel;
use App\modules\HRM\Models\BlackListModel;
use App\modules\HRM\Models\BlockListModel;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\RestInfoLogModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use App\modules\HRM\Models\TransferAnsar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Mockery\CountValidator\Exception;

class DGController extends Controller
{
    //
    function directOfferView()
    {
        return View::make('HRM::Dgview.direct_offer');
    }

    function directEmbodimentView()
    {
        return View::make('HRM::Dgview.direct_embodiment');
    }

    function directDisEmbodimentView()
    {
        return View::make('HRM::Dgview.direct_disembodiment');
    }

    function directTransferView()
    {
        return View::make('HRM::Dgview.direct_transfer');
    }

    function loadAnsarDetail()
    {
        $ansar_id = Input::get('ansar_id');
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/|exists:hrm.tbl_ansar_parsonal_info,ansar_id',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return response("Invalid Request(400)", 400);
        }
        try {
            $ansarPersonalDetail = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_ansar_parsonal_info.ansar_id',
                    'tbl_units.unit_name_bng', 'tbl_units.id as unit_id', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_designations.name_bng', 'tbl_ansar_parsonal_info.mobile_no_self')->first();
            $ansarStatusInfo = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            $ansarPanelInfo = DB::table('tbl_panel_info')->where('ansar_id', $ansar_id);
            if (!$ansarPanelInfo->exists()) {
                $ansarPanelInfo = DB::table('tbl_panel_info_log')->where('ansar_id', $ansar_id)->orderBy('id', 'desc')->select('panel_date', 'old_memorandum_id as memorandum_id')->first();
            } else {
                $ansarPanelInfo = $ansarPanelInfo->select('panel_date', 'memorandum_id')->first();
            }
            $ansarOfferInfo = DB::table('tbl_sms_offer_info')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_offer_info.district_id')
                ->where('tbl_sms_offer_info.ansar_id', '=', $ansar_id);

            if ($ansarOfferInfo->exists()) {
                $ansarOfferInfo = $ansarOfferInfo->select('tbl_sms_offer_info.sms_send_datetime as offerDate', 'tbl_units.unit_name_bng as offerUnit')->first();
            } else {
                $ansarOfferInfo = DB::table('tbl_sms_receive_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_receive_info.offered_district')
                    ->where('tbl_sms_receive_info.ansar_id', '=', $ansar_id);

                if ($ansarOfferInfo->exists()) {
                    $ansarOfferInfo = $ansarOfferInfo->select('tbl_sms_receive_info.sms_send_datetime as offerDate', 'tbl_units.unit_name_bng as offerUnit')->first();

                } else {
                    $ansarOfferInfo = DB::table('tbl_sms_send_log')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
                        ->where('tbl_sms_send_log.ansar_id', '=', $ansar_id)->orderBy('tbl_sms_send_log.id', 'desc')
                        ->select('tbl_sms_send_log.offered_date as offerDate', 'tbl_units.unit_name_bng as offerUnit')->first();
                }
            }
            $offer_cancel = DB::table('tbl_offer_cancel')->where('ansar_id', $ansar_id)->orderBy('id', 'desc')->select('offer_cancel_date as offerCancel')->first();
            $ansarEmbodimentInfo = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->where('tbl_embodiment.ansar_id', Input::get('ansar_id'));
            if (!$ansarEmbodimentInfo->exists()) {
                $ansarEmbodimentInfo = DB::table('tbl_embodiment_log')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->where('tbl_embodiment_log.ansar_id', $ansar_id)->orderBy('tbl_embodiment_log.id', 'desc')
                    ->select('tbl_embodiment_log.joining_date', 'tbl_embodiment_log.old_memorandum_id as memorandum_id', 'tbl_kpi_info.kpi_name', 'tbl_units.unit_name_bng')->first();
            } else {
                $ansarEmbodimentInfo = $ansarEmbodimentInfo
                    ->select('tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id as memorandum_id', 'tbl_kpi_info.kpi_name', 'tbl_units.unit_name_bng')
                    ->first();
            }
            $ansarDisEmbodimentInfo = '';
            if ($ansarStatusInfo->getStatus()[0] != AnsarStatusInfo::EMBODIMENT_STATUS) {
                $ansarDisEmbodimentInfo = DB::table('tbl_embodiment_log')
                    ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                    ->where('tbl_embodiment_log.ansar_id', $ansar_id)->orderBy('tbl_embodiment_log.release_date', 'desc')
                    ->select('tbl_embodiment_log.release_date as disembodiedDate', 'tbl_disembodiment_reason.reason_in_bng as disembodiedReason')->first();

            }
            return json_encode(['apid' => $ansarPersonalDetail, 'api' => $ansarPanelInfo, 'aod' => $ansarOfferInfo, 'aoci' => $offer_cancel, 'asi' => $ansarStatusInfo,
                'aei' => $ansarEmbodimentInfo, 'adei' => $ansarDisEmbodimentInfo,'status'=>$ansarStatusInfo->getStatus()[0]]);
        }catch(\Exception $e){
            return [];
        }


    }
    function loadAnsarForDirectEmbodiment(Request $request){
        $rule = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:hrm.tbl_ansar_parsonal_info,ansar_id'
        ];
        $vaild = Validator::make($request->all(), $rule);
        if ($vaild->fails()) {
            return Response::json([]);
        }
        $ansar_id = Input::get('ansar_id');

        $status = AnsarStatusInfo::where('ansar_id', $ansar_id)->first()->getStatus();
        $ansar_details = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->leftJoin('tbl_embodiment_log', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->leftJoin('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)->orderBy('tbl_embodiment_log.id','desc')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                'tbl_units.unit_name_eng', 'tbl_designations.name_eng','tbl_embodiment_log.release_date','tbl_disembodiment_reason.reason_in_bng')
            ->first();
        return Response::json(array('ansar_details' => $ansar_details, 'status' => $status[0]));
    }
    function loadAnsarForDirectDisEmbodiment(Request $request){
        $rule = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:hrm.tbl_embodiment,ansar_id'
        ];
        $vaild = Validator::make($request->all(), $rule);
        if ($vaild->fails()) {
            return Response::json([]);
        }
        $ansar_id = Input::get('ansar_id');

        $status = AnsarStatusInfo::where('ansar_id', $ansar_id)->first()->getStatus();
        $ansar_details = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->leftJoin('tbl_embodiment_log', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->leftJoin('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)->orderBy('tbl_embodiment_log.id','desc')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                'tbl_units.unit_name_eng', 'tbl_designations.name_eng','tbl_embodiment_log.release_date','tbl_disembodiment_reason.reason_in_bng','tbl_embodiment.joining_date','tbl_kpi_info.kpi_name')
            ->first();
        return Response::json(array('ansar_details' => $ansar_details, 'status' => $status[0]));
    }
    function directEmbodimentSubmit(Request $request)
    {
//        return $request->all();
        $rules = [
            'kpi_id' => 'required|numeric|regex:/^[0-9]+$/',
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/|exists:hrm.tbl_ansar_status_info,ansar_id',
            'mem_id' => 'required',
            'reporting_date' => ['required', 'regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'joining_date' => ['required', 'regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/', 'joining_date_validate:reporting_date'],
            'unit' => 'required|numeric|regex:/^[0-9]+$/',
            'thana' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return $valid->messages()->toJson();
        }
        DB::beginTransaction();
        try {
            $ansar = AnsarStatusInfo::where('ansar_id', $request->ansar_id)->first();
            if (!$ansar) {
                throw new \Exception('This ansar not found in database ');
            }
            $status = $ansar->getAnsarForDirectEmbodiment();
            if ($status === false) {
                throw new \Exception('Ansar ID: ' . $request->ansar_id . ' can`t be embodied. Because he/she not in panel,offer or rest');
            }
            $kpi = KpiGeneralModel::where('unit_id', $request->unit)->where('thana_id', $request->thana)->where('id', $request->kpi_id)->first();
            if (!$kpi) {
                throw new \Exception('Invalid request for Ansar ID: ' . $request->ansar_id);
            }
            $kpi->embodiment()->save(new EmbodimentModel([
                'ansar_id' => $request->input('ansar_id'),
                'received_sms_id' => 0,
                'emboded_status' => 'Emboded',
                'action_user_id' => Auth::user()->id,
                'service_ended_date' => GlobalParameterFacades::getServiceEndedDate($request->input('joining_date')),
                'memorandum_id' => $request->input('mem_id'),
                'reporting_date' => Carbon::parse($request->input('reporting_date'))->format('Y-m-d'),
                'transfered_date' => Carbon::parse($request->input('joining_date'))->format('Y-m-d'),
                'joining_date' => Carbon::parse($request->input('joining_date'))->format('Y-m-d'),
            ]));
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $request->input('mem_id');
            $memorandum_entry->mem_date = Carbon::parse($request->mem_date);
            $memorandum_entry->save();

            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT EMBODIMENT', 'from_state' => $status, 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
//            CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'EMBODIED', 'from_state' => $status, 'to_state' => 'EMBODIED']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['status' => false, 'message' => $e->getMessage()]);
        }
        return Response::json(['status' => true, 'message' => 'Embodiment process complete successfully']);
    }

    /**
     * This method complete dis-embodiment process for DG
     * @param Request $request
     *
     * @return mixed
     */
    function directDisEmbodimentSubmit(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:tbl_embodiment,ansar_id',
            'mem_id' => 'required',
            'dis_date' => 'required',
            'reason' => 'required',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return $valid->messages()->toJson();
        }
        DB::beginTransaction();
        try {
            $embodiment_infos = EmbodimentModel::where('ansar_id', $request->input('ansar_id'))->first();
            $joining_date = Carbon::parse($embodiment_infos->joining_date);
            $service_days = Carbon::parse($request->input('dis_date'))->diffInDays($joining_date);
            RestInfoModel::create([
                'ansar_id' => $request->ansar_id,
                'old_embodiment_id' => $embodiment_infos->id,
                'memorandum_id' => $request->mem_id,
                'rest_date' => Carbon::parse($request->input('dis_date'))->format("Y-m-d"),
                'active_date' => GlobalParameterFacades::getActiveDate($request->input('dis_date')),
                'total_service_days' => $service_days,
                'disembodiment_reason_id' => $request->reason,
                'rest_form' => 'Regular',
                'action_user_id' => Auth::user()->id,
                'comment' => $request->input('comment'),
            ]);
            $embodiment_infos->saveLog('Rest', Carbon::parse($request->input('dis_date'))->format("Y-m-d"), $request->input('comment'), $request->reason);
            AnsarStatusInfo::where('ansar_id', $request->input('ansar_id'))->update(['embodied_status' => 0, 'rest_status' => 1]);
            $embodiment_infos->delete();
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT DISEMBODIMENT', 'from_state' => 'EMBODIED', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
            DB::commit();
        } catch (\Exception $e) {
            return Response::json(['status' => false, 'message' => $e->getMessage()]);
        }
        return Response::json(['status' => true, 'message' => 'Dis-Embodiment process complete successfully']);
    }

    function loadDisembodimentReson()
    {
        $reason = DB::table('tbl_disembodiment_reason')->get();
        return Response::json($reason);
    }


    function directTransferSubmit(Request $request)
    {
//        return $request->all();
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:tbl_embodiment,ansar_id',
            'unit' => 'required|regex:/^[0-9]+$/',
            'thana' => 'required|regex:/^[0-9]+$/|exists:tbl_thana,id,unit_id,' . $request->unit,
            't_kpi_id' => 'required|regex:/^[0-9]+$/|exists:tbl_kpi_info,id,thana_id,' . $request->thana,
            'transfer_date' => 'required',
            'mem_id' => 'required',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) return $valid->messages()->toJson();
        DB::beginTransaction();
        try {
            $status = AnsarStatusInfo::where('ansar_id', $request->ansar_id)->first();
            if (!$status || $status->getStatus()[0] == AnsarStatusInfo::BLOCK_STATUS) throw new \Exception('This Ansar is Blocked');
            $t_date = Input::get('transfer_date');
            $t_kpi_id = Input::get('t_kpi_id');
            $ansar_id = Input::get('ansar_id');
            $mem_id = Input::get('mem_id');
            $e_id = EmbodimentModel::where('ansar_id', $ansar_id)->first();
            $c_kpi_id = $e_id->kpi_id;
            $p_j_date = $e_id->transfered_date;
            $e_id->kpi_id = $t_kpi_id;
            $e_id->transfered_date = Carbon::parse($t_date)->format("Y-m-d");
            $e_id->save();
            $transfer = new TransferAnsar;
            $transfer->ansar_id = $ansar_id;
            $transfer->embodiment_id = $e_id->id;
            $transfer->transfer_memorandum_id = $mem_id;
            $transfer->present_kpi_id = $c_kpi_id;
            $transfer->transfered_kpi_id = $t_kpi_id;
            $transfer->present_kpi_join_date = $p_j_date;
            $transfer->transfered_kpi_join_date = Carbon::parse($t_date)->format("Y-m-d");
            $transfer->action_by = Auth::user()->id;
            $transfer->save();
            DB::commit();
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT TRANSFER', 'from_state' => $e_id->kpi_id, 'to_state' => $t_kpi_id, 'action_by' => auth()->user()->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' => $e->getMessage()]);
        }
        return Response::json(['status' => true, 'message' => 'Transfer process complete successfully']);
    }

    public function blockListEntryView()
    {
        return view('HRM::Dgview.direct_blocklist_entry');
    }

    public function loadAnsarDetailforBlock()
    {
        $ansar_id = Input::get('ansar_id');
        $status = "";
        $ansar_details = "";
        $ansar_id = Input::get('ansar_id');
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        }
        $ansar_check = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
            ->select('tbl_ansar_status_info.free_status', 'tbl_ansar_status_info.pannel_status', 'tbl_ansar_status_info.offer_sms_status',
                'tbl_ansar_status_info.embodied_status', 'tbl_ansar_status_info.rest_status', 'tbl_ansar_status_info.block_list_status', 'tbl_ansar_status_info.black_list_status', 'tbl_ansar_parsonal_info.verified')
            ->first();

        if ($ansar_check->verified == 0 || $ansar_check->verified == 1) {
            $ansar_details = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                    'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                ->first();

            $status = "Entry";
        } else {
            if ($ansar_check->free_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
                $ansar_details = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                    ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                    ->first();

                $status = "Free";

            } elseif ($ansar_check->pannel_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
                $ansar_details = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_panel_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                    ->select('tbl_panel_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                    ->first();

                $status = "Paneled";

            } elseif ($ansar_check->offer_sms_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
                $ansar_details = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                    ->select('tbl_sms_offer_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                    ->first();

                $status = "Offer";

            } elseif ($ansar_check->embodied_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
                $ansar_details = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                    ->select('tbl_embodiment.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                    ->first();

                $status = "Embodied";

            } elseif ($ansar_check->rest_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
                $ansar_details = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                    ->select('tbl_rest_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                    ->first();

                $status = "Rest";
            }
        }

        return Response::json(array('ansar_details' => $ansar_details, 'status' => $status));
    }

    public function blockListEntry(Request $request)
    {
        $ansar_status = $request->input('ansar_status');
        $ansar_id = $request->input('ansar_id');
        $block_date = $request->input('block_date');
        $modified_block_date = Carbon::parse($block_date)->format('Y-m-d');
        $block_comment = $request->input('block_comment');
        $from_id = $request->input('from_id');

        DB::beginTransaction();
        try {
            switch ($ansar_status) {

                case "Entry":
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Entry";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->direct_status = 1;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'ENTRY', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'ENTRY', 'to_state' => 'BLOCKED']);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'ENTRY','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'ENTRY','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                    break;

                case "Free":
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Free";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->direct_status = 1;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
                    // CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED']);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'FREE','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'FREE','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));

                    break;

                case "Paneled":
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Panel";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED']);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'PANEL','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'PANEL','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));

                    break;

                case "Offer":
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Offer";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->direct_status = 1;
                    $blocklist_entry->save();
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED']);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'OFFER','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'OFFER','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));

                    break;

                case "Embodied":
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Embodiment";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->direct_status = 1;
                    $blocklist_entry->save();
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED']);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'EMBODIED','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'EMBODIED','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));

                    break;

                case "Rest":
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Rest";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->direct_status = 1;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED']);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'REST','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'REST','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));

                    break;

            }
            AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['block_list_status' => 1]);
            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return Redirect::route('dg_blocklist_entry_view')->with('success_message', 'Ansar Blocked successfully');
    }

    public function unblockListEntryView()
    {
        return view('HRM::Dgview.direct_unblocklist_entry');
    }

    public function loadAnsarDetailforUnblock()
    {
        $ansar_id = Input::get('ansar_id');

        $ansar_details = DB::table('tbl_blocklist_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blocklist_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_blocklist_info.ansar_id', '=', $ansar_id)
            ->where('tbl_blocklist_info.date_for_unblock', '=', null)
            ->where('tbl_ansar_status_info.block_list_status', '=', 1)
            ->select('tbl_blocklist_info.block_list_from', 'tbl_blocklist_info.date_for_block', 'tbl_blocklist_info.comment_for_block', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                'tbl_units.unit_name_eng', 'tbl_designations.name_eng')->first();

        return Response::json($ansar_details);
    }

    public function unblockListEntry(Request $request)
    {
        $ansar_id = $request->input('ansar_id');
        $unblock_date = $request->input('unblock_date');
        $modified_unblock_date = Carbon::parse($unblock_date)->format('Y-m-d');
        $unblock_comment = $request->input('unblock_comment');

        DB::beginTransaction();
        try {
            $blocklist_entry = BlockListModel::where('ansar_id', $ansar_id)->first();
            $blocklist_entry->ansar_id = $ansar_id;
            $blocklist_entry->date_for_unblock = $modified_unblock_date;
            $blocklist_entry->comment_for_unblock = $unblock_comment;
            $blocklist_entry->direct_status = 1;
            $blocklist_entry->save();

            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            $ansar->block_list_status = 0;
            $ansar->save();
            switch (1) {
                case $ansar->free_status;
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'FREE']);

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'FREE','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'FREE','action_by'=>auth()->user()->id]));
                    break;
                case $ansar->pannel_status;
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'PANEL', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'PANEL']);

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'PANEL','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'PANEL','action_by'=>auth()->user()->id]));
                    break;
                case $ansar->offer_sms_status;
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'OFFER', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'OFFER']);

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'OFFER','action_by'=>auth()->user()->id]));
//                    Event::fire(newDGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'OFFER','action_by'=>auth()->user()->id]));
                    break;
                case $ansar->embodied_status;
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'EMBODIED']);

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'EMBODIED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'EMBODIED','action_by'=>auth()->user()->id]));
                    break;
                case $ansar->rest_status;
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'REST']);

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'REST','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'REST','action_by'=>auth()->user()->id]));
                    break;
                default:
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'ENTRY', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'ENTRY']);

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'ENTRY','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'UNBLOCKED','from_state'=>'BLOCKED','to_state'=>'ENTRY','action_by'=>auth()->user()->id]));
                    break;
            }
            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return Redirect::route('unblocklist_entry_view')->with('success_message', 'Ansar Unblocked successfully');
    }

    public function blackListEntryView()
    {
        return view('HRM::Dgview.direct_blacklist_entry');
    }

    public function loadAnsarDetailforBlack()
    {
        $ansar_id = Input::get('ansar_id');
        $status = "";
        $ansar_details = "";

        $ansar_check = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
            ->select('tbl_ansar_status_info.free_status', 'tbl_ansar_status_info.pannel_status', 'tbl_ansar_status_info.offer_sms_status',
                'tbl_ansar_status_info.embodied_status', 'tbl_ansar_status_info.rest_status', 'tbl_ansar_status_info.block_list_status', 'tbl_ansar_status_info.black_list_status', 'tbl_ansar_status_info.freezing_status', 'tbl_ansar_parsonal_info.verified')
            ->first();

        if ($ansar_check->verified == 0 || $ansar_check->verified == 1) {
            $ansar_details = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                    'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                ->first();

            $status = "Entry";
        } else {
            if ($ansar_check->free_status == 1 && $ansar_check->black_list_status == 0) {
                if ($ansar_check->block_list_status == 1) {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_blocklist_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Blocklisted";
                } else {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Free";
                }
            } elseif ($ansar_check->pannel_status == 1 && $ansar_check->black_list_status == 0) {
                if ($ansar_check->block_list_status == 1) {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_blocklist_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Blocklisted";
                } else {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_panel_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_panel_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Paneled";
                }
            } elseif ($ansar_check->offer_sms_status == 1 && $ansar_check->black_list_status == 0) {
                if ($ansar_check->block_list_status == 1) {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_blocklist_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Blocklisted";
                } else {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_sms_offer_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Offer";
                }
            } elseif ($ansar_check->embodied_status == 1 && $ansar_check->black_list_status == 0) {
                if ($ansar_check->block_list_status == 1) {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_blocklist_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Blocklisted";

                } else {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_embodiment.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Embodied";
                }


            } elseif ($ansar_check->rest_status == 1 && $ansar_check->black_list_status == 0) {
                if ($ansar_check->block_list_status == 1) {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_blocklist_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Blocklisted";
                } else {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_rest_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Rest";
                }

            } elseif ($ansar_check->freezing_status == 1 && $ansar_check->black_list_status == 0) {
                if ($ansar_check->block_list_status == 1) {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_blocklist_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Blocklisted";
                } else {
                    $ansar_details = DB::table('tbl_ansar_parsonal_info')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_freezing_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_freezing_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                            'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                        ->first();

                    $status = "Freeze";
                }
            }
        }
        return Response::json(array('ansar_details' => $ansar_details, 'status' => $status));
    }

    public function blackListEntry(Request $request)
    {
        $ansar_status = $request->input('ansar_status');
        $ansar_id = $request->input('ansar_id');
        $black_date = $request->input('black_date');
        $modified_black_date = Carbon::parse($black_date)->format('Y-m-d');
        $black_comment = $request->input('black_comment');
        $from_id = $request->input('from_id');
        $mobile_no = DB::table('tbl_ansar_parsonal_info')->where('ansar_id', $ansar_id)->select('tbl_ansar_parsonal_info.mobile_no_self')->first();

        DB::beginTransaction();
        try {
            switch ($ansar_status) {

                case "Entry":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Free";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->direct_status = 1;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'ENTRY','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'ENTRY','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));

                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'ENTRY', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'ENTRY', 'to_state' => 'BLACKED']);
                    break;

                case "Free":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Free";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->direct_status = 1;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'FREE','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'FREE','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREE', 'to_state' => 'BLACKED']);
                    break;

                case "Paneled":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Panel";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->direct_status = 1;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                    $panel_log_save = new PanelInfoLogModel();
                    $panel_log_save->panel_id_old = $from_id;
                    $panel_log_save->ansar_id = $ansar_id;
                    $panel_log_save->merit_list = $panel_info->ansar_merit_list;
                    $panel_log_save->panel_date = $panel_info->panel_date;
                    $panel_log_save->movement_date = Carbon::today();
                    $panel_log_save->come_from = $panel_info->come_from;
                    $panel_log_save->move_to = "Blacklist";
                    $panel_log_save->comment = $black_comment;
                    $panel_log_save->direct_status = 1;
                    $panel_log_save->action_user_id = Auth::user()->id;
                    $panel_log_save->save();

                    $panel_info->delete();
                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'PANEL','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'PANEL','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'PANEL', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'PANEL', 'to_state' => 'BLACKED']);
                    break;

                case "Offer":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Offer";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->direct_status = 1;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();


                    $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                    $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

                    if (!is_null($sms_offer_info)) {

                        $sms_log_save = new OfferSmsLog();
                        $sms_log_save->ansar_id = $ansar_id;
                        $sms_log_save->sms_offer_id = $sms_offer_info->id;
                        $sms_log_save->mobile_no = $mobile_no->mobile_no_self;

                        //$sms_log_save->offer_status=;
                        $sms_log_save->action_date = Carbon::now();
                        $sms_log_save->reply_type = "No Reply";
                        $sms_log_save->offered_district = $sms_offer_info->district_id;
                        $sms_log_save->offered_date = $sms_offer_info->sms_send_datetime;
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
                        $sms_log_save->offered_date = $sms_receive_info->sms_received_datetime;
                        $sms_log_save->action_user_id = Auth::user()->id;
                        $sms_log_save->save();

                        $sms_receive_info->delete();
                    }

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'OFFER','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'OFFER','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'OFFER', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'OFFER', 'to_state' => 'BLACKED']);
                    break;

                case "Embodied":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Embodiment";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->direct_status = 1;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                    $embodiment_log_save = new EmbodimentLogModel();
                    $embodiment_log_save->old_embodiment_id = $embodiment_info->id;
                    $embodiment_log_save->old_memorandum_id = $embodiment_info->memorandum_id;
                    $embodiment_log_save->ansar_id = $ansar_id;
                    $embodiment_log_save->kpi_id = $embodiment_info->kpi_id;
                    $embodiment_log_save->reporting_date = $embodiment_info->reporting_date;
                    $embodiment_log_save->joining_date = $embodiment_info->joining_date;
                    $embodiment_log_save->release_date = $black_date;
                    $embodiment_log_save->move_to = "Blacklist";
                    $embodiment_log_save->service_extension_status = $embodiment_info->service_extension_status;
                    $embodiment_log_save->comment = $black_comment;
                    $embodiment_log_save->direct_status = 1;
                    $embodiment_log_save->action_user_id = Auth::user()->id;
                    $embodiment_log_save->save();

                    $embodiment_info->delete();
                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'EMBODIED','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'EMBODIED','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLACKED']);
                    break;

                case "Rest":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Rest";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->direct_status = 1;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                    $rest_log_save = new RestInfoLogModel();
                    $rest_log_save->old_rest_id = $rest_info->id;
                    $rest_log_save->old_embodiment_id = $rest_info->old_embodiment_id;
                    $rest_log_save->old_memorandum_id = $rest_info->memorandum_id;
                    $rest_log_save->ansar_id = $ansar_id;
                    $rest_log_save->rest_date = $rest_info->rest_date;
                    $rest_log_save->total_service_days = $rest_info->total_service_days;
                    $rest_log_save->rest_type = $rest_info->rest_form;
                    $rest_log_save->disembodiment_reason_id = $rest_info->disembodiment_reason_id;
                    $rest_log_save->comment = $black_comment;
                    $rest_log_save->move_to = "Blacklist";
                    $rest_log_save->move_date = $modified_black_date;
                    $rest_log_save->direct_status = 1;
                    $rest_log_save->action_user_id = Auth::user()->id;
                    $rest_log_save->save();

                    $rest_info->delete();

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'REST','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'REST','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'REST', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'REST', 'to_state' => 'BLACKED']);
                    break;

                case "Freeze":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Freeze";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $freeze_info = FreezingInfoModel::where('ansar_id', $ansar_id)->first();
                    $freeze_log_save = new FreezingInfoLog();
                    $freeze_log_save->old_freez_id = $freeze_info->id;
                    $freeze_log_save->ansar_id = $ansar_id;
                    $freeze_log_save->ansar_embodiment_id = $freeze_info->ansar_embodiment_id;
                    $freeze_log_save->freez_reason = $freeze_info->freez_reason;
                    $freeze_log_save->freez_date = $freeze_info->freez_date;
                    $freeze_log_save->comment_on_freez = $freeze_info->comment_on_freez;
                    $freeze_log_save->move_frm_freez_date = $modified_black_date;
                    $freeze_log_save->move_to = "Blacklist";
                    $freeze_log_save->comment_on_move = $black_comment;
                    $freeze_log_save->direct_status = 0;
                    $freeze_log_save->action_user_id = Auth::user()->id;
                    $freeze_log_save->save();

                    $freeze_info->delete();

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'FREEZE','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREEZE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREEZE', 'to_state' => 'BLACKED']);
                    break;

                case "Blocklisted":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Blocklist";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->direct_status = 1;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $block_info = BlockListModel::where('ansar_id', $ansar_id)->first();

                    if ($block_info->block_list_from == "Entry") {
                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    } elseif ($block_info->block_list_from == "Free") {
                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);

                    } elseif ($block_info->block_list_from == "Panel") {

                        $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                        $panel_log_save = new PanelInfoLogModel();
                        $panel_log_save->panel_id_old = $from_id;
                        $panel_log_save->ansar_id = $ansar_id;
                        $panel_log_save->merit_list = $panel_info->ansar_merit_list;
                        $panel_log_save->panel_date = $panel_info->panel_date;
                        $panel_log_save->movement_date = Carbon::today();
                        $panel_log_save->come_from = "Blocklist";
                        $panel_log_save->move_to = "Blacklist";
                        $panel_log_save->comment = $black_comment;
                        $panel_log_save->direct_status = 1;
                        $panel_log_save->action_user_id = Auth::user()->id;
                        $panel_log_save->save();

                        $panel_info->delete();
                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);

                    } elseif ($block_info->block_list_from == "Embodiment") {

                        $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                        $embodiment_log_save = new EmbodimentLogModel();
                        $embodiment_log_save->old_embodiment_id = $embodiment_info->id;
                        $embodiment_log_save->old_memorandum_id = $embodiment_info->memorandum_id;
                        $embodiment_log_save->ansar_id = $ansar_id;
                        $embodiment_log_save->kpi_id = $embodiment_info->kpi_id;
                        $embodiment_log_save->reporting_date = $embodiment_info->reporting_date;
                        $embodiment_log_save->joining_date = $embodiment_info->joining_date;
                        $embodiment_log_save->release_date = $modified_black_date;
                        $embodiment_log_save->move_to = "Blacklist";
                        $embodiment_log_save->service_extension_status = $embodiment_info->service_extension_status;
                        $embodiment_log_save->comment = $black_comment;
                        $embodiment_log_save->direct_status = 1;
                        $embodiment_log_save->action_user_id = Auth::user()->id;
                        $embodiment_log_save->save();

                        $embodiment_info->delete();

                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);

                    } elseif ($block_info->block_list_from == "Rest") {

                        $blacklist_entry = new BlackListModel();
                        $blacklist_entry->ansar_id = $ansar_id;
                        $blacklist_entry->black_list_from = "Rest";
                        $blacklist_entry->from_id = $from_id;
                        $blacklist_entry->black_listed_date = $modified_black_date;
                        $blacklist_entry->black_list_comment = $black_comment;
                        $blacklist_entry->direct_status = 1;
                        $blacklist_entry->action_user_id = Auth::user()->id;
                        $blacklist_entry->save();

                        $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                        $rest_log_save = new RestInfoLogModel();
                        $rest_log_save->old_rest_id = $rest_info->id;
                        $rest_log_save->old_embodiment_id = $rest_info->old_embodiment_id;
                        $rest_log_save->old_memorandum_id = $rest_info->memorandum_id;
                        $rest_log_save->ansar_id = $ansar_id;
                        $rest_log_save->rest_date = $rest_info->rest_date;
                        $rest_log_save->total_service_days = $rest_info->total_service_days;
                        $rest_log_save->rest_type = $rest_info->rest_form;
                        $rest_log_save->disembodiment_reason_id = $rest_info->disembodiment_reason_id;
                        $rest_log_save->comment = $black_comment;
                        $rest_log_save->move_to = "Blacklist";
                        $rest_log_save->move_date = $modified_black_date;
                        $rest_log_save->direct_status = 1;
                        $rest_log_save->action_user_id = Auth::user()->id;
                        $rest_log_save->save();

                        $rest_info->delete();

                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);

                    } elseif ($block_info->block_list_from == "Freeze") {
                        $blacklist_entry = new BlackListModel();
                        $blacklist_entry->ansar_id = $ansar_id;
                        $blacklist_entry->black_list_from = "Freeze";
                        $blacklist_entry->from_id = $from_id;
                        $blacklist_entry->black_listed_date = $modified_black_date;
                        $blacklist_entry->black_list_comment = $black_comment;
                        $blacklist_entry->action_user_id = Auth::user()->id;
                        $blacklist_entry->save();

                        $freeze_info = FreezingInfoModel::where('ansar_id', $ansar_id)->first();
                        $freeze_log_save = new FreezingInfoLog();
                        $freeze_log_save->old_freez_id = $freeze_info->id;
                        $freeze_log_save->ansar_id = $ansar_id;
                        $freeze_log_save->ansar_embodiment_id = $freeze_info->ansar_embodiment_id;
                        $freeze_log_save->freez_reason = $freeze_info->freez_reason;
                        $freeze_log_save->freez_date = $freeze_info->freez_date;
                        $freeze_log_save->comment_on_freez = $freeze_info->comment_on_freez;
                        $freeze_log_save->move_frm_freez_date = $modified_black_date;
                        $freeze_log_save->move_to = "Blacklist";
                        $freeze_log_save->comment_on_move = $black_comment;
                        $freeze_log_save->direct_status = 0;
                        $freeze_log_save->action_user_id = Auth::user()->id;
                        $freeze_log_save->save();

                        $freeze_info->delete();

                    } elseif ($block_info->block_list_from == "Offer") {

                        $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                        $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

                        if (!is_null($sms_offer_info)) {

                            $sms_log_save = new OfferSmsLog();
                            $sms_log_save->ansar_id = $ansar_id;
                            $sms_log_save->sms_offer_id = $sms_offer_info->id;
                            $sms_log_save->mobile_no = $mobile_no->mobile_no_self;

                            //$sms_log_save->offer_status=;
                            $sms_log_save->reply_type = "No Reply";
                            $sms_log_save->action_date = Carbon::now();
                            $sms_log_save->offered_district = $sms_offer_info->district_id;
                            $sms_log_save->offered_date = $sms_offer_info->sms_send_datetime;
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
                            $sms_log_save->offered_date = $sms_receive_info->sms_received_datetime;
                            $sms_log_save->action_user_id = Auth::user()->id;
                            $sms_log_save->save();

                            $sms_receive_info->delete();
                        }

                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                        Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'BLOCKED','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
//                        Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'BLOCKED','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                        //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'BLOCKED', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    }
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'BLOCKED', 'to_state' => 'BLACKED']);
                    break;

            }

            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return Redirect::route('blacklist_entry_view')->with('success_message', 'Ansar Blacked successfully');
    }

    public function unblackListEntryView()
    {
        return view('HRM::Dgview.direct_unblacklist_entry');
    }

    public function loadAnsarDetailforUnblack()
    {
        $ansar_id = Input::get('ansar_id');

        $ansar_details = DB::table('tbl_blacklist_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_blacklist_info.ansar_id', '=', $ansar_id)
            ->select('tbl_blacklist_info.black_list_from', 'tbl_blacklist_info.black_listed_date', 'tbl_blacklist_info.black_list_comment', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                'tbl_units.unit_name_eng', 'tbl_designations.name_eng')->first();

        return Response::json($ansar_details);
    }

    public function unblackListEntry(Request $request)
    {
        $ansar_id = $request->input('ansar_id');
        $unblack_date = $request->input('unblack_date');
        $modified_unblack_date = Carbon::parse($unblack_date)->format('Y-m-d');
        $unblack_comment = $request->input('unblack_comment');

        DB::beginTransaction();
        try {
            $blacklist_info = BlackListModel::where('ansar_id', $ansar_id)->first();
            $blacklist_log_entry = new BlackListInfoModel();
            $blacklist_log_entry->old_blacklist_id = $blacklist_info->id;
            $blacklist_log_entry->ansar_id = $ansar_id;
            $blacklist_log_entry->black_list_from = $blacklist_info->black_list_from;
            $blacklist_log_entry->from_id = $blacklist_info->from_id;
            $blacklist_log_entry->black_listed_date = $blacklist_info->black_listed_date;
            $blacklist_log_entry->black_list_comment = $blacklist_info->black_list_comment;
            $blacklist_log_entry->unblacklist_date = $modified_unblack_date;
            $blacklist_log_entry->unblacklist_comment = $unblack_comment;
            $blacklist_log_entry->move_to = "Panel";
            $blacklist_log_entry->move_date = $unblack_date;
            $blacklist_log_entry->direct_status = 1;
            $blacklist_log_entry->action_user_id = Auth::user()->id;
            $blacklist_log_entry->save();

            $blacklist_info->delete();


            AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 1, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//            Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'FREE','from_state'=>'BLACKED','to_state'=>'FREE','action_by'=>auth()->user()->id]));
//            Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'FREE','from_state'=>'BLACKED','to_state'=>'FREE','action_by'=>auth()->user()->id]));
            //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLACKED', 'from_state' => 'BLACKED', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);
            CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLACKED', 'from_state' => 'BLACKED', 'to_state' => 'FREE']);

            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return Redirect::route('dg_unblacklist_entry_view')->with('success_message', 'Ansar removed from Blacklist successfully');
    }

    public function directCancelPanelView()
    {
        return view('HRM::Dgview.direct_cancel_panel');
    }

    public function loadAnsarDetailforCancelPanel()
    {
        $ansar_id = Input::get('ansar_id');

        $ansar_details = DB::table('tbl_panel_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->where('tbl_panel_info.ansar_id', '=', $ansar_id)
            ->select('tbl_panel_info.*', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng')
            ->first();

        return Response::json($ansar_details);
    }

    public function cancelPanelEntry(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:hrm.tbl_panel_info',
            'cancel_panel_date' => 'required',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput($request->except(['_token']));
        }
        $ansar_id = $request->input('ansar_id');
        $cancel_panel_date = $request->input('cancel_panel_date');
        $modified_cancel_panel_date = Carbon::parse($cancel_panel_date)->format('Y-m-d');
        $cancel_panel_comment = $request->input('cancel_panel_comment');

        DB::beginTransaction();
        try {
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
//            return $ansar->getStatus();
            if (!$ansar) throw new \Exception("This Ansar is not exists");
            if (in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->getStatus()) || in_array(AnsarStatusInfo::BLACK_STATUS, $ansar->getStatus()) || !in_array(AnsarStatusInfo::PANEL_STATUS, $ansar->getStatus())) throw new \Exception("This Ansar is not available in panel");
            $panel_info = $ansar->panel;
            if (!$panel_info) throw new \Exception("This Ansar is not in panel");
            if ($panel_info->come_from == 'Rest') {
                RestInfoModel::create([
                    'ansar_id' => $ansar_id,
                    'rest_date' => $modified_cancel_panel_date,
                    'comment' => $cancel_panel_comment,
                    'disembodiment_reason_id' => 8,
                    'action_user_id' => Auth::user()->id,
                    'rest_form' => "Panel",
                ]);
                $panel_info->saveLog("Rest", Carbon::now(), $cancel_panel_comment);
                $ansar->update([
                    'rest_status' => 1,
                    'pannel_status' => 0
                ]);
                CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT CANCEL PANEL', 'from_state' => 'PANEL', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
            } else {
                $panel_info->saveLog("Free", Carbon::now(), $cancel_panel_comment);
                $ansar->update([
                    'free_status' => 1,
                    'pannel_status' => 0
                ]);
                CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT CANCEL PANEL', 'from_state' => 'PANEL', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);
            }
            $panel_info->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::route('direct_panel_cancel_view')->with('success_message', 'Ansar Canceled from Panel successfully');
    }

    public function directPanelView()
    {
        return view('HRM::Dgview.direct_panel');
    }

    public function loadAnsarDetailforDirectPanel()
    {
        $ansar_id = Input::get('ansar_id');
        $status = "No Status Found";

        $ansar_details = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->distinct()
            ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.verified', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng')
            ->first();
        if ($ansar_details) {
            return Response::json(array('ansar_details' => $ansar_details, 'status' => strtoupper(AnsarStatusInfo::where('ansar_id', $ansar_id)->first()->getStatus()[0])));
        }
        return Response::json([]);
    }
    public function loadAnsarDetailforDirectOffer()
    {
        $ansar_id = Input::get('ansar_id');
        $status = "No Status Found";
        try {
            $ansar_details = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
                ->leftJoin('tbl_embodiment_log', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->leftJoin('tbl_kpi_info', 'tbl_embodiment_log.kpi_id', '=', 'tbl_kpi_info.id')
                ->leftJoin('tbl_units as t', 'tbl_kpi_info.unit_id', '=', 't.id')
                ->leftJoin('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->distinct()->orderBy('tbl_embodiment_log.id', 'desc')
                ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.verified', 'tbl_designations.code', 'tbl_units.unit_name_eng', 'tbl_embodiment_log.release_date',
                    'tbl_disembodiment_reason.reason_in_bng', 't.unit_name_bng as tt', 'tbl_kpi_info.kpi_name')
                ->first();
            if ($ansar_details) {
                return Response::json(array('ansar_details' => $ansar_details, 'status' => strtoupper(AnsarStatusInfo::where('ansar_id', $ansar_id)->first()->getStatus()[0])));
            }
        }catch (\Exception $e){
            return Response::json([]);
        }
        return Response::json([]);
    }

    public function directPanelEntry(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|unique:tbl_panel_info,ansar_id',
            'memorandum_id' => 'required',
            'direct_panel_date' => 'required',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput($request->all());
        }
        $ansar_id = $request->input('ansar_id');
        $memorandum_id = $request->input('memorandum_id');
        $direct_panel_date = $request->input('direct_panel_date');
        $modified_direct_panel_date = Carbon::parse($direct_panel_date)->format('Y-m-d');
        $direct_panel_comment = $request->input('direct_panel_comment');

        DB::beginTransaction();
        try {
            $memorandum_id_save = new MemorandumModel();
            $memorandum_id_save->memorandum_id = $memorandum_id;
            $memorandum_id_save->save();
            $ansar = AnsarStatusInfo::where('ansar_id', $request->ansar_id)->first();
            if (!$ansar) throw new \Exception('No Ansar available with this ID ' . $request->ansar_id);

            switch ($ansar->getStatus()[0]) {
                case AnsarStatusInfo::FREE_STATUS:
                    PanelModel::create([
                        'ansar_id' => $ansar_id,
                        'panel_date' => $modified_direct_panel_date,
                        'memorandum_id' => $memorandum_id,
                        'come_from' => "Free",
                        'action_user_id' => Auth::user()->id,
                    ]);
                    $ansar->update([
                        'free_status' => 0,
                        'pannel_status' => 1,
                    ]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT PANEl', 'from_state' => 'FREE', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::REST_STATUS:
                    $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                    $rest_info->saveLog("Panel", $direct_panel_date, $direct_panel_comment);
                    PanelModel::create([
                        'ansar_id' => $ansar_id,
                        'panel_date' => $modified_direct_panel_date,
                        'memorandum_id' => $memorandum_id,
                        'come_from' => "Rest",
                        'action_user_id' => Auth::user()->id,
                    ]);
                    $ansar->update([
                        'rest_status' => 0,
                        'pannel_status' => 1,
                    ]);
                    $rest_info->delete();
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT PANEl', 'from_state' => 'REST', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    break;
                default:
                    throw new \Exception('This Ansar can`t be paneled. Because he is not in Free or Rest status');
                    break;
            }
            DB::commit();
        } catch (\Exception $e) {
            return Redirect::back()->with('error_message', $e->getMessage());
        }

        return Redirect::route('direct_panel_view')->with('success_message', 'Ansar Added in the Panel successfully');
    }

    public function directOfferSend(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/',
            'unit_id' => 'required|regex:/^[0-9]+$/',
            'offer_date' => 'required|offer_date_validate',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return $valid->messages()->toJson();
        }
        DB::beginTransaction();
        try {
            $a = PersonalInfo::where('ansar_id', $request->ansar_id)->first();
            if (!$a) throw new \Exception('Invalid Ansar ID');
            $status = $a->status->getStatus();
            if ((!in_array(AnsarStatusInfo::PANEL_STATUS, $status) && !in_array(AnsarStatusInfo::REST_STATUS, $status)) || in_array(AnsarStatusInfo::BLOCK_STATUS, $status) || in_array(AnsarStatusInfo::BLACK_STATUS, $status)||in_array(AnsarStatusInfo::OFFER_BLOCK_STATUS, $status)) throw new \Exception("This ansar not eligible for offer");
            if (!$a && !preg_match('/^(\+88)?0[0-9]{10}/', $a->mobile_no_self)) throw new Exception("Invalid mobile number");
            $a->offer_sms_info()->save(new OfferSMS([
                'sms_send_datetime' => Carbon::parse($request->offer_date)->format("y-m-d H:i:s"),
                'sms_end_datetime' => Carbon::parse($request->offer_date)->addHours(24)->format("y-m-d H:i:s"),
                'district_id' => $request->unit_id,
                'action_user_id' => auth()->user()->id,
                'come_from' => $status[0]
            ]));
            switch ($status[0]) {
                case AnsarStatusInfo::PANEL_STATUS:
                    $a->panel->saveLog('Offer', Carbon::today());
                    $a->status->update(['pannel_status' => 0, 'offer_sms_status' => 1]);
                    $a->panel->delete();
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT OFFER', 'from_state' => 'PANEL', 'to_state' => 'OFFER', 'action_by' => auth()->user()->id]);

                    break;
                case AnsarStatusInfo::REST_STATUS:
                    $a->status->update(['rest_status' => 0, 'offer_sms_status' => 1]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DIRECT OFFER', 'from_state' => 'REST', 'to_state' => 'OFFER', 'action_by' => auth()->user()->id]);
                    break;
                default:
                    throw new \Exception('Invalid Ansar ID');
                    break;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' => $e->getMessage()]);
        }
        return Response::json(['status' => true, 'message' => "Offer send successfully"]);
    }

    public function directOfferCancel(Request $request){
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:hrm.tbl_ansar_parsonal_info,ansar_id'
        ];
        $vaild = Validator::make(Input::all(), $rules);
        if ($vaild->fails()) {
            return Response::json(['status' => false, 'message' => "Invalid Ansar id"]);
        }
        $ansar_ids = $request->ansar_id;
        DB::beginTransaction();
        try {
            $ansar = PersonalInfo::where('ansar_id',$ansar_ids)->first();
            $status = $ansar->status->getStatus();
            if(in_array(AnsarStatusInfo::BLOCK_STATUS,$status)){
                throw new \Exception("This Ansar is Blocked");
            }
            if(!in_array(AnsarStatusInfo::OFFER_STATUS,$status)){
                throw new \Exception("This Ansar is not in offer status");
            }
            $offered_ansar = $ansar->offer_sms_info;
            if (!$offered_ansar) $received_ansar = $ansar->receiveSMS;
            if($offered_ansar&&$offered_ansar->come_from=='rest'){
                $ansar->status()->update([
                    'offer_sms_status'=>0,
                    'rest_status'=>1,
                ]);
            }
            else{
                $panel_log = $ansar->panelLog()->first();
                $ansar->panel()->save(new PanelModel([
                    'memorandum_id'=>$panel_log->old_memorandum_id,
                    'panel_date'=>Carbon::now(),
                    'come_from'=>'OfferCancel',
                    'ansar_merit_list'=>1,
                    'action_user_id'=>auth()->user()->id,
                ]));
                $ansar->status()->update([
                    'offer_sms_status'=>0,
                    'pannel_status'=>1,
                ]);
            }
            $ansar->offerCancel()->save(new OfferCancel([
                'offer_cancel_date'=>Carbon::now()
            ]));
            if ($offered_ansar) {
                $ansar->offerLog()->save(new OfferSmsLog([
                    'offered_date'=>$offered_ansar->sms_send_datetime,
                    'action_date'=>Carbon::now(),
                    'offered_district'=>$offered_ansar->district_id,
                    'action_user_id'=>auth()->user()->id,
                    'reply_type'=>'No Reply',
                ]));
                $offered_ansar->delete();
            } else {
                $ansar->offerLog()->save(new OfferSmsLog([
                    'offered_date'=>$received_ansar->sms_send_datetime,
                    'offered_district'=>$received_ansar->offered_district,
                    'action_user_id'=>auth()->user()->id,
                    'reply_type'=>'Yes',
                ]));
                $received_ansar->delete();
            }
            auth()->user()->actionLog()->save(new ActionUserLog([
                'ansar_id' => $ansar_ids,
                'action_type' => 'CANCEL OFFER',
                'from_state' => 'OFFER',
                'to_state' => 'PANEL'
            ]));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' =>$e->getMessage()]);
        }
        return Response::json(['status' => true, 'message' => 'Offer cancel successfully']);
    }

    public function viewUserActionLog(Request $request){

        if(strcasecmp($request->method(),"post")==0) {
            //return Carbon::parse($request->from_date)."<br>".Carbon::parse($request->to_date);
            try {
                $form_date = Carbon::now()->toDateString();
                $to_date = Carbon::parse($form_date)->subHours(48)->toDateString();
//                return $form_date." ".$to_date;
                DB::enableQueryLog();
                $user = User::where('user_name', $request->user_name)->first();
                if(!$user) throw new \Exception();
                $data = $user->actionLog()->whereDate('created_at','>',$to_date)->whereDate('created_at','<=',$form_date)->select('ansar_id', 'from_state', 'to_state', 'action_type', DB::raw('DATE_FORMAT(created_at,"%d %b. %Y") as date'), DB::raw('DATE_FORMAT(created_at,"%r") as time'))->orderBy('created_at', 'desc')->get();
//                return DB::getQueryLog();
                return View::make('HRM::Partial_view.partial_user_action_log', ['logs' => collect($data)->groupBy('date'), 'user' => $user]);
            }catch(\Exception $e){
                return View::make('HRM::Partial_view.partial_user_action_log', ['logs' => [], 'user' => null]);
            }
        }
        else if(strcasecmp($request->method(),"get")==0) {
            return View::make('HRM::Dgview.user_action_log');
        }
        else abort(401);
    }
}
