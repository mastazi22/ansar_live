<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\BlackListModel;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\FreezingInfoLog;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\Login;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\TransferAnsar;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Mockery\Exception;

class FreezeController extends Controller
{
    //  Do the freeze by id for law breaking 
    //view
    public function freezeView()
    {
        return View::make('HRM::Freeze.freeze_view');
    }

    //submit
    public function loadAnsarDetailforFreeze(){
        $ansar_id=Input::get('ansar_id');
        $ansar_details = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0)
            ->select('tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date','tbl_ansar_parsonal_info.ansar_name_eng as name',
                'tbl_ansar_parsonal_info.data_of_birth as dob', 'tbl_ansar_parsonal_info.sex','tbl_kpi_info.id', 'tbl_kpi_info.kpi_name as kpi', 'tbl_designations.name_eng as rank',
                'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana','tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date','tbl_kpi_info.withdraw_status')
            ->first();
        return Response::json($ansar_details);
    }
    public function freezeEntry(Request $request){
        $ansar_id = $request->input('ansar_id');
        $freeze_date = $request->input('freeze_date');
        $freeze_comment = $request->input('freeze_comment');
        $memorandum_id=$request->input('memorandum_id');
        $modifed_freeze_date=Carbon::parse($freeze_date)->format('Y-m-d');
        DB::beginTransaction();
        try {
            $memorandum_info=new MemorandumModel();
            $memorandum_info->memorandum_id=$memorandum_id;

            $embodiment_info=EmbodimentModel::where('ansar_id', $ansar_id)->first();

            $freeze_info = new FreezingInfoModel();
            $freeze_info->ansar_id=$ansar_id;
            $freeze_info->freez_reason="Disciplinary Actions";
            $freeze_info->freez_date=$modifed_freeze_date;
            $freeze_info->comment_on_freez=$freeze_comment;
            $freeze_info->memorandum_id=$memorandum_id;
            $freeze_info->kpi_id=$embodiment_info->kpi_id;
            $freeze_info->ansar_embodiment_id=$embodiment_info->id;
            $freeze_info->action_user_id=Auth::user()->id;
            $freeze_info->save();

            $embodiment_info->emboded_status= "freeze";
            $embodiment_info->save();

            AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 1]);
            CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'FREEZE', 'from_state' => 'EMBODIED', 'to_state' => 'FREEZE', 'action_by' => auth()->user()->id]);

            DB::commit();
        } catch (Exception $e) {
            return Redirect::to('dofreeze')->with('error_message', 'Problem in inserting');
        }
        return Redirect::action('FreezeController@freezeView')->with('success_message', 'Ansar Freezed for Disciplinary Action Successfully!');

    }

