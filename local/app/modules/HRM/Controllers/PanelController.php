<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoLogModel;
use App\modules\HRM\Models\RestInfoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class PanelController extends Controller
{
    public function panelView()
    {
        return view('HRM::Panel.panel_view_rough');
    }

    public function statusSelection(Request $request)
    {
        $statusSelected = Input::get('status');
        $select = Input::get('select');
        $from_id=Input::get('from');
        $to_id=Input::get('to');
        $count=Input::get('ansar_count');
        if ($statusSelected == 1) {
            //$ansar_status = AnsarStatusInfo::where('rest_status', 1)->get();

            $ansar_status = DB::table('tbl_rest_info')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->where('tbl_ansar_status_info.rest_status', '=', 1)
                ->whereBetween('tbl_rest_info.ansar_id', array($from_id, $to_id))
                ->whereBetween('tbl_rest_info.disembodiment_reason_id', array(3, 8))
                ->whereNotNull('tbl_ansar_parsonal_info.mobile_no_self')
                ->distinct()
                ->select('tbl_rest_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.created_at')
                ->skip(0)
                ->take($count)
                ->get();

            if (count($ansar_status) <= 0) return Response::json(array('result' => true));
            return view('HRM::Panel.selected_view')->with('ansar_status', $ansar_status);
        } elseif ($statusSelected == 2) {

            $ansar_status = DB::table('tbl_ansar_status_info')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->where('tbl_ansar_status_info.free_status', '=', 1)
                ->whereBetween('tbl_ansar_parsonal_info.ansar_id', array($from_id, $to_id))
                ->whereNotNull('tbl_ansar_parsonal_info.mobile_no_self')
                ->distinct()
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.created_at')
                ->skip(0)
                ->take($count)
                ->get();

            if (count($ansar_status) <= 0) return Response::json(array('result' => true));
            return view('HRM::Panel.selected_view')->with('ansar_status', $ansar_status);

        }
    }

    public function savePanelEntry(Request $request)
    {
        DB::beginTransaction();
        $user = [];
        try {
            $selected_ansars = $request->input('selected-ansar_id');
            $mi = $request->input('memorandum_id');
            $pd = $request->input('panel_date');
            $modified_panel_date=Carbon::parse($pd)->format('Y-m-d');
            $come_from_where = $request->input('come_from_where');
            $ansar_merit = $request->input('ansar_merit');
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $mi;
            $memorandum_entry->save();
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {

                    if ($come_from_where == 1) {

                        $panel_entry = new PanelModel;
                        $panel_entry->ansar_id = $selected_ansars[$i];
                        $panel_entry->come_from = "Rest";
                        $panel_entry->panel_date = $modified_panel_date;
                        $panel_entry->memorandum_id = $mi;
                        $panel_entry->ansar_merit_list = $ansar_merit[$i];
                        $panel_entry->action_user_id = Auth::user()->id;
                        $panel_entry->save();

                        $rest_info = RestInfoModel::where('ansar_id', $selected_ansars[$i])->first();

                        $rest_log_entry = new RestInfoLogModel();
                        $rest_log_entry->old_rest_id = $rest_info->id;
                        $rest_log_entry->old_embodiment_id = $rest_info->old_embodiment_id;
                        $rest_log_entry->old_memorandum_id = $rest_info->memorandum_id;
                        $rest_log_entry->ansar_id = $selected_ansars[$i];
                        $rest_log_entry->rest_date = $rest_info->rest_date;
                        $rest_log_entry->total_service_days = $rest_info->total_service_days;
                        $rest_log_entry->rest_type = $rest_info->rest_form;
                        $rest_log_entry->disembodiment_reason_id = $rest_info->disembodiment_reason_id;
                        $rest_log_entry->comment = $rest_info->comment;
                        $rest_log_entry->move_to = "Panel";
                        $rest_log_entry->move_date = $modified_panel_date;
                        $rest_log_entry->action_user_id = Auth::user()->id;
                        $rest_log_entry->save();

                        $rest_info->delete();
                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 1, 'freezing_status' => 0]);

                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'PANELED', 'from_state' => 'REST', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    } else {
                        $panel_entry = new PanelModel;
                        $panel_entry->ansar_id = $selected_ansars[$i];
                        $panel_entry->come_from = "Entry";
                        $panel_entry->panel_date = $modified_panel_date;
                        $panel_entry->memorandum_id = $mi;
                        $panel_entry->ansar_merit_list = $ansar_merit[$i];
                        $panel_entry->action_user_id = Auth::user()->id;
                        $panel_entry->save();

                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 1, 'freezing_status' => 0]);

                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'PANELED', 'from_state' => 'FREE', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    }

                }
            }
            DB::commit();
            CustomQuery::addActionlog($user, true);
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' => "Ansar/s not added to panel"]);
        }
        return Response::json(['status' => true, 'message' => "Ansar/s added to panel successfully"]);
    }

