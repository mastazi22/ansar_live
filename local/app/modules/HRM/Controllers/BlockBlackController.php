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
use App\modules\HRM\Models\FreezingInfoLog;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoLogModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
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
          'ansar_id'=>'required|regex:/^[0-9]+$/'
        ];
        $vaild = Validator::make($request->all(),$rule);
        if($vaild->fails()){

        }
        $ansar_id = Input::get('ansar_id');

        $status = AnsarStatusInfo::where('ansar_id',$ansar_id)->first()->getStatus();
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
            'ansar_id'=>'required|regex:/^[0-9]+$/',
            'block_date'=>'required',
            'block_comment'=>'required',
        ];
        $this->validate($request,$rules);
        $ansar_status = $request->input('ansar_status');
        $ansar_id = $request->input('ansar_id');
        $block_date = $request->input('block_date');
        $modified_block_date = Carbon::parse($block_date)->format('Y-m-d');
        $block_comment = $request->input('block_comment');
        $from_id = $request->input('from_id');
//        return $request->all();
        DB::beginTransaction();
        try {
            $ansar = AnsarStatusInfo::where('ansar_id',$ansar_id)->first();
            switch ($ansar->getStatus()[0]) {

                case AnsarStatusInfo::NOT_VERIFIED_STATUS:
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

                case AnsarStatusInfo::FREE_STATUS:
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Free";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'FREE','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::PANEL_STATUS:
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Panel";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'PANEL','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::OFFER_STATUS:
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Offer";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'OFFER','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::EMBODIMENT_STATUS:
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Embodiment";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'EMBODIED','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::REST_STATUS:
                    $blocklist_entry = new BlockListModel();
                    $blocklist_entry->ansar_id = $ansar_id;
                    $blocklist_entry->block_list_from = "Rest";
                    $blocklist_entry->from_id = $from_id;
                    $blocklist_entry->date_for_block = $modified_block_date;
                    $blocklist_entry->comment_for_block = $block_comment;
                    $blocklist_entry->action_user_id = Auth::user()->id;
                    $blocklist_entry->save();
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'REST','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                    break;
                default:
                    throw new \Exception('This Ansar can`t be blocked.Because he is BLACKED');
                    break;

            }
            AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['block_list_status' => 1]);
            DB::commit();
        } catch (\Exception $e) {
            if($request->ajax()) {
                return Response::json(['status' => false, 'message' => $e->getMessage()]);
            }
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        if ($request->ajax()) {
            return Response::json(['status' => true,'message'=>'Ansar id '.$ansar_id." successfully blocked"]);
        }
        return Redirect::route('blocklist_entry_view')->with('success_message', 'Ansar Blocked Successfully');
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
        foreach($ansar as $a){
            //return $a;
            $ansar_id = $a['ansar_id'];
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
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'OFFER','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Embodied":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Embodiment";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
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
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'REST','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    default:
                        if ($request->ajax()) {
                            return Response::json(['status' => false,'message'=>'Invalid Request']);
                        }

                }
                AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['block_list_status' => 1]);
            }
            catch (\Exception $e) {
                DB::rollBack();
                return Response::json(['status' => false,'message'=>$e->getMessage()]);
            }
        }
        DB::commit();
        if ($request->ajax()) {
            return Response::json(['status' => true,'message'=>"Ansars successfully blocked"]);
        }
        return Response::json(['status' => false,'message'=>'Invalid Request']);
    }

    public function unblockListEntryView()
    {
        return view('HRM::Blackblock_view.unblocklist_entry');
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
            $blocklist_entry->save();
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            $ansar->block_list_status = 0;
            $ansar->save();
            switch (1) {
                case $ansar->free_status;
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);
                    break;
                case $ansar->pannel_status;
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'PANEL', 'action_by' => auth()->user()->id]);
                    break;
                case $ansar->offer_sms_status;
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'OFFER', 'action_by' => auth()->user()->id]);
                    break;
                case $ansar->embodied_status;
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
                    break;
                case $ansar->rest_status;
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
                    break;
                default:
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'ENTRY', 'action_by' => auth()->user()->id]);
                    break;
            }
            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return Redirect::route('unblocklist_entry_view')->with('success_message', 'Ansar Removed from Blocklist Successfully');
    }

    public function blackListEntryView()
    {
        return view('HRM::Blackblock_view.blacklist_entry');
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
                'tbl_ansar_status_info.embodied_status', 'tbl_ansar_status_info.rest_status', 'tbl_ansar_status_info.block_list_status', 'tbl_ansar_status_info.freezing_status', 'tbl_ansar_status_info.black_list_status', 'tbl_ansar_parsonal_info.verified')
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
        $rules = [
            'ansar_id'=>'required|regex:/^[0-9]+$/',
            'black_date'=>'required',
            'black_comment'=>'required',
        ];
        $this->validate($request,$rules);
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
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'ENTRY','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'ENTRY', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    break;

                case "Free":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Free";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'FREE','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case "Paneled":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Panel";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                    $panel_info->saveLog('Blacklist',$modified_black_date,$black_comment);
                    $panel_info->delete();
                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'PANEL','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'PANEL', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case "Offer":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Offer";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();


                    $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                    $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

                    if (!is_null($sms_offer_info)) {

                        $sms_offer_info->saveLog('No Reply');
                        $sms_offer_info->delete();

                    } elseif (!is_null($sms_receive_info)) {
                        $sms_receive_info->saveLog();
                        $sms_receive_info->delete();
                    }

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'OFFER','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'OFFER', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case "Embodied":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Embodiment";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                    $embodiment_info->saveLog('Blacklist',$modified_black_date,$black_comment);
                    $embodiment_info->delete();
                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'EMBODIED','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case "Rest":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Rest";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                    $rest_info->saveLog('Blacklist',$modified_black_date);
                    $rest_info->delete();

                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'REST','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'REST', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

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
                    $freeze_info->saveLog('Blacklist',$modified_black_date,$black_comment);
                    $freeze_info->delete();
                    $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                    $embodiment_info->saveLog('Blacklist',$modified_black_date,$black_comment);
                    $embodiment_info->delete();
                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'FREEZE','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREEZE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case "Blocklisted":
                    $blacklist_entry = new BlackListModel();
                    $blacklist_entry->ansar_id = $ansar_id;
                    $blacklist_entry->black_list_from = "Blocklist";
                    $blacklist_entry->from_id = $from_id;
                    $blacklist_entry->black_listed_date = $modified_black_date;
                    $blacklist_entry->black_list_comment = $black_comment;
                    $blacklist_entry->action_user_id = Auth::user()->id;
                    $blacklist_entry->save();

                    $block_info = BlockListModel::where('ansar_id', $ansar_id)->first();

                    if ($block_info->block_list_from == "Entry") {
                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    } elseif ($block_info->block_list_from == "Free") {
                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);

                    } elseif ($block_info->block_list_from == "Panel") {

                        $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                        $panel_info->saveLog('Blacklist',$modified_black_date);
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
                        $embodiment_log_save->transfered_date = $embodiment_info->transfered_date;
                        $embodiment_log_save->release_date = $modified_black_date;
                        $embodiment_log_save->move_to = "Blacklist";
                        $embodiment_log_save->service_extension_status = $embodiment_info->service_extension_status;
                        $embodiment_log_save->comment = $black_comment;
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
                            $sms_log_save->sms_offer_id = $sms_receive_info->id;
                            $sms_log_save->mobile_no = $mobile_no->mobile_no_self;

                            //$sms_log_save->offer_status=;
                            $sms_log_save->reply_type = "No Reply";
                            $sms_log_save->offered_district = $sms_offer_info->district_id;
                            $sms_log_save->offered_date = $sms_offer_info->sms_send_datetime;
                            $sms_log_save->action_user_id = Auth::user()->id;
                            $sms_log_save->save();

                            $sms_offer_info->delete();

                        } elseif (!is_null($sms_receive_info)) {
                            $sms_log_save = new OfferSmsLog();
                            $sms_log_save->ansar_id = $ansar_id;
                            $sms_log_save->sms_offer_id = $sms_offer_info->id;
                            $sms_log_save->mobile_no = $mobile_no->mobile_no_self;
                            //$sms_log_save->offer_status=;
                            $sms_log_save->reply_type = "Yes";
                            $sms_log_save->offered_district = $sms_receive_info->offered_district;
                            $sms_log_save->offered_date = $sms_receive_info->sms_received_datetime;
                            $sms_log_save->action_user_id = Auth::user()->id;
                            $sms_log_save->save();

                            $sms_receive_info->delete();
                        }

                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//                        Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLACKED','from_state'=>'BLOCKED','to_state'=>'BLACKED','action_by'=>auth()->user()->id]));

                    }
                    CustomQuery::addActionlog(['ansar_id'=>$request->input('ansar_id'),'action_type'=>'BLACKED','from_state'=>'BLOCKED','to_state'=>'BLACKED','action_by'=>auth()->user()->id]);
                    break;

            }
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
            $blacklist_log_entry->move_to = "Free";
            $blacklist_log_entry->move_date = $modified_unblack_date;
            $blacklist_log_entry->action_user_id = Auth::user()->id;
            $blacklist_log_entry->save();

            $blacklist_info->delete();

            AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 1, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
//            Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'FREE','from_state'=>'BLACKED','to_state'=>'FREE','action_by'=>auth()->user()->id]));
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLACKED', 'from_state' => 'BLACKED', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);


            DB::commit();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return Redirect::route('dg_unblacklist_entry_view')->with('success_message', 'Ansar removed from Blacklist Successfully');
    }
}