//    show freeze list
    public function freezeList()
    {
        return View::make('HRM::Freeze.freezelist');
    }

    public function getfreezelist()
    {
        return response()->json(CustomQuery::getFreezeList());
    }

    public function freezeRembodied($ansarid)
    {
        $date = Carbon::now();

        $freezeLog = new FreezingInfoLog();
        $frezeInfo = FreezingInfoModel::where('ansar_id', $ansarid)->first();
        $date_differ = $date->diffInDays(Carbon::parse($frezeInfo->freez_date),true);
        $freezeLog->old_freez_id = $frezeInfo->id;
        $freezeLog->ansar_id = $ansarid;
        $freezeLog->ansar_embodiment_id = $frezeInfo->ansar_embodiment_id;
        $freezeLog->freez_reason = $frezeInfo->freez_reason;
        $freezeLog->freez_date = $frezeInfo->freez_date;
        $freezeLog->comment_on_freez = $frezeInfo->comment_on_freez;
        $freezeLog->move_frm_freez_date = $date;
        $freezeLog->move_to = 'Emodiment';
        $freezeLog->comment_on_move = $frezeInfo->comment_on_freez;

        DB::beginTransaction();
        try {
            $frezeDelete = FreezingInfoModel::where('ansar_id', $ansarid)->delete();
            $freezeLogSave = $freezeLog->save();
            $updateEmbodiment = EmbodimentModel::where('ansar_id', $ansarid)->first();
            $updateEmbodiment->service_ended_date = Carbon::parse($updateEmbodiment->service_ended_date)->addDays($date_differ);
            $updateEmbodiment->emboded_status = 'Emboded';
            $updateStatus = AnsarStatusInfo::where('ansar_id', $ansarid)->update(['freezing_status' => 0, 'embodied_status' => 1]);
            if ($frezeDelete && $freezeLogSave && $updateEmbodiment->save() && $updateStatus) {
                DB::commit();
                CustomQuery::addActionlog(['ansar_id' => $ansarid, 'action_type' => 'EMBODIED', 'from_state' => 'FREEZE', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
                return 'Re-embodied successfully';
            }
            throw new Exception();
        } catch (Exception $rollback) {
            DB::rollback();
            return 'Could not Re-embodied';
        }
    }

    public function freezeDisEmbodied($ansarid)
    {
        $freezeLog = new FreezingInfoLog();
        $frezeInfo = FreezingInfoModel::where('ansar_id', $ansarid)->first();
        $embodiment = EmbodimentModel::where('ansar_id', $ansarid)->first();
        $embodimentLog = new EmbodimentLogModel();
        $restInfo = new RestInfoModel();

        $freezeLog->old_freez_id = $frezeInfo->id;
        $freezeLog->ansar_id = $ansarid;
        $freezeLog->ansar_embodiment_id = $embodiment->id;
        $freezeLog->freez_reason = $frezeInfo->freez_reason;
        $freezeLog->freez_date = $frezeInfo->freez_date;
        $freezeLog->comment_on_freez = $frezeInfo->comment_on_freez;
        $freezeLog->move_frm_freez_date = Input::get('rest_date');
        $freezeLog->move_to = 'Rest';
        if (Input::get('comment')) {
            $freezeLog->comment_on_move = Input::get('comment');
        } else {
            $freezeLog->comment_on_move = 'No Comment';
        }

        $embodimentLog->old_embodiment_id = $embodiment->id;
        $embodimentLog->old_memorandum_id = $embodiment->memorandum_id;
        $embodimentLog->ansar_id = $ansarid;
        $embodimentLog->kpi_id = $embodiment->kpi_id;
        $embodimentLog->reporting_date = $embodiment->reporting_date;
        $embodimentLog->joining_date = $embodiment->joining_date;
        $embodimentLog->release_date = Input::get('rest_date');
        $embodimentLog->disembodiment_reason_id = Input::get('reason');
        $embodimentLog->move_to = 'Rest';
        $embodimentLog->action_user_id = Auth::id();

        $restInfo->ansar_id = $ansarid;
        $restInfo->old_embodiment_id = $embodiment->id;
        $restInfo->memorandum_id = Input::get('memorandum');
        $restInfo->rest_date = Input::get('rest_date');
        $restInfo->active_date = Carbon::parse(Input::get('rest_date'))->addMinute(10);
        $restInfo->disembodiment_reason_id = Input::get('reason');
        $restInfo->total_service_days = Carbon::parse($embodiment->joining_date)->diffInDays(Carbon::parse(Input::get('rest_date')), true);
        $restInfo->rest_form = 'Freeze';
        if (Input::get('comment')) {
            $restInfo->comment = Input::get('comment');
        } else {
            $restInfo->comment = 'No Comment';
        }
        $restInfo->action_user_id = Auth::id();

        DB::beginTransaction();
        try {
            $frezeDelete = FreezingInfoModel::where('ansar_id', $ansarid)->delete();
            $freezeLogSave = $freezeLog->save();
            $embodimentDelete = EmbodimentModel::where('ansar_id', $ansarid)->delete();
            $embodimentLogSave = $embodimentLog->save();
            $restInfoSave = $restInfo->save();
            $updateStatus = AnsarStatusInfo::where('ansar_id', $ansarid)->update(['freezing_status' => 0, 'rest_status' => 1]);
            if ($frezeDelete && $freezeLogSave && $embodimentDelete && $embodimentLogSave && $restInfoSave && $updateStatus) {
                DB::commit();
                CustomQuery::addActionlog(['ansar_id' => $ansarid, 'action_type' => 'DISEMBODIMENT', 'from_state' => 'FREEZE', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
                return 'dis-embodied successfully';
            }
            throw new Exception();
        } catch (Exception $rollback) {
            DB::rollback();
            return 'Could not dis-embodied';
        }
    }

    public function transferFreezedAnsar(){
        $date = Carbon::now();
        $ansarid = Input::get('ansar_id');
        $freezeLog = new FreezingInfoLog();
        $frezeInfo = FreezingInfoModel::where('ansar_id', $ansarid)->first();
        $date_differ = $date->diffInDays(Carbon::parse($frezeInfo->freez_date),true);
        $freezeLog->old_freez_id = $frezeInfo->id;
        $freezeLog->ansar_id = $ansarid;
        $freezeLog->ansar_embodiment_id = $frezeInfo->ansar_embodiment_id;
        $freezeLog->freez_reason = $frezeInfo->freez_reason;
        $freezeLog->freez_date = $frezeInfo->freez_date;
        $freezeLog->comment_on_freez = $frezeInfo->comment_on_freez;
        $freezeLog->move_frm_freez_date = $date;
        $freezeLog->move_to = 'Emodiment';
        $freezeLog->comment_on_move = $frezeInfo->comment_on_freez;
        DB::beginTransaction();
        try {
            $frezeDelete = FreezingInfoModel::where('ansar_id', $ansarid)->delete();
            $freezeLogSave = $freezeLog->save();
            $updateEmbodiment = EmbodimentModel::where('ansar_id', $ansarid)->first();
            $transfer = new TransferAnsar;
            $transfer->ansar_id = $ansarid;
            $transfer->embodiment_id = $updateEmbodiment->id;
            $transfer->transfer_memorandum_id = Input::get('mem_id');
            $transfer->present_kpi_id = $updateEmbodiment->kpi_id;
            $transfer->transfered_kpi_id = Input::get('kpi_id');
            $transfer->present_kpi_join_date = $updateEmbodiment->transfered_date;
            $transfer->transfered_kpi_join_date = Carbon::createFromFormat("d-M-Y",Input::get('transfered_date'))->format("Y-m-d");
            $transfer->action_by = Auth::user()->id;
            $updateEmbodiment->service_ended_date = Carbon::parse($updateEmbodiment->service_ended_date)->addDays($date_differ);
            $updateEmbodiment->emboded_status = 'Emboded';
            $updateEmbodiment->transfered_date = Carbon::createFromFormat("d-M-Y",Input::get('transfered_date'))->format("Y-m-d");
            $updateEmbodiment->kpi_id = Input::get('kpi_id');
            $updateStatus = AnsarStatusInfo::where('ansar_id', $ansarid)->update(['freezing_status' => 0, 'embodied_status' => 1]);
            if ($frezeDelete && $freezeLogSave && $updateEmbodiment->save()&&$transfer->save() && $updateStatus) {
                DB::commit();
                CustomQuery::addActionlog(['ansar_id' => $ansarid, 'action_type' => 'EMBODIED', 'from_state' => 'FREEZE', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
                return 'Re-embodied successfully';
            }
            throw new Exception();
        } catch (Exception $rollback) {
            DB::rollback();
            return 'Could not Re-embodied';
        }
    }
    public function freezeBlack(Request $request, $ansarId)
    {
        $ansar_status = "Emboded";
        $ansar_id = $ansarId;
        $black_date = $request->input('black_date');
        $black_comment = $request->input('black_comment');

        DB::beginTransaction();
        try {
            $blacklist_entry = new BlackListModel();
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
            $embodiment_log_save->comment = $black_comment;
            $embodiment_log_save->action_user_id = Auth::user()->id;
            $successembodimentlog = $embodiment_log_save->save();

            $blacklist_entry->ansar_id = $ansar_id;
            $blacklist_entry->black_list_from = "Embodiment";
            $blacklist_entry->from_id = $embodiment_info->id;
            $blacklist_entry->black_listed_date = $black_date;
            $blacklist_entry->black_list_comment = $black_comment;
            $blacklist_entry->action_user_id = Auth::user()->id;
            $successblack = $blacklist_entry->save();
            $successembodiment = $embodiment_info->delete();

            $updateStatus = AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['freezing_status' => 0, 'black_list_status' => 1]);
            if ($successembodimentlog && $successblack && $successembodiment && $updateStatus) {
                DB::commit();
                CustomQuery::addActionlog(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'FREEZE','to_state'=>'BLACKED','action_by'=>auth()->user()->id]);
                return "Blacklisted Successfully!";
            }
        } catch (Exception $rollback) {
            DB::rollback();
            return 'Could not Black listed!';
        }
    }
}