//    public function statusSelection(Request $request)
//    {
//        $statusSelected = Input::get('status');
//        $select = Input::get('select');
//        if ($select == 1) {
//            if ($statusSelected == 1) {
//                //$ansar_status = AnsarStatusInfo::where('rest_status', 1)->get();
//                $ansar_status = DB::table('tbl_rest_info')
//                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
//                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
//                    ->where('tbl_ansar_status_info.rest_status', '=', 1)
//                    ->whereBetween('tbl_rest_info.disembodiment_reason_id', array(3, 8))->distinct()
//                    ->select('tbl_rest_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.created_at')
//                    ->get();
//
//                return view('panel.selected_view')->with('ansar_status', $ansar_status);
//            } elseif ($statusSelected == 2) {
//
//                $ansar_status = DB::table('tbl_ansar_status_info')
//                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
//                    ->where('tbl_ansar_status_info.free_status', '=', 1)->distinct()
//                    ->select('tbl_ansar_status_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.created_at')
//                    ->get();
//
//                if (count($ansar_status) <= 0) return Response::json(array('result' => true));
//                return view('panel.selected_view')->with('ansar_status', $ansar_status);
//            }
//        } else {
//            $from_id = Input::get('from');
//            $to_id = Input::get('to');
//            if ($statusSelected == 1) {
//                $ansar_status = DB::table('tbl_rest_info')
//                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
//                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
//                    ->where('tbl_ansar_status_info.rest_status', '=', 1)
//                    ->whereBetween('tbl_rest_info.ansar_id', array($from_id, $to_id))
//                    ->whereBetween('tbl_rest_info.disembodiment_reason_id', array(3, 8))->distinct()
//                    ->select('tbl_rest_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.created_at')
//                    ->get();
//                //$ansar_status = AnsarStatusInfo::where('rest_status', 1)->whereBetween('ansar_id', [$from_id, $to_id])->get();
//                if (count($ansar_status) <= 0) return Response::json(array('result' => true));
//                return view('panel.selected_view')->with('ansar_status', $ansar_status);
//
//            } elseif ($statusSelected == 2) {
//                $ansar_status = DB::table('tbl_ansar_status_info')
//                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
//                    ->where('tbl_ansar_status_info.free_status', '=', 1)
//                    ->whereBetween('tbl_ansar_status_info.ansar_id', [$from_id, $to_id])->distinct()
//                    ->select('tbl_ansar_status_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.created_at')
//                    ->get();
//                //return Response::json(array('result' => true, 'view' => View::make('panel.selected_view')->with('ansar_data',$ansar_status)));
//                if (count($ansar_status) <= 0) return Response::json(array('result' => true));
//                return view('panel.selected_view')->with('ansar_status', $ansar_status);
//            }
//        }
//    }

    public function getCentralPanelList()
    {
        $pcMale = DB::table('tbl_panel_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_ansar_status_info.pannel_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')
            ->where('tbl_ansar_parsonal_info.designation_id', '=', 3)->count('tbl_ansar_parsonal_info.ansar_id');
        $pcFeMale = DB::table('tbl_panel_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_ansar_status_info.pannel_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')
            ->where('tbl_ansar_parsonal_info.designation_id', '=', 3)->count('tbl_ansar_parsonal_info.ansar_id');
        $apcMale = DB::table('tbl_panel_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_ansar_status_info.pannel_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')
            ->where('tbl_ansar_parsonal_info.designation_id', '=', 2)->count('tbl_ansar_parsonal_info.ansar_id');
        $apcFeMale = DB::table('tbl_panel_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_ansar_status_info.pannel_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')
            ->where('tbl_ansar_parsonal_info.designation_id', '=', 2)->count('tbl_ansar_parsonal_info.ansar_id');
        $ansarMale = DB::table('tbl_panel_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_ansar_status_info.pannel_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')
            ->where('tbl_ansar_parsonal_info.designation_id', '=', 1)->count('tbl_ansar_parsonal_info.ansar_id');
        $ansarFeMale = DB::table('tbl_panel_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_ansar_status_info.pannel_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')
            ->where('tbl_ansar_parsonal_info.designation_id', '=', 1)->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['pm' => $pcMale, 'pf' => $pcFeMale, 'apm' => $apcMale, 'apf' => $apcFeMale, 'am' => $ansarMale, 'af' => $ansarFeMale]);
    }

    public function getPanelListBySexAndDesignation($sex, $designation)
    {
        $ansarList = DB::table('tbl_panel_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->where('tbl_ansar_parsonal_info.sex', '=', $sex)
            ->where('tbl_ansar_status_info.pannel_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_parsonal_info.designation_id', '=', $designation);
        if(Input::exists('ansar_id')){
            $ansarList = $ansarList->where('tbl_ansar_parsonal_info.ansar_id',Input::get('ansar_id'));
            $ansarList = $ansarList->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_units.unit_name_bng',
                'tbl_thana.thana_name_bng','tbl_panel_info.created_at','tbl_panel_info.memorandum_id','tbl_designations.name_bng as rank')->orderBy('tbl_panel_info.created_at','asc')->get();
            return View::make('panel.search_panel_view')->with(['ansarList' => $ansarList]);
        }
        $ansarList = $ansarList->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_units.unit_name_bng',
            'tbl_thana.thana_name_bng','tbl_panel_info.created_at','tbl_panel_info.memorandum_id','tbl_designations.name_bng as rank')->orderBy('tbl_panel_info.created_at','asc')->get();
        return View::make('panel.panel_individual_list')->with(['designation' => $designation, 'sex' => $sex, 'ansarList' => $ansarList]);
    }
}