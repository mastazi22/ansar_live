<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\BlackListInfoModel;
use App\modules\HRM\Models\BlackListModel;
use App\modules\HRM\Models\BlockListModel;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
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
        $ansarPersonalDetail = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', Input::get('ansar_id'))
            ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.profile_pic','tbl_ansar_parsonal_info.ansar_id',
                'tbl_units.unit_name_bng', 'tbl_units.id as unit_id', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_designations.name_bng', 'tbl_ansar_parsonal_info.mobile_no_self')->first();
        $ansarStatusInfo = DB::table('tbl_ansar_status_info')->where('ansar_id', Input::get('ansar_id'))
            ->select('*')->first();
        $ansarPanelInfo = DB::table('tbl_panel_info')->where('ansar_id', Input::get('ansar_id'));
        if (!$ansarPanelInfo->exists()) {
            $ansarPanelInfo = DB::table('tbl_panel_info_log')->where('ansar_id', Input::get('ansar_id'))->orderBy('id', 'desc')->select('panel_date', 'panel_id_old as memorandum_id')->first();
        } else {
            $ansarPanelInfo = $ansarPanelInfo->select('panel_date', 'memorandum_id')->first();
        }
        $ansarOfferInfo = DB::table('tbl_sms_offer_info')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_offer_info.district_id')
            ->where('tbl_sms_offer_info.ansar_id', '=', Input::get('ansar_id'));

        if ($ansarOfferInfo->exists()) {
            $ansarOfferInfo = $ansarOfferInfo->select('tbl_sms_offer_info.created_at as offerDate', 'tbl_units.unit_name_bng as offerUnit')->first();
        } else {
            $ansarOfferInfo = DB::table('tbl_sms_receive_info')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_receive_info.offered_district')
                ->where('tbl_sms_receive_info.ansar_id', '=', Input::get('ansar_id'));

            if ($ansarOfferInfo->exists()) {
                $ansarOfferInfo = $ansarOfferInfo->select('tbl_sms_receive_info.sms_send_datetime as offerDate', 'tbl_units.unit_name_bng as offerUnit')->first();

            } else {
                $ansarOfferInfo = DB::table('tbl_sms_send_log')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
                    ->where('tbl_sms_send_log.ansar_id', '=', Input::get('ansar_id'))->orderBy('tbl_sms_send_log.id', 'desc')
                    ->select('tbl_sms_send_log.offered_date as offerDate', 'tbl_units.unit_name_bng as offerUnit')->first();
            }
        }
        $offer_cancel = DB::table('tbl_offer_cancel')->where('ansar_id', Input::get('ansar_id'))->orderBy('id', 'desc')->select('offer_cancel_date as offerCancel')->first();
        $ansarEmbodimentInfo = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->where('tbl_embodiment.ansar_id', Input::get('ansar_id'));
        if (!$ansarEmbodimentInfo->exists()) {
            $ansarEmbodimentInfo = DB::table('tbl_embodiment_log')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->where('tbl_embodiment_log.ansar_id', Input::get('ansar_id'))->orderBy('tbl_embodiment_log.id', 'desc')
                ->select('tbl_embodiment_log.joining_date', 'tbl_embodiment_log.old_memorandum_id as memorandum_id', 'tbl_kpi_info.kpi_name', 'tbl_units.unit_name_bng')->first();
        } else {
            $ansarEmbodimentInfo = $ansarEmbodimentInfo
                ->select('tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id as memorandum_id', 'tbl_kpi_info.kpi_name', 'tbl_units.unit_name_bng')
                ->first();
        }
        $ansarDisEmbodimentInfo = DB::table('tbl_embodiment_log')
            ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
            ->where('tbl_embodiment_log.ansar_id', Input::get('ansar_id'))->orderBy('tbl_embodiment_log.id', 'desc')
            ->select('tbl_embodiment_log.release_date as disembodiedDate', 'tbl_disembodiment_reason.reason_in_bng as disembodiedReason')->first();
        return json_encode(['apid' => $ansarPersonalDetail, 'api' => $ansarPanelInfo, 'aod' => $ansarOfferInfo, 'aoci' => $offer_cancel, 'asi' => $ansarStatusInfo,
            'aei' => $ansarEmbodimentInfo, 'adei' => $ansarDisEmbodimentInfo]);


    }

    function directEmbodimentSubmit(Request $request)
    {
        $srm = SmsReceiveInfoModel::where('ansar_id', $request->input('ansar_id'))->first();
        if (!$srm) {
            return Response::json(['status' => false, 'message' => 'This ansar hasn`t accepted offer yet. Please wait until he/she accept offer ']);
        }
        DB::beginTransaction();
        try {
            $embodiment_entry = new EmbodimentModel;
            $embodiment_entry->ansar_id = $request->input('ansar_id');
            $embodiment_entry->kpi_id = $request->input('kpi_id');
            $embodiment_entry->memorandum_id = $request->input('mem_id');
            $embodiment_entry->reporting_date = Carbon::createFromFormat("d-M-Y", $request->input('reporting_date'))->format("Y-m-d");
            $embodiment_entry->joining_date = Carbon::createFromFormat("d-M-Y", $request->input('joining_date'))->format("Y-m-d");
            $embodiment_entry->transfered_date = Carbon::createFromFormat("d-M-Y", $request->input('joining_date'))->format("Y-m-d");
            $embodiment_entry->emboded_status = "Emboded";
            $embodiment_entry->service_ended_date = Carbon::parse($request->input('joining_date'))->addHours(6)->addYears(3);
            $embodiment_entry->save();
            AnsarStatusInfo::where('ansar_id', $request->input('ansar_id'))->update(['embodied_status' => 1, 'offer_sms_status' => 0]);
            $sml = new OfferSmsLog;
            $sml->ansar_id = $srm->ansar_id;
            $sml->offered_district = $srm->offered_district;
            $sml->action_user_id = $srm->action_user_id;
            $sml->offered_date = $srm->sms_send_datetime;
            $sml->reply_type = 'Yes';
            $sml->save();
            $srm->delete();
            DB::commit();
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'EMBODIED', 'from_state' => 'OFFER', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
            CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'EMBODIED', 'from_state' => 'OFFER', 'to_state' => 'EMBODIED']);
        } catch (Exception $e) {
            return Response::json(['status' => false, 'message' => 'An Error occur while inserting. Please try again later']);
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
        DB::beginTransaction();
        try {
            $rest_entry = new RestInfoModel();
            $rest_entry->ansar_id = $request->input('ansar_id');
            $embodiment_infos = EmbodimentModel::where('ansar_id', $request->input('ansar_id'))->first();

            $rest_entry->old_embodiment_id = $embodiment_infos->id;

            $rest_entry->memorandum_id = $request->input('mem_id');;

            $rest_entry->rest_date = Carbon::createFromFormat("d-M-Y", $request->input('dis_date'))->format("Y-m-d");
            $rest_entry->active_date = Carbon::parse($request->input('dis_date'))->addDay(182);

            $joining_date = Carbon::parse($embodiment_infos->joining_date);
            $disembodiment_date = Carbon::createFromFormat("d-M-Y", $request->input('dis_date'))->format("Y-m-d");
            $service_days = $disembodiment_date->diffInDays($joining_date);


            $rest_entry->total_service_days = $service_days;
            $rest_entry->disembodiment_reason_id = $request->input('reason');
            $rest_entry->rest_form = "Regular";
            $rest_entry->action_user_id = $user_type = Auth::user()->id;
            $rest_entry->comment = $request->input('comment');
            $rest_entry->save();

            $embodiment_log_update = new EmbodimentLogModel();
            $embodiment_log_update->old_embodiment_id = $embodiment_infos->id;
            $embodiment_log_update->old_memorandum_id = $embodiment_infos->memorandum_id;
            $embodiment_log_update->ansar_id = $request->input('ansar_id');
            $embodiment_log_update->kpi_id = $embodiment_infos->kpi_id;
            $embodiment_log_update->reporting_date = $embodiment_infos->reporting_date;
            $embodiment_log_update->joining_date = $embodiment_infos->joining_date;
            $embodiment_log_update->transfered_date = $embodiment_infos->transfered_date;
            $embodiment_log_update->release_date = Carbon::createFromFormat("d-M-Y", $request->input('dis_date'))->format("Y-m-d");
            $embodiment_log_update->disembodiment_reason_id = $request->input('reason');
            $embodiment_log_update->move_to = "Rest";
            $embodiment_log_update->action_user_id = $user_type = Auth::user()->id;
            $embodiment_log_update->save();

            AnsarStatusInfo::where('ansar_id', $request->input('ansar_id'))->update(['embodied_status' => 0, 'rest_status' => 1]);

            EmbodimentModel::where('ansar_id', $request->input('ansar_id'))->delete();
            DB::commit();
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DISEMBODIMENT', 'from_state' => 'EMBODIED', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
            CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'DISEMBODIMENT', 'from_state' => 'EMBODIED', 'to_state' => 'REST']);
        } catch (Exception $e) {
            return Response::json(['status' => false, 'message' => 'An Error occur while inserting. Please try again later']);
        }
        return Response::json(['status' => true, 'message' => 'Dis-Embodiment process complete successfully']);
    }

    function loadDisembodimentReson()
    {
        $reason = DB::table('tbl_disembodiment_reason')->get();
        return Response::json($reason);
    }


    function directTransferSubmit()
    {
        $t_date = Input::get('transfer_date');
        $t_kpi_id = Input::get('t_kpi_id');
        $c_kpi_id = Input::get('c_kpi_id');
        $ansar_id = Input::get('ansar_id');
        $mem_id = Input::get('mem_id');
        DB::beginTransaction();
        try {
            $e_id = EmbodimentModel::where('ansar_id', $ansar_id)->first();
            $p_j_date = $e_id->transfered_date;
            $e_id->kpi_id = $t_kpi_id;
            $e_id->transfered_date = Carbon::createFromFormat("d-M-Y", $t_date)->format("Y-m-d");
            $e_id->save();
            $transfer = new TransferAnsar;
            $transfer->ansar_id = $ansar_id;
            $transfer->embodiment_id = $e_id->id;
            $transfer->transfer_memorandum_id = $mem_id;
            $transfer->present_kpi_id = $c_kpi_id;
            $transfer->transfered_kpi_id = $t_kpi_id;
            $transfer->present_kpi_join_date = $p_j_date;
            $transfer->transfered_kpi_join_date = Carbon::createFromFormat("d-M-Y", $t_date)->format("Y-m-d");
            $transfer->action_by = Auth::user()->id;
            $transfer->save();
            DB::commit();
            CustomQuery::addDGlog(['ansar_id' => $ansar_id, 'action_type' => 'TRANSFER', 'from_state' => $c_kpi_id, 'to_state' => $t_kpi_id]);
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'data' => 'An error occur while transfer. Please try again later']);
        }
        return Response::json(['status' => true, 'data' => 'Transfer process complete successfully']);
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

                $status = "Panneled";

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

                $status = "Embodded";

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

                case "Panneled":
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

                case "Embodded":
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
        return Redirect::action('DGController@blockListEntryView')->with('success_message', 'Ansar Blocked successfully');
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
        return Redirect::action('DGController@unblockListEntryView')->with('success_message', 'Ansar Unblocked successfully');
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

                    $status = "Panneled";
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

                    $status = "Embodded";
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

                case "Panneled":
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

                case "Embodded":
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
        return Redirect::action('BlockBlackController@blackListEntryView')->with('success_message', 'Ansar Blacked successfully');
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
        return Redirect::action('DGController@unblackListEntryView')->with('success_message', 'Ansar removed from Blacklist successfully');
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
        $ansar_id = $request->input('ansar_id');
        $cancel_panel_date = $request->input('cancel_panel_date');
        $modified_cancel_panel_date = Carbon::parse($cancel_panel_date)->format('Y-m-d');
        $cancel_panel_comment = $request->input('cancel_panel_comment');

        DB::beginTransaction();
        try {
            $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();

            $rest_info_entry = new RestInfoModel();
            $rest_info_entry->ansar_id = $ansar_id;
//            $rest_info_entry->memorandum_id = $panel_info->memorandum_id;
            $rest_info_entry->rest_date = $modified_cancel_panel_date;
            $rest_info_entry->disembodiment_reason_id = 8;
            $rest_info_entry->rest_form = "Panel";
            $rest_info_entry->comment = $cancel_panel_comment;
            $rest_info_entry->action_user_id = Auth::user()->id;
            $rest_info_entry->save();

            $panel_log_entry = new PanelInfoLogModel();
            $panel_log_entry->panel_id_old = $panel_info->id;
            $panel_log_entry->ansar_id = $ansar_id;
            $panel_log_entry->merit_list = $panel_info->ansar_merit_list;
            $panel_log_entry->panel_date = $panel_info->panel_date;
            $panel_log_entry->old_memorandum_id = $panel_info->memorandum_id;
            $panel_log_entry->movement_date = Carbon::today()->addHour(6);
            $panel_log_entry->come_from = "Cancel by DG";
            $panel_log_entry->move_to = "Rest";
            $panel_log_entry->direct_status = 1;
            $panel_log_entry->comment = $cancel_panel_comment;
            $panel_log_entry->action_user_id = Auth::user()->id;
            $panel_log_entry->save();

            $panel_info->delete();
            AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 1, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//            Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'CANCEL PANEL','from_state'=>'PANELED','to_state'=>'REST','action_by'=>auth()->user()->id]));
//            Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'CANCEL PANEL','from_state'=>'PANELED','to_state'=>'REST','action_by'=>auth()->user()->id]));
            //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'CANCEL PANEL', 'from_state' => 'PANELED', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
            CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'CANCEL PANEL', 'from_state' => 'PANELED', 'to_state' => 'REST']);
            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return Redirect::action('DGController@directCancelPanelView')->with('success_message', 'Ansar Canceled from Panel successfully');
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
            ->select('tbl_ansar_status_info.free_status', 'tbl_ansar_status_info.pannel_status', 'tbl_ansar_status_info.offer_sms_status', 'tbl_ansar_status_info.offered_status',
                'tbl_ansar_status_info.embodied_status', 'tbl_ansar_status_info.freezing_status', 'tbl_ansar_status_info.rest_status',
                'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.verified', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng')
            ->first();

        if ($ansar_details->verified == 0) {
            $status = "Entry";

        } elseif ($ansar_details->verified == 1) {
            $status = "Entry";

        } else {

            if ($ansar_details->free_status == 1) {
                $status = "Free";

            } elseif ($ansar_details->pannel_status == 1) {
                $status = "Panelled";

            } elseif ($ansar_details->offer_sms_status == 1) {
                $status = "Offered";

            } /*elseif ($ansar_details->offered_status == 1) {
                $status = "Offered";

            } */elseif ($ansar_details->embodied_status == 1) {
                $status = "Embodded";

            } elseif ($ansar_details->freezing_status) {
                $status = "Freeze";

//        } elseif ($ansar_details->block_list_status == 1) {
//            $status = "BlockListed";
//
//        } elseif ($ansar_details->black_list_status == 1) {
//            $status = "Blaclisted";

            } elseif ($ansar_details->rest_status == 1) {
                $status = "Rest";
            }
        }
        return Response::json(array('ansar_details' => $ansar_details, 'status' => $status));
    }

    public function directPanelEntry(Request $request)
    {
        $ansar_id = $request->input('ansar_id');
        $ansar_status = $request->input('ansar_status');
        $memorandum_id = $request->input('memorandum_id');
        $direct_panel_date = $request->input('direct_panel_date');
        $modified_direct_panel_date = Carbon::parse($direct_panel_date)->format('Y-m-d');
        $direct_panel_comment = $request->input('direct_panel_comment');

        DB::beginTransaction();
        try {
            $memorandum_id_save = new MemorandumModel();
            $memorandum_id_save->memorandum_id = $memorandum_id;
            $memorandum_id_save->save();

            switch ($ansar_status) {
                case "Free":
                    $panel_entry = new PanelModel();
                    $panel_entry->ansar_id = $ansar_id;
                    $panel_entry->panel_date = $modified_direct_panel_date;
                    $panel_entry->memorandum_id = $memorandum_id;
                    $panel_entry->come_from = "Direct";
                    $panel_entry->action_user_id = Auth::user()->id;
                    $panel_entry->save();

                    $ansar_status_update = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
                    $ansar_status_update->free_status = 0;
                    $ansar_status_update->pannel_status = 1;
                    $ansar_status_update->save();

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'PANEL','from_state'=>'FREE','to_state'=>'PANELED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'PANEL','from_state'=>'FREE','to_state'=>'PANELED','action_by'=>auth()->user()->id]));
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'PANEL', 'from_state' => 'FREE', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'PANELED', 'from_state' => 'FREE', 'to_state' => 'PANELED']);
                    break;

                case "Offered":
                    $panel_entry = new PanelModel();
                    $panel_entry->ansar_id = $ansar_id;
                    $panel_entry->panel_date = $modified_direct_panel_date;
                    $panel_entry->memorandum_id = $memorandum_id;
                    $panel_entry->come_from = "Direct";
                    $panel_entry->action_user_id = Auth::user()->id;
                    $panel_entry->save();

                    $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                    $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();
                    $mobile_no = DB::table('tbl_ansar_parsonal_info')->where('ansar_id', $ansar_id)->select('tbl_ansar_parsonal_info.mobile_no_self')->first();

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
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'PANEL','from_state'=>'OFFER','to_state'=>'PANELED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'PANEL','from_state'=>'OPFFER','to_state'=>'PANELED','action_by'=>auth()->user()->id]));
                    //CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'PANEL', 'from_state' => 'OFFER', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'PANEL', 'from_state' => 'OFFER', 'to_state' => 'PANELED']);

                    break;
//                case "Offered SMS":
//                    break;
                case "Rest":
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
                    $rest_log_save->direct_status = 1;
                    $rest_log_save->comment = $direct_panel_comment;
                    $rest_log_save->move_to = "Panel";
                    $rest_log_save->move_date = $direct_panel_date;
                    $rest_log_save->action_user_id = Auth::user()->id;
                    $rest_log_save->save();

                    $panel_entry = new PanelModel();
                    $panel_entry->ansar_id = $ansar_id;
                    $panel_entry->panel_date = $modified_direct_panel_date;
                    $panel_entry->memorandum_id = $memorandum_id;
                    $panel_entry->come_from = "Direct";
                    $panel_entry->action_user_id = Auth::user()->id;
                    $panel_entry->save();

//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'PANEL','from_state'=>'REST','to_state'=>'PANELED','action_by'=>auth()->user()->id]));
//                    Event::fire(new DGActionEvent(['ansar_id'=>$ansar_id,'action_type'=>'PANEL','from_state'=>'REST','to_state'=>'PANELED','action_by'=>auth()->user()->id]));
                    // CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'PANEL', 'from_state' => 'REST', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    CustomQuery::addDGlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'PANEL', 'from_state' => 'REST', 'to_state' => 'PANELED']);

                    ///Start from here
                    break;
            }
            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Redirect::action('DGController@directPanelView')->with('success_message', 'Ansar Added in the Panel successfully');
    }
}
