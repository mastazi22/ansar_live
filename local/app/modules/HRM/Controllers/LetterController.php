<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\MemorandumModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LetterController extends Controller
{
    //
    function transferLetterView()
    {
        return View::make('HRM::Letter.transfer_letter');
    }

    function getMemorandumIds(Request $requests)
    {
//        return $requests->all();
        $t = DB::table('tbl_memorandum_id')
            ->join('tbl_transfer_ansar', 'tbl_transfer_ansar.transfer_memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_transfer_ansar.transfered_kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_memorandum_id.*');
        $e = DB::table('tbl_memorandum_id')
            ->join('tbl_embodiment', 'tbl_embodiment.memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_memorandum_id.*');
        $d = DB::table('tbl_memorandum_id')
            ->join('tbl_rest_info', 'tbl_rest_info.memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_embodiment_log', 'tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment_log.kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_memorandum_id.*');
        if($requests->unit){
            $e->where('tbl_kpi_info.unit_id',$requests->unit);
            $t->where('tbl_kpi_info.unit_id',$requests->unit);
            $d->where('tbl_kpi_info.unit_id',$requests->unit)->orderBy('tbl_embodiment_log.id','desc');
        }
        return $t->toSql();
        switch ($requests->type) {
            case 'TRANSFER':
                return view('HRM::Letter.partial_letter_view',['data'=>$t->distinct()->paginate(20),'units'=>District::all()]);
            case 'EMBODIED':
                return view('HRM::Letter.partial_letter_view',['data'=>$e->distinct()->paginate(20),'units'=>District::all()]);
            case 'DISEMBODIED':
                return view('HRM::Letter.partial_letter_view',['data'=>$d->distinct('tbl_rest_info.memorandum_id')->paginate(20),'units'=>District::all()]);

            default:
                return [];
        }

    }

    function printLetter(Request $request)
    {
//        return $request->all();
        $id = Input::get('id');
        $type = Input::get('type');
        $unit = Input::get('unit');
        $view = Input::get('view');
        $option = Input::get('option');
        $rules = [
            'type' => 'regex:/^[A-Z]+$/',
            'unit' => 'numeric|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        switch ($type) {
            case 'TRANSFER':
                return $this->transferLetterPrint($id, $unit, $view,$option);
            case 'EMBODIMENT':
                return $this->embodimentLetterPrint($id, $unit, $view,$option);
            case 'DISEMBODIMENT':
                return $this->disembodimentLetterPrint($id, $unit, $view,$option);
        }
    }

    function transferLetterPrint($id, $unit, $v,$option)
    {
        $mem = DB::table('tbl_memorandum_id')
            ->join('tbl_transfer_ansar','tbl_transfer_ansar.transfer_memorandum_id','=','tbl_memorandum_id.memorandum_id')
            ->distinct('tbl_memorandum_id.memorandum_id')->select('tbl_memorandum_id.memorandum_id as memorandum_id', 'mem_date as created_at');
        //$mem = TransferAnsar::where('transfer_memorandum_id', $id)->select('transfer_memorandum_id', 'created_at')->first();

        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_transfer_ansar')
            ->join('tbl_kpi_info as pk', 'tbl_transfer_ansar.present_kpi_id', '=', 'pk.id')
            ->join('tbl_kpi_info as tk', 'tbl_transfer_ansar.transfered_kpi_id', '=', 'tk.id')
            ->join('tbl_ansar_parsonal_info', 'tbl_transfer_ansar.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tk.unit_id',$unit)
            ->select('tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'pk.kpi_name as p_kpi_name', 'tk.kpi_name as t_kpi_name');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_transfer_ansar.ansar_id',$id);
        }
        else{
            $result->where('tbl_transfer_ansar.transfer_memorandum_id', $id);
            $mem->where('tbl_transfer_ansar.transfer_memorandum_id', $id);
        }
        $result = $result->get();
        $mem = $mem->first();
        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_transfer_letter']);
//            else return View::make('HRM::Letter.print_transfer_letter')->with(['mem' => $mem, 'user' => $user, 'ta' => $result]);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with(['id' => $id]);
        }
    }

    function embodimentLetterPrint($id, $unit, $v,$option)
    {
//        $mem = MemorandumModel::where('memorandum_id', $id)->select('memorandum_id', 'mem_date as created_at')->first();
        $mem = DB::table('tbl_embodiment')
            ->leftJoin('tbl_memorandum_id','tbl_memorandum_id.memorandum_id','=','tbl_embodiment.memorandum_id')
            ->select('tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_thana as kt', 'kt.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_kpi_info.unit_id',$unit)
            ->select('tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name as village_name', 'tbl_ansar_parsonal_info.post_office_name as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment.joining_date','kt.thana_name_bng as kpi_thana');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_embodiment.ansar_id',$id);
        }
        else{
            $result->where('tbl_embodiment.memorandum_id', $id);
            $mem->where('tbl_embodiment.memorandum_id', $id);
        }
        $result = $result->get();
        $mem = $mem->first();
        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_embodiment_letter']);
//            else return View::make('HRM::Letter.print_embodiment_letter')->with(['result' => $result, 'user' => $user, 'mem' => $mem]);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }
    }

    function disembodimentLetterPrint($id, $unit, $v,$option)
    {
        DB::enableQueryLog();
        $mem = DB::table('tbl_rest_info')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.memorandum_id')
            ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_rest_info.disembodiment_reason_id')
            ->select('tbl_disembodiment_reason.reason_in_bng as reason', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
        //return Response::json($mem);
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_embodiment_log')
            ->join('tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->whereRaw('tbl_embodiment_log.release_date=tbl_rest_info.rest_date')
            ->where('tbl_kpi_info.unit_id',$unit)
            ->select('tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name as village_name', 'tbl_ansar_parsonal_info.post_office_name as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment_log.joining_date', 'tbl_embodiment_log.release_date')->orderBy('tbl_embodiment_log.id','DESC');
//        return $result;
       // return DB::getQueryLog();
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_rest_info.ansar_id',$id);
        }
        else{
            $result->where('tbl_rest_info.memorandum_id', $id);
            $mem->where('tbl_rest_info.memorandum_id', $id);
        }
//        return $result->toSql();
        $result = DB::table(DB::raw("({$result->toSql()}) x"))->mergeBindings($result)->groupBy('ansar_id')->get();
//        return $result;
//        return DB::getQueryLog();
        $mem = $mem->first();
        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_disembodiment_letter']);
//            else return View::make('HRM::Letter.print_disembodiment_letter')->with(['result' => $result, 'user' => $user, 'mem' => $mem]);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }
    }

    function embodimentLetterView()
    {
        return View::make('HRM::Letter.embodiment_letter');
    }

    function disembodimentLetterView()
    {
        return View::make('HRM::Letter.disembodiment_letter');
    }
}
