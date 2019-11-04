<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Facades\GlobalParameterFacades;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\BlockStatusSms;
use App\modules\HRM\Models\AnsarFutureState;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\BlackListInfoModel;
use App\modules\HRM\Models\BlackListModel;
use App\modules\HRM\Models\BlockListModel;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\FreezedAnsarEmbodimentDetail;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class BlockBlackController extends Controller
{
    public function blockListEntryView()
    {
        return view('HRM::Blackblock_view.blocklist_entry');
    }

    public function loadAnsarDetailforBlock(Request $request)
    {
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
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
            ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
            ->first();
//        if ($ansar_check->verified == 0 || $ansar_check->verified == 1) {
//            $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                    'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                ->first();
//
//            $status = "Entry";
//        }
//        else {
//            if ($ansar_check->free_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Free";
//
//            }
//            elseif ($ansar_check->pannel_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_panel_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_panel_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Paneled";
//
//            } elseif ($ansar_check->offer_sms_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_sms_offer_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Offer";
//
//            } elseif ($ansar_check->embodied_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_embodiment.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Embodied";
//
//            } elseif ($ansar_check->rest_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_rest_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Rest";
//            }
//        }

        return Response::json(array('ansar_details' => $ansar_details, 'status' => $status[0]));
    }

    public function blockListEntry(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/',
            'block_date' => 'required',
            'block_comment' => 'required',
        ];
        $this->validate($request, $rules);
        $ansar_status = $request->input('ansar_status');
        $ansar_id = $request->input('ansar_id');
        $block_date = $request->input('block_date');
        $modified_block_date = Carbon::parse($block_date)->format('Y-m-d');
        $block_comment = $request->input('block_comment');
        $from_id = $request->input('from_id');
//        return $request->all();
        DB::beginTransaction();
        try {
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            if (!$ansar) throw new\Exception('This Ansar doesn`t exists');
            $ansar_block_details = [
                'ansar_id' => $ansar_id,
                'block_list_from' => $ansar->getStatus()[0] == "Embodied" ? "Embodiment" : $ansar->getStatus()[0],
                'from_id' => $from_id,
                'date_for_block' => $modified_block_date,
                'comment_for_block' => $block_comment,
                'action_user_id' => Auth::user()->id,
            ];
            if (Carbon::parse($block_date)->lte(Carbon::now())) {
                BlockListModel::create($ansar_block_details);
                switch ($ansar->getStatus()[0]) {
                    case AnsarStatusInfo::NOT_VERIFIED_STATUS:
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'ENTRY', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::FREE_STATUS:
                        $ansar->update(['block_list_status' => 1, 'free_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::PANEL_STATUS:
                        $ansar->ansar->panel->saveLog("Blocklist", $modified_block_date, $block_comment);
                        $ansar->ansar->panel->delete();
                        $ansar->update(['block_list_status' => 1, 'pannel_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::OFFER_STATUS:
                        $offer = $ansar->ansar->offer_sms_info;
                        if (!$offer) {
                            $offer = $ansar->ansar->receiveSMS;
                            $offer->saveLog();
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        } else {
                            $offer->saveLog("No Reply");
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        }
                        $ansar->update(['block_list_status' => 1, 'offer_sms_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::EMBODIMENT_STATUS:
                        $ansar->ansar->embodiment->saveLog("Blocklist", $modified_block_date, 8);
                        $ansar->ansar->embodiment->delete();
                        $ansar->update(['block_list_status' => 1, 'embodied_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::REST_STATUS:
                        $ansar->ansar->rest->saveLog("Blocklist", $modified_block_date, $block_comment);
                        $ansar->ansar->rest->delete();
                        $ansar->update(['block_list_status' => 1, 'rest_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::OFFER_BLOCK_STATUS:
                        $offer_blocked = OfferBlockedAnsar::where('ansar_id', $ansar->ansar_id)->first();
                        $offer_blocked->delete();
                        $ansar->update(['block_list_status' => 1, 'offer_block_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER BLOCK', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    default:
                        throw new \Exception('This Ansar can`t be blocked.Because he is BLACKED');
                        break;

                }
                $this->dispatch(new BlockStatusSms($ansar_id, $block_comment));
            } else {
                $ansar_future_state = [
                    'ansar_id' => $request->ansar_id,
                    'data' => serialize($ansar_block_details),
                    'action_date' => Carbon::now()->format("y-m-d H:i:s"),
                    'activation_date' => Carbon::parse($block_date)->format("Y-m-d"),
                    'action_by' => Auth::user()->id,
                    'from_status' => 'Embodiment',
                    'to_status' => 'Block',
                ];
                switch ($ansar->getStatus()[0]) {

                    case AnsarStatusInfo::NOT_VERIFIED_STATUS:
                        $ansar_future_state['from_status'] = 'Unverified';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'ENTRY', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::FREE_STATUS:
                        $ansar_future_state['from_status'] = 'Free';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::PANEL_STATUS:
                        $ansar_future_state['from_status'] = 'Panel';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::OFFER_STATUS:
                        $offer = $ansar->ansar->offer_sms_info;
                        if (!$offer) {
                            $offer = $ansar->ansar->receiveSMS;
                            $offer->saveLog();
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        } else {
                            $offer->saveLog("No Reply");
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        }
                        $ansar->update(['block_list_status' => 1, 'offer_sms_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::EMBODIMENT_STATUS:
                        $ansar_future_state['from_status'] = 'Embodiment';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::REST_STATUS:
                        $ansar_future_state['from_status'] = 'Rest';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::OFFER_BLOCK_STATUS:
                        $ansar_future_state['from_status'] = 'Offer_blocked';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER BLOCK', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    default:
                        throw new \Exception('This Ansar can`t be blocked.Because he is BLACKED');
                        break;

                }
                AnsarFutureState::create($ansar_future_state);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return Response::json(['status' => false, 'message' => $e->getMessage()]);
            }
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        if ($request->ajax()) {
            return Response::json(['status' => true, 'message' => 'Ansar Id: ' . $ansar_id . " successfully blocked"]);
        }
        return Redirect::route('blocklist_entry_view')->with('success_message', 'Ansar Id:' . $ansar_id . ' successfully blocked');
    }

    public function arrayBlockListEntry(Request $request)
    {
//        return $request->all();
        $ansar = $request->input('ansar');
        $block_date = $request->input('block_date');
        $modified_block_date = Carbon::parse($block_date)->format('Y-m-d');
        $block_comment = $request->input('block_comment');
        $from_id = $request->input('from_id');

        DB::beginTransaction();
        foreach ($ansar as $a) {
            //return $a;
            $ansar_id = $a['ansar_id'];
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            try {
                switch ($a['status']) {

                    case "Entry":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Entry";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'ENTRY','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'ENTRY', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Free":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Free";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $ansar->update(['block_list_status' => 1, 'free_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'FREE','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
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
                        $ansar->ansar->panel->saveLog("Block", $modified_block_date, $block_comment);
                        $ansar->ansar->panel->delete();
                        $ansar->update(['block_list_status' => 1, 'pannel_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'PANEL','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Offer":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Offer";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $offer = $ansar->ansar->offer_sms_info;
                        if (!$offer) {
                            $offer = $ansar->ansar->receiveSMS;
                            $offer->saveLog();
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        } else {
                            $offer->saveLog("No Reply");
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        }
                        $ansar->update(['block_list_status' => 1, 'offer_sms_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'OFFER','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Embodied":
                        Log::info("OK BLOCK EMBODIED");
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Embodiment";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $ansar->ansar->embodiment->saveLog("Blocklist", $modified_block_date, 8);
                        $ansar->ansar->embodiment->delete();
                        $ansar->update(['block_list_status' => 1, 'embodied_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'EMBODIED','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Rest":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Rest";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $ansar->ansar->rest->saveLog("Blocklist", $modified_block_date, $block_comment);
                        $ansar->ansar->rest->delete();
                        $ansar->update(['block_list_status' => 1, 'rest_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'REST','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    default:
                        if ($request->ajax()) {
                            return Response::json(['status' => false, 'message' => 'Invalid Request']);
                        }

                }

            } catch (\Exception $e) {
                DB::rollBack();
                return Response::json(['status' => false, 'message' => $e->getMessage()]);
            }
        }
        DB::commit();
        if ($request->ajax()) {
            return Response::json(['status' => true, 'message' => "Ansars successfully blocked"]);
        }
        return Response::json(['status' => false, 'message' => 'Invalid Request']);
    }

    public function unblockListEntryView()
    {
        return view('HRM::Blackblock_view.unblocklist_entry');
    }

    public function loadAnsarDetailforUnblock(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'ansar_id' => 'required|regex:/^[0-9]+$/'
        ]);
        if ($valid->fails()) {
            return [];
        }
        $ansar_id = Input::get('ansar_id');

        $ansar_details = DB::table('tbl_blocklist_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blocklist_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_blocklist_info.ansar_id', '=', $ansar_id)
            ->where('tbl_blocklist_info.date_for_unblock', '=', null)
            ->where('tbl_ansar_status_info.block_list_status', '=', 1)->orderBy('tbl_blocklist_info.id', 'desc')
            ->select('tbl_blocklist_info.block_list_from', 'tbl_blocklist_info.date_for_block', 'tbl_blocklist_info.comment_for_block', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                'tbl_units.unit_name_eng', 'tbl_designations.name_eng')->first();

        return Response::json($ansar_details);
    }

    public function unblockListEntry(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:hrm.tbl_blocklist_info,ansar_id',
            'unblock_date' => 'required',
            'move_status' => 'required'
        ];
        $this->validate($request, $rules);
        $ansar_id = $request->input('ansar_id');
        $unblock_date = $request->input('unblock_date');
        $moveStatus = $request->input('move_status');
        if (!empty($request->input('memo_id'))) $memorandumId = $request->input('memo_id');
        else $memorandumId = 'N/A';
        $modified_unblock_date = Carbon::parse($unblock_date)->format('Y-m-d');
        $unblock_comment = $request->input('unblock_comment');
        DB::beginTransaction();
        try {
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            if (empty($ansar) || !in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->getStatus())) {
                throw new \Exception("This ansar is not in block list");
            }
            $ansar_unblock_details = [
                'date_for_unblock' => $modified_unblock_date,
                'comment_for_unblock' => $unblock_comment
            ];
            if (Carbon::parse($unblock_date)->lte(Carbon::now())) {
                $this->removeOtherStatusExceptBlock($ansar);
                $blocklist_entry = BlockListModel::where('ansar_id', $ansar_id)->orderBy('id', 'desc')->first();
                $blocklist_entry->update($ansar_unblock_details);
                $blocklist_entry->save();
                switch (strtolower($moveStatus)) {
                    case "free":
                        $ansar->updateToFreeState()->save();
                        break;
                    case "rest":
                        RestInfoModel::create([
                            'ansar_id' => $ansar_id,
                            'old_embodiment_id' => 0,
                            'memorandum_id' => $memorandumId,
                            'rest_date' => Carbon::now()->format("Y-m-d"),
                            'active_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                            'disembodiment_reason_id' => 8,
                            'total_service_days' => 0,
                            'rest_form' => 'Block',
                            'comment' => 'After unblock move to rest status',
                            'action_user_id' => auth()->user()->id
                        ]);
                        $ansar->updateToRestState()->save();
                        break;
                    case "panel":
                        PanelModel::create([
                            'ansar_id' => $ansar_id,
                            'come_from' => 'Block',
                            'panel_date' => Carbon::now(),
                            're_panel_date' => Carbon::now(),
                            'memorandum_id' => $memorandumId,
                            'ansar_merit_list' => 'N\A',
                            'action_user_id' => auth()->user()->id,
                        ]);
                        $ansar->updateToPanelState()->save();
                        break;
                    case "not_verified":
                        $ansar->ansar->update(['verified' => 0]);
                        break;
                }
            } else {
                $futureData = [
                    'ansar_id' => $ansar_id,
                    'data' => serialize($ansar_unblock_details),
                    'action_date' => Carbon::now()->format("y-m-d H:i:s"),
                    'activation_date' => $modified_unblock_date,
                    'action_by' => Auth::user()->id,
                    'from_status' => 'Block'
                ];
                switch (strtolower($moveStatus)) {
                    case "free":
                        $futureData["to_status"] = "Free";
                        break;
                    case "rest":
                        $futureData["to_status"] = "Rest";
                        break;
                    case "panel":
                        $futureData["to_status"] = "Panel";
                        break;
                    case "not_verified":
                        $futureData["to_status"] = "Unverified";
                        break;
                    default:
                        $futureData["to_status"] = "Unverified";
                        break;
                }
                AnsarFutureState::create($futureData);
            }
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'UNVERIFIED', 'action_by' => auth()->user()->id]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::route('unblocklist_entry_view')->with('success_message', 'Ansar Removed from Blocklist Successfully');
    }

    public function blackListEntryView()
    {
        return view('HRM::Blackblock_view.blacklist_entry');
    }

    public function loadAnsarDetailforBlack(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'ansar_id' => 'required|regex:/^[0-9]+$/'
        ]);
        if ($valid->fails()) {
            return [];
        }
        try {
            $ansar_id = Input::get('ansar_id');

            $ansar_check = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            $ansar_details = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
                    'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                ->first();
            $r = array('ansar_details' => $ansar_details, 'status' => $ansar_check->getStatus()[0]);
            return Response::json($r);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function blackListEntry(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|unique:tbl_blacklist_info,ansar_id',
            'black_date' => 'required',
            'black_comment' => 'required',
        ];
        $this->validate($request, $rules);
        $ansar_status = $request->input('ansar_status');
        $ansar_id = $request->input('ansar_id');
        $black_date = $request->input('black_date');
        $modified_black_date = Carbon::parse($black_date)->format('Y-m-d');
        $black_comment = $request->input('black_comment');
        $from_id = $request->input('from_id');
        $mobile_no = DB::table('tbl_ansar_parsonal_info')->where('ansar_id', $ansar_id)->select('tbl_ansar_parsonal_info.mobile_no_self')->first();

        DB::beginTransaction();
//        return $ansar_status->getStatus();
        try {
            $ansar_status = AnsarStatusInfo::where('ansar_id', $request->ansar_id)->first();
            if (!$ansar_status) throw new \Exception("This is Ansar doesn`t exists");
            BlackListModel::create([
                'ansar_id' => $request->ansar_id,
                'black_list_from' => $ansar_status->getStatus()[0],
                'from_id' => 0,
                'black_listed_date' => $modified_black_date,
                'black_list_comment' => $black_comment,
                'action_user_id' => Auth::user()->id,
            ]);
            switch ($ansar_status->getStatus()[0]) {

                case AnsarStatusInfo::NOT_VERIFIED_STATUS:
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'ENTRY', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::FREE_STATUS:
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::PANEL_STATUS:
                    $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                    $panel_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                    $panel_info->delete();
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'PANEL', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::OFFER_STATUS:
                    $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                    $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

                    if (!is_null($sms_offer_info)) {

                        $sms_offer_info->saveLog('No Reply');
                        $sms_offer_info->delete();
                        $sms_offer_info->deleteCount();
                        $sms_offer_info->deleteOfferStatus();

                    } elseif (!is_null($sms_receive_info)) {
                        $sms_receive_info->saveLog();
                        $sms_receive_info->delete();
                        $sms_receive_info->deleteCount();
                        $sms_receive_info->deleteOfferStatus();
                    }

                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'OFFER', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::EMBODIMENT_STATUS:
                    $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                    $embodiment_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                    $embodiment_info->delete();
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::REST_STATUS:
                    $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                    $rest_info->saveLog('Blacklist', $modified_black_date);
                    $rest_info->delete();

                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'REST', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::FREEZE_STATUS:
                    $freeze_info = FreezingInfoModel::where('ansar_id', $ansar_id)->first();
                    $freeze_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                    $freeze_info->delete();
                    $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                    if (!$embodiment_info) $embodiment_info = FreezedAnsarEmbodimentDetail::where('ansar_id', $ansar_id)->first();
                    $embodiment_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                    $embodiment_info->delete();
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREEZE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::BLOCK_STATUS:
                    $blocklist_entry = BlockListModel::where('ansar_id', $ansar_id)->first();
                    $blocklist_entry->update([
                        'date_for_unblock' => $modified_black_date,
                        'comment_for_unblock' => $black_comment
                    ]);
                    if (!isset($ansar_status->getStatus()[1])) {
                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);

                    } else {

                        switch ($ansar_status->getStatus()[1]) {
                            case AnsarStatusInfo::FREE_STATUS:
                                $ansar_status->update(['free_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::PANEL_STATUS:
                                $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                                $panel_info->saveLog('Blacklist', $modified_black_date);
                                $panel_info->delete();
                                $ansar_status->update(['pannel_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::EMBODIMENT_STATUS:
                                $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                                $embodiment_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                                $embodiment_info->delete();
                                $ansar_status->update(['embodied_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::REST_STATUS:
                                $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                                $rest_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                                $rest_info->delete();
                                $ansar_status->update(['rest_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::FREEZE_STATUS:
                                $freeze_info = FreezingInfoModel::where('ansar_id', $ansar_id)->first();
                                $freeze_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                                $freeze_info->delete();
                                $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                                $embodiment_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                                $embodiment_info->delete();
                                $ansar_status->update(['freezing_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::OFFER_STATUS:
                                $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                                $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

                                if (!is_null($sms_offer_info)) {

                                    $sms_offer_info->saveLog('No Reply');
                                    $sms_offer_info->delete();

                                } elseif (!is_null($sms_receive_info)) {
                                    $sms_receive_info->saveLog();
                                    $sms_receive_info->delete();
                                }
                                $ansar_status->update(['offer_sms_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;


                        }
                    }
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'BLOCKED', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    break;
                default :
                    throw new \Exception("This Ansar already in black list");
                    break;

            }
            //return $ansar_status->getStatus()[0];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::route('blacklist_entry_view')->with('success_message', 'Ansar Blacklisted Successfully');
    }

    public function unblackListEntryView()
    {
        return view('HRM::Blackblock_view.unblacklist_entry');
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
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:tbl_blacklist_info,ansar_id',
            'unblack_date' => 'required'
        ];
        $this->validate($request, $rules);
        $ansar_id = $request->input('ansar_id');
        $unblack_date = $request->input('unblack_date');
        $modified_unblack_date = Carbon::parse($unblack_date)->format('Y-m-d');
        $unblack_comment = $request->input('unblack_comment');

        DB::beginTransaction();
        try {
            $blacklist_info = BlackListModel::where('ansar_id', $ansar_id)->first();
            $ansar_unblack_detail = [
                'old_blacklist_id' => $blacklist_info->id,
                'ansar_id' => $ansar_id,
                'black_list_from' => $blacklist_info->black_list_from,
                'from_id' => $blacklist_info->from_id,
                'black_listed_date' => $blacklist_info->black_listed_date,
                'black_list_comment' => $blacklist_info->black_list_comment,
                'unblacklist_date' => $modified_unblack_date,
                'unblacklist_comment' => $unblack_comment,
                'move_to' => "Free",
                'move_date' => $modified_unblack_date,
                'action_user_id' => Auth::user()->id,
            ];
            if (Carbon::parse($unblack_date)->lte(Carbon::now())) {

                BlackListInfoModel::create($ansar_unblack_detail);

                $blacklist_info->delete();
                AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 1, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
            } else {
                AnsarFutureState::create([
                    'ansar_id' => $ansar_id,
                    'data' => serialize($ansar_unblack_detail),
                    'action_date' => Carbon::now()->format("y-m-d H:i:s"),
                    'activation_date' => $modified_unblack_date,
                    'action_by' => Auth::user()->id,
                    'from_status' => 'Black',
                    'to_status' => 'Free'
                ]);
            }


//            Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'FREE','from_state'=>'BLACKED','to_state'=>'FREE','action_by'=>auth()->user()->id]));
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLACKED', 'from_state' => 'BLACKED', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);


            DB::commit();
        } catch (\Exception $e) {
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::back()->with('success_message', 'Ansar removed from Blacklist Successfully');
    }

    /**
     * Remove Double Status
     * Type: AnsarStatusInfo
     * @param null $ansar
     */
    private function removeOtherStatusExceptBlock($ansar = null)
    {
        if (!empty($ansar) && in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->getStatus())) {
            if (in_array(AnsarStatusInfo::PANEL_STATUS, $ansar->getStatus())) {
                $ansar->panel->saveLog("Blocklist", Carbon::now()->format('Y-m-d'), '44.03.0000.048.50.007.18-577 Date:Oct-27-2019');
                $ansar->panel->delete();
            } elseif (in_array(AnsarStatusInfo::EMBODIMENT_STATUS, $ansar->getStatus())) {
                $ansar->embodiment->saveLog('Blocklist', Carbon::now()->format('Y-m-d'), '44.03.0000.048.50.007.18-577 Date:Oct-27-2019', 8);
                $ansar->embodiment->delete();
            } elseif (in_array(AnsarStatusInfo::REST_STATUS, $ansar->getStatus())) {
                $ansar->rest->saveLog('Blocklist', Carbon::now()->format('Y-m-d'), '44.03.0000.048.50.007.18-577 Date:Oct-27-2019');
                $ansar->rest->delete();
            }
        }
    }
}
