<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\KpiInfoLogModel;
use App\modules\HRM\Models\MemorandumModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class KpiController extends Controller
{
    public function kpiIndex()
    {
        return view('HRM::Kpi.kpi_entry');
    }

    public function kpiView()
    {
        return view('HRM::Kpi.kpi_view');
    }

    public function kpiViewDetails()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $division = Input::get('division');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $view = Input::get('view');
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::kpiInfo($offset, $limit, $division, $unit, $thana);
        } else {
            return CustomQuery::kpiInfoCount($division, $unit, $thana);
        }
    }


    public function saveKpiInfo(Request $request)
    {
        $kpi_name = $request->input('kpi_name');
        $division_id = $request->input('division_name_eng');
        $unit_id = $request->input('unit_name_eng');
        $thana_id = $request->input('thana_name_eng');
        $kpi_address = $request->input('kpi_address');
        $kpi_contact_no = $request->input('kpi_contact_no');
        $total_ansar_request = $request->input('total_ansar_request');
        $total_ansar_given = $request->input('total_ansar_given');
        $with_weapon = $request->input('with_weapon');
        $weapon_count = $request->input('weapon_count');
        $bullet_no = $request->input('bullet_no');
        $weapon_description = $request->input('weapon_description');
        $no_of_ansar = $request->input('no_of_ansar');
        $no_of_apc = $request->input('no_of_apc');
        $no_of_pc = $request->input('no_of_pc');
        $activation_date = $request->input('activation_date');
        $withdraw_date = $request->input('withdraw_date');

        $rules=[
            'kpi_name'=> 'required',
            'division_name_eng'=> 'required',
            'unit_name_eng'=> 'required',
            'thana_name_eng'=> 'required',
            'kpi_address'=> 'required',
            'kpi_contact_no'=> 'required',
            'total_ansar_request'=> 'required',
            'total_ansar_given'=> 'required',
            'with_weapon'=> 'required',
            'weapon_count'=> 'required',
            'bullet_no'=> 'required',
            'weapon_description'=> 'required',
            'no_of_ansar'=> 'required',
            'no_of_apc'=> 'required',
            'no_of_pc'=> 'required',
            'activation_date'=> 'required|date_format:d-M-Y',
            'withdraw_date'=> 'date_format:d-M-Y',
        ];
        $modified_activation_date = Carbon::parse($activation_date)->format('Y-m-d');
        if (strcasecmp($withdraw_date, '') != 0) {
            $modified_withdraw_date = Carbon::parse($withdraw_date)->format('Y-m-d');
        } else {
            $modified_withdraw_date = NULL;
        }

//        return $modified_withdraw_date;
        DB::beginTransaction();
        try {
            $kpi_general = new KpiGeneralModel();
            $kpi_general->kpi_name = $kpi_name;
            $kpi_general->division_id = $division_id;
            $kpi_general->unit_id = $unit_id;
            $kpi_general->thana_id = $thana_id;
            $kpi_general->kpi_address = $kpi_address;
            $kpi_general->kpi_contact_no = $kpi_contact_no;
            $kpi_general->status_of_kpi = 0;
            $kpi_general->save();

            $kpi_details = new KpiDetailsModel();
            $kpi_details->kpi_id = $kpi_general->id;
            $kpi_details->total_ansar_request = $total_ansar_request;
            $kpi_details->total_ansar_given = $total_ansar_given;
            $kpi_details->with_weapon = $with_weapon;
            $kpi_details->weapon_count = $weapon_count;
            $kpi_details->bullet_no = $bullet_no;
            $kpi_details->weapon_description = $weapon_description;
            $kpi_details->activation_date = $modified_activation_date;
            $kpi_details->withdraw_date = $modified_withdraw_date;
            $kpi_details->no_of_ansar = $no_of_ansar;
            $kpi_details->no_of_apc = $no_of_apc;
            $kpi_details->no_of_pc = $no_of_pc;
            $kpi_details->save();
            DB::commit();
            CustomQuery::addActionlog(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('kpi_view')->with('success_message', 'New KPI is Entered successfully');
    }

    public function edit($id)
    {
        $kpi_info = KpiGeneralModel::find($id);
        $kpi_details = KpiDetailsModel::where('kpi_id', $id)->first();
        return view('HRM::Kpi.kpi_edit', ['id' => $id])->with(['kpi_info' => $kpi_info])->with('kpi_details', $kpi_details);
    }

    public function updateKpi(Request $request)
    {
        $id = $request->input('id');
        $activation_date = $request->input('activation_date');
        $withdraw_date = $request->input('withdraw_date');
        $modified_activation_date = Carbon::parse($activation_date)->format('Y-m-d');
//        $modified_withdraw_date=Carbon::parse($withdraw_date)->format('Y-m-d');

        if (strcasecmp($withdraw_date, '') != 0) {
            $modified_withdraw_date = Carbon::parse($request->input('withdraw_date'))->format('Y-m-d');
        } else {
            $modified_withdraw_date = NULL;
        }

        DB::beginTransaction();
        try {

            $kpi_general = KpiGeneralModel::find($id);
            $kpi_general->kpi_name = $request->input('kpi_name');
            $kpi_general->division_id = $request->input('division_name_eng');
            $kpi_general->unit_id = $request->input('unit_name_eng');
            $kpi_general->thana_id = $request->input('thana_name_eng');
            $kpi_general->kpi_address = $request->input('kpi_address');
            $kpi_general->kpi_contact_no = $request->input('kpi_contact_no');
            $kpi_general->save();


            $kpi_details = KpiDetailsModel::where('kpi_id', $id)->first();
            $kpi_details->total_ansar_request = $request->input('total_ansar_request');
            $kpi_details->total_ansar_given = $request->input('total_ansar_given');
            $kpi_details->with_weapon = $request->input('with_weapon');
            $kpi_details->weapon_count = $request->input('weapon_count');
            $kpi_details->bullet_no = $request->input('bullet_no');
            $kpi_details->weapon_description = $request->input('weapon_description');
            $kpi_details->activation_date = $modified_activation_date;
            $kpi_details->withdraw_date = $modified_withdraw_date;
            $kpi_details->no_of_ansar = $request->input('no_of_ansar');
            $kpi_details->no_of_apc = $request->input('no_of_apc');
            $kpi_details->no_of_pc = $request->input('no_of_pc');
            $kpi_details->save();

            DB::commit();
            CustomQuery::addActionlog(['ansar_id' => $kpi_general->id, 'action_type' => 'EDIT KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('kpi_view')->with('success_message', 'New KPI is Updated successfully');
    }

    public function delete($id)
    {
        KpiGeneralModel::find($id)->delete();
        return redirect('/kpi_view');
    }

    public function kpiVerify()
    {
        $kpi_id = Input::get('verified_id');
        $kpi_status_update = KpiGeneralModel::where('id', $kpi_id)->update(['status_of_kpi' => 1]);
        if ($kpi_status_update) {
            return 1;
        } else {
            return 0;
        }
    }

    public function ansarWithdrawView()
    {
        $kpi_names = KpiGeneralModel::where('status_of_kpi', 1)->get();
        return view('HRM::Kpi.ansar_withdraw_view')->with('kpi_names', $kpi_names);
    }

    public function ansarListForWithdraw()
    {

        $statusSelected = Input::get('selected_name');
        $kpi_infos = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->where('tbl_embodiment.kpi_id', '=', $statusSelected)
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0)
            ->where('tbl_embodiment.emboded_status', '=', "Emboded")
            ->distinct()
            ->select('tbl_embodiment.*', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_kpi_info.kpi_name', 'tbl_division.division_name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_designations.name_eng')
            ->get();
        //$kpi_infos = EmbodimentModel::where('kpi_id', $statusSelected)->get();
        if (count($kpi_infos) <= 0) return Response::json(array('result' => true));
        return view('HRM::Kpi.selected_ansar_withrdaw_view')->with('kpi_infos', $kpi_infos);
    }

    public function ansarWithdrawUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $kpi_id = $request->input('kpi_id_withdraw');
            $ansar_ids = EmbodimentModel::where('kpi_id', $kpi_id)->get();
            $withdraw_date = $request->input('kpi_withdraw_date');
            $modified_withdraw_date = Carbon::parse($withdraw_date)->format('Y-m-d');
            $memorandum_id = $request->input('memorandum_id');
            $memorandum_id_save = new MemorandumModel();
            $memorandum_id_save->memorandum_id = $memorandum_id;
            $memorandum_id_save->save();
            $user = [];
            if ($kpi_id && $ansar_ids && $withdraw_date && $memorandum_id) {
                foreach ($ansar_ids as $ansar_id) {
                    $withdraw_ansar_id = $ansar_id->ansar_id;
                    $freeze_change = new FreezingInfoModel();
                    $freeze_change->ansar_id = $withdraw_ansar_id;
                    $freeze_change->freez_reason = "Guard Withdraw";
                    $freeze_change->freez_date = $modified_withdraw_date;
                    $freeze_change->comment_on_freez = $request->input('kpi_withdraw_reason');
                    $freeze_change->memorandum_id = $memorandum_id;
                    $freeze_change->kpi_id = $ansar_id->kpi_id;
                    $freeze_change->ansar_embodiment_id = $ansar_id->id;
                    $freeze_change->action_user_id = Auth::user()->id;
                    $freeze_change->save();

                    $embodied_status_freezed = EmbodimentModel::where('ansar_id', $withdraw_ansar_id)->first();
                    $embodied_status_freezed->emboded_status = "Freeze";
                    $embodied_status_freezed->action_user_id = Auth::user()->id;
                    $embodied_status_freezed->save();

                    AnsarStatusInfo::where('ansar_id', $withdraw_ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 1]);

                    $kpi_log_change = new KpiInfoLogModel();
                    $kpi_log_change->kpi_id = $kpi_id;
                    $kpi_log_change->ansar_id = $withdraw_ansar_id;
                    $kpi_log_change->reason_for_freeze = "Withdraw";
                    $kpi_log_change->comment_on_freeze = $request->input('kpi_withdraw_reason');
                    $kpi_log_change->date_of_freeze = $modified_withdraw_date;
                    $kpi_log_change->reporting_date = $ansar_id->reporting_date;
                    $kpi_log_change->joining_date = $ansar_id->joining_date;
                    $kpi_log_change->action_user_id = Auth::user()->id;
                    $kpi_log_change->save();
                    array_push($user, ['ansar_id' => $ansar_id->ansar_id, 'action_type' => 'FREEZE', 'from_state' => 'EMBODIED', 'to_state' => 'FREEZE', 'action_by' => auth()->user()->id]);

                }
                DB::commit();
                CustomQuery::addActionlog($user, true);
                CustomQuery::addActionlog(['ansar_id' => $kpi_id, 'action_type' => 'WITHDRAW KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
            }
            return Redirect::route('ansar-withdraw-view')->with('success_message', 'Ansar/s Withdrawn successfully');
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function reduceGuardStrength()
    {
        $kpi_names = KpiGeneralModel::where('status_of_kpi', 1)->get();
        return view('HRM::Kpi.reduce_guard_strength')->with('kpi_names', $kpi_names);
    }

    public function ansarListForReduce()
    {
        $selctedReducedAnsars = Input::get('selected_name');
        $kpi_infos = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->where('tbl_embodiment.kpi_id', '=', $selctedReducedAnsars)
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0)
            ->where('tbl_embodiment.emboded_status', '=', "Emboded")
            ->distinct()
            ->select('tbl_embodiment.*', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_kpi_info.kpi_name', 'tbl_division.division_name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_designations.name_eng')
            ->get();
        if (count($kpi_infos) <= 0) return Response::json(array('result' => true));
        return view('HRM::Kpi.selected_reduce_guard_strength')->with('kpi_infos', $kpi_infos);
    }

    public function ansarReduceUpdate(Request $request)
    {
        DB::beginTransaction();
        $kpi_id = '';
        try {
            if ($request->ajax()) {
                $selected_ansars = Input::get('ansaridget');
                $mi = Input::get('memorandum_id');
                $rd = Input::get('reduce_date');
                $modified_reduce_date = Carbon::parse($rd)->format('Y-m-d');
                $reduce_reason = Input::get('reduce_reason');
                $memorandum_entry = new MemorandumModel();
                $memorandum_entry->memorandum_id = $mi;
                $memorandum_entry->save();
                $user = [];
                if (!is_null($selected_ansars)) {
                    for ($i = 0; $i < count($selected_ansars); $i++) {

                        $reduced_ansar = $selected_ansars[$i];
                        $ansar_ids = EmbodimentModel::where('ansar_id', $reduced_ansar)->where('emboded_status', 'Emboded')->get();
                        foreach ($ansar_ids as $ansar_id) {
                            $reduced_ansar_id = $ansar_id->ansar_id;
                            $kpi_id = $ansar_id->kpi_id;
                            $freeze_change = new FreezingInfoModel();
                            $freeze_change->ansar_id = $reduced_ansar_id;
                            $freeze_change->freez_reason = "Guard Reduce";
                            $freeze_change->freez_date = $modified_reduce_date;
                            $freeze_change->comment_on_freez = $reduce_reason;
                            $freeze_change->memorandum_id = $mi;
                            $freeze_change->kpi_id = $ansar_id->kpi_id;
                            $freeze_change->ansar_embodiment_id = $ansar_id->id;
                            $freeze_change->action_user_id = Auth::user()->id;
                            $freeze_change->save();

                            $embodied_status_freezed = EmbodimentModel::where('ansar_id', $reduced_ansar_id)->first();
                            $embodied_status_freezed->emboded_status = "Freeze";
                            $embodied_status_freezed->action_user_id = Auth::user()->id;
                            $embodied_status_freezed->save();

                            AnsarStatusInfo::where('ansar_id', $reduced_ansar_id)->update(['embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 1]);

                            $kpi_log_change = new KpiInfoLogModel();
                            $kpi_log_change->kpi_id = $ansar_id->kpi_id;
                            $kpi_log_change->ansar_id = $reduced_ansar_id;
                            $kpi_log_change->reason_for_freeze = "Reduce";
                            $kpi_log_change->comment_on_freeze = $reduce_reason;
                            $kpi_log_change->date_of_freeze = $modified_reduce_date;
                            $kpi_log_change->reporting_date = $ansar_id->reporting_date;
                            $kpi_log_change->joining_date = $ansar_id->joining_date;
                            $kpi_log_change->action_user_id = Auth::user()->id;
                            $kpi_log_change->save();
                            array_push($user, ['ansar_id' => $ansar_id->ansar_id, 'action_type' => 'FREEZE', 'from_state' => 'EMBODIED', 'to_state' => 'FREEZE', 'action_by' => auth()->user()->id]);
                        }
                    }
                }
            }
            DB::commit();
            CustomQuery::addActionlog($user, true);
            CustomQuery::addActionlog(['ansar_id' => $kpi_id, 'action_type' => 'REDUCE KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' => "Ansar/s reduced unsuccessfully"]);
        }
        return Response::json(['status' => true, 'message' => "Ansar/s reduced successfully"]);
    }

    public function guardBeforeWithdrawView()
    {
        return view('HRM::Kpi.ansar_before_withdraw_list');
    }

    public function loadAnsarsForBeforeWithdraw()
    {
        $kpi_id = Input::get('kpi_id');
        $freeze_reason = "Guard Withdraw";

        $guards_before_withdraw = DB::table('tbl_freezing_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_freezing_info.kpi_id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
            ->where('tbl_freezing_info.freez_reason', '=', $freeze_reason)
            ->where('tbl_freezing_info.kpi_id', '=', $kpi_id)
            ->distinct()
            ->select('tbl_freezing_info.ansar_id as id', 'tbl_freezing_info.freez_date as date', 'tbl_freezing_info.comment_on_freez as reason', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana', 'tbl_kpi_info.kpi_name',
                'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.reporting_date as r_date', 'tbl_kpi_info.kpi_name')->get();
//        return Response::json($guards_before_withdraw);
        return $guards_before_withdraw;
    }

    public function guardBeforeReduceView()
    {
        return view('HRM::Kpi.ansar_before_reduce_list');
    }

    public function loadAnsarsForBeforeReduce()
    {
        $kpi_id = Input::get('kpi_id');
        $freeze_reason = "Guard Reduce";

        $guards_before_reduce = DB::table('tbl_freezing_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_freezing_info.kpi_id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
            ->where('tbl_freezing_info.freez_reason', '=', $freeze_reason)
            ->where('tbl_freezing_info.kpi_id', '=', $kpi_id)
            ->distinct()
            ->select('tbl_freezing_info.ansar_id as id', 'tbl_freezing_info.freez_date as date', 'tbl_freezing_info.comment_on_freez as reason', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana', 'tbl_kpi_info.kpi_name',
                'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.reporting_date as r_date', 'tbl_kpi_info.kpi_name')->get();
//        return Response::json($guards_before_withdraw);
        return $guards_before_reduce;
    }

    public function kpiWithdrawView()
    {
        return view('HRM::Kpi.kpi_withdraw_view');
    }

    public function loadKpiForWithdraw()
    {
        $kpi_id = Input::get('kpi_id');
        $unit_id = Input::get('unit_id');
        $thana_id = Input::get('thana_id');
        $rules = [
            'kpi_id'=>'required|numeric|regex:/^[0-9]+$/',
            'unit_id'=>'required|numeric|regex:/^[0-9]+$/',
            'thana_id'=>'required|numeric|regex:/^[0-9]+$/',
        ];
        $validation = Validator::make(Input::all(),$rules);
        if(!$validation->fails()){
            $kpi_info = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.id', '=', $kpi_id)
                ->whereNull('tbl_kpi_detail_info.kpi_withdraw_date')
                ->select('tbl_kpi_info.kpi_name as kpi', 'tbl_kpi_detail_info.total_ansar_request as tar', 'tbl_kpi_detail_info.total_ansar_given as tag', 'tbl_kpi_detail_info.weapon_count as weapon', 'tbl_kpi_detail_info.bullet_no as bullet', 'tbl_kpi_detail_info.activation_date as a_date', 'tbl_division.division_name_eng as division', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')
                ->first();
            return Response::json($kpi_info);
        }
    }

    public function kpiWithdrawUpdate(Request $request)
    {
        $kpi_id = $request->input('kpi_id');
        $unit_id = Input::get('unit_id');
        $thana_id = Input::get('thana_id');
        $withdraw_date = $request->input('withdraw_date');

        $rules = [
            'kpi_id'=>'required|numeric|regex:/^[0-9]+$/',
            'unit_id'=>'required|numeric|regex:/^[0-9]+$/',
            'thana_id'=>'required|numeric|regex:/^[0-9]+$/',
            'withdraw_date'=>'required|date_format:d-M-Y',
        ];
        $message = [
            'required'=>'This field is required',
            'regex'=>'Enter a valid ansar id',
            'numeric'=>'Ansar id must be numeric',
            'date_format'=>'Invalid date format',
        ];
        $validation = Validator::make(Input::all(),$rules,$message);
        if($validation->fails()){
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        }else{
            $modified_withdraw_date = Carbon::parse($withdraw_date)->format('Y-m-d');
            DB::beginTransaction();
            try {
                $kpi_withdraw_date_entry = KpiDetailsModel::where('kpi_id', $kpi_id)->first();
                $kpi_withdraw_date_entry->kpi_withdraw_date = $modified_withdraw_date;
                $kpi_withdraw_date_entry->save();

                DB::commit();
            } catch
            (Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }
            return Redirect::route('kpi-withdraw-view')->with('success_message', 'KPI withdraw date is saved successfully');
        }
    }

    public function withdrawnKpiView()
    {
        return view('HRM::Kpi.withdrawn_kpi_list_view');
    }

    public function withdrawnKpiList()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $view = Input::get('view');
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::withdrawnKpiInfo($offset, $limit, $unit, $thana);
        } else {
            return CustomQuery::withdrawnKpiInfoCount($unit, $thana);
        }
    }

    public function kpiWithdrawDateEdit($id)
    {
        $kpi_details = KpiDetailsModel::where('kpi_id', $id)->first();
        $kpi_info = KpiGeneralModel::find($id);
        return view('HRM::Kpi.kpi_withdraw_date_edit', ['id' => $id])->with(['kpi_info' => $kpi_info, 'kpi_details' => $kpi_details]);
    }

    public function kpiWithdrawDateUpdate(Request $request)
    {
        $id = $request->input('id');

        DB::beginTransaction();
        try {
            $kpi_details = KpiDetailsModel::where('kpi_id', $id)->first();
            $modified_activation_date = Carbon::parse($request->input('withdraw-date'))->format('Y-m-d');
            $kpi_details->kpi_withdraw_date = $modified_activation_date;

            $kpi_details->save();
            DB::commit();
//            Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'EDIT KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
        } catch
        (Exception $e) {
            DB::rollback();
//            return Response::json(['status' => false, 'message' => "Date not Updated"]);
        }
        return Redirect::route('withdrawn_kpi_view')->with('success_message', 'KPI withdraw date is updated successfully');
    }

    public function inactiveKpiView()
    {
        return view('HRM::Kpi.inactive_kpi_view');
    }

    public function inactiveKpiList()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $view = Input::get('view');
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::inactiveKpiInfo($offset, $limit, $unit, $thana);
        } else {
            return CustomQuery::inactiveKpiInfoCount($unit, $thana);
        }
    }

    public function activeKpi($id)
    {
        DB::beginTransaction();
        try {
            KpiGeneralModel::where('id', $id)->update(['withdraw_status' => 0]);
            DB::commit();
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('kpi_view')->with('success_message', 'KPI is Active Successfully!');
    }

    public function withdrawnKpiName(Request $request)
    {
        if (Input::exists('id')) {
            $id = $request->input('id');
            $kpi = KpiGeneralModel::where('thana_id', '=', $id)->where('status_of_kpi', 1)->get();
        } else
            $kpi = KpiGeneralModel::where('status_of_kpi', 1)->get();

        return Response::json($kpi);
    }

    public function kpiWithdrawCancelView()
    {
        return view('HRM::Kpi.kpi_withdraw_cancel');
    }

    public function kpiListForWithdrawCancel()
    {
        $kpi_id = Input::get('kpi_id');
        $kpi_info = DB::table('tbl_kpi_info')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->where('tbl_kpi_info.id', '=', $kpi_id)
            ->whereNotNull('tbl_kpi_detail_info.kpi_withdraw_date')
            ->select('tbl_kpi_info.kpi_name as kpi', 'tbl_kpi_detail_info.total_ansar_request as tar',
                'tbl_kpi_detail_info.total_ansar_given as tag', 'tbl_kpi_detail_info.weapon_count as weapon',
                'tbl_kpi_detail_info.bullet_no as bullet', 'tbl_kpi_detail_info.activation_date as a_date',
                'tbl_division.division_name_eng as division', 'tbl_units.unit_name_eng as unit',
                'tbl_thana.thana_name_eng as thana', 'tbl_kpi_detail_info.kpi_withdraw_date as w_date')
            ->first();
        return Response::json($kpi_info);
    }

    public function kpiWithdrawCancelUpdate(Request $request)
    {
        $kpi_id = $request->input('kpi_id');
        $kpi_withdraw_date_cancel = KpiDetailsModel::where('kpi_id', $kpi_id)->first();
        if (empty($kpi_withdraw_date_cancel->kpi_withdraw_date)) {
            return Redirect::route('kpi_withdraw_cancel_view')->with('error_message', 'This kpi not in withdraw list');
        }
        DB::beginTransaction();
        try {
            $kpi_withdraw_date_cancel = KpiDetailsModel::where('kpi_id', $kpi_id)->first();
            $kpi_withdraw_date_cancel->kpi_withdraw_date = NULL;
            $kpi_withdraw_date_cancel->save();

            $kpi_withdraw_status_change = KpiGeneralModel::find($kpi_id);
            $kpi_withdraw_status_change->withdraw_status = 0;
            $kpi_withdraw_status_change->save();

            DB::commit();
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('kpi_withdraw_cancel_view')->with('success_message', 'KPI withdrawal cancelled successfully');
    }
}

