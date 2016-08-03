<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AnsarIdCard;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\RestInfoLogModel;
use App\modules\HRM\Models\RestInfoModel;
use Barryvdh\Snappy\Facades\SnappyImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

class ReportController extends Controller
{
    //
    function reportGuardSearchView()
    {
        return View::make('HRM::Report.report_guard_search');
    }

    function reportAllGuard()
    {
        $kpi = Input::get('kpi_id');

        $rules = [
            'kpi_id' => 'regex:/^[0-9]+$/'
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        } else {
            //DB::enableQueryLog();
            $ansar = DB::table('tbl_kpi_info')
                ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_kpi_info.id', '=', $kpi)
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_designations.name_bng',
                    'tbl_units.unit_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.joining_date')->get();
            //return DB::getQueryLog();
            $guards = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.id', '=', $kpi)
                ->select('tbl_kpi_info.kpi_name', 'tbl_kpi_info.kpi_address', 'tbl_kpi_detail_info.total_ansar_given', 'tbl_units.unit_name_bng', 'tbl_thana.thana_name_bng')->first();
            return Response::json(['ansars' => $ansar, 'guard' => $guards]);
        }
    }

    function localizeReport()
    {
        $s = file_get_contents(asset("report_" . Input::get('type') . ".json"));
        return json_encode(json_decode($s, true)[Input::get('name')]);
    }

    function ansarServiceReportView()
    {
        return View::make('HRM::Report.report_ansar_in_service');
    }

    function ansarServiceReport()
    {
        $ansar_id = Input::get('ansar_id');
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        }
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_blood_group', 'tbl_ansar_parsonal_info.blood_group_id', '=', 'tbl_blood_group.id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
            ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng', 'tbl_blood_group.blood_group_name_bng')->first();
        $ansarCurrentServiceRecord = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
            ->select('tbl_embodiment.joining_date', 'tbl_embodiment.reporting_date', 'tbl_embodiment.memorandum_id', 'tbl_embodiment.service_ended_date',
                'tbl_units.unit_name_bng', 'tbl_kpi_info.kpi_name')->first();
        $ansarPastServiceRecord = DB::table('tbl_embodiment_log')
            ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->where('tbl_embodiment_log.ansar_id', '=', $ansar_id)->orderBy('tbl_embodiment_log.id', 'desc')
            ->select('tbl_embodiment_log.joining_date', 'tbl_embodiment_log.reporting_date', 'tbl_embodiment_log.old_memorandum_id',
                'tbl_units.unit_name_bng', 'tbl_kpi_info.kpi_name', 'tbl_embodiment_log.release_date',
                'tbl_disembodiment_reason.reason_in_bng', 'tbl_embodiment_log.joining_date')->get();
        return Response::json(['ansar' => $ansar, 'current' => $ansarCurrentServiceRecord, 'past' => $ansarPastServiceRecord, 'pi' => file_exists($ansar->profile_pic)]);
    }

    function ansarPrintIdCardView()
    {
        return View::make('HRM::Report.ansar_id_card_view');
    }

    function printIdCard()
    {
        $id = Input::get('ansar_id');
        $issue_date = Input::get('issue_date');
        $expire_date = Input::get('expire_date');
        $type = Input::get('type');
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
            'issue_date' => 'required|date_format:d-M-Y',
            'expire_date' => 'required|date_format:d-M-Y',
        ];
        $message = [
            'required' => 'This field is required',
            'regex' => 'Enter a valid ansar id',
            'numeric' => 'Ansar id must be numeric',
            'date_format' => 'Invalid date format',
        ];
        $validation = Validator::make(Input::all(), $rules, $message);
        if ($validation->fails()) {
            return Response::json(['validation' => true, 'messages' => $validation->messages()]);
        }
        $report_data = $this->getReportData($type, 'ansar_id_card');
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_ansar_parsonal_info.division_id')
            ->join('tbl_blood_group', 'tbl_blood_group.id', '=', 'tbl_ansar_parsonal_info.blood_group_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $id)
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_' . $type . ' as name', 'tbl_designations.name_' . $type . ' as rank', 'tbl_blood_group.blood_group_name_' . $type . ' as blood_group', 'tbl_units.unit_name_' . $type . ' as unit_name', 'tbl_units.unit_code', 'tbl_division.division_code', 'tbl_ansar_parsonal_info.profile_pic')->first();
        if ($ansar) {
            $ansarIdHistory = AnsarIdCard::where('ansar_id', $id)->get();
            $id_card = new AnsarIdCard;
            $id_card->ansar_id = $id;
            $id_card->issue_date = Carbon::createFromFormat('d-M-Y', $issue_date)->format("Y-m-d");
            $id_card->expire_date = Carbon::createFromFormat('d-M-Y', $expire_date)->format("Y-m-d");
            $id_card->type = strtoupper($type);
            $id_card->status = 1;
            if (!$id_card->saveOrFail()) {
                return View::make('HRM::Report.no_ansar_found')->with('id', $id);
            }
            $path = public_path("{$id}.jpg");
            SnappyImage::loadView('HRM::Report.ansar_id_card_font', ['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::createFromFormat('d-M-Y', $issue_date)->format("d/m/Y"), 'ed' => Carbon::createFromFormat('d-M-Y', $expire_date)->format("d/m/Y"), 'type' => $type])->setOption('quality', 100)
                ->setOption('crop-x', 0)->setOption('crop-y', 0)->setOption('crop-h', 292)->setOption('crop-w', 340)->setOption('encoding', 'utf-8')->save($path);
            $image = Image::make($path)->encode('data-url');
            File::delete($path);
//            return View::make('HRM::Report.ansar_id_card_font',['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::createFromFormat('d-M-Y', $issue_date)->format("d/m/Y"), 'ed' => Carbon::createFromFormat('d-M-Y', $expire_date)->format("d/m/Y"), 'type' => $type]);
            return View::make('HRM::Report.id_card_print')->with(['image' => $image->encode('data-url'), 'history' => $ansarIdHistory]);
        }
        return View::make('HRM::Report.no_ansar_found')->with('id', $id);
    }

    function getReportData($type, $name)
    {
        $s = file_get_contents(asset("report_" . $type . ".json"));
        return json_decode($s, true)[$name];
    }

    public function ansarDisembodimentReportView()
    {
        return view('HRM::Report.ansar_disembodiment_report_view');
        //return view('report.disembodiment_rough');
    }

    public function disembodedAnsarInfo(Request $request)
    {
        $from = Input::get('from_date');
        $to = Input::get('to_date');
        $unit = $request->input('unit_id');
        $thana = $request->input('thana_id');
        $limit = Input::get('limit');
        $offset = Input::get('offset');

        $view = Input::get('view');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'from_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/'],
            'to_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/'],
            'unit_id' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana_id' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        } else {
            if (!is_null($from) && !is_null($to) && !is_null($unit) && !is_null($thana)) {
                $from_date = Carbon::parse($from)->format('Y-m-d');
                $to_date = Carbon::parse($to)->format('Y-m-d');
                if (strcasecmp($view, 'view') == 0) {
                    return CustomQuery::disembodedAnsarListforReport($offset, $limit, $from_date, $to_date, $unit, $thana);
                } else {
                    return CustomQuery::disembodedAnsarListforReportCount($from_date, $to_date, $unit, $thana);
                }
            }
        }

    }

    public function blockListView()
    {
        return view('HRM::Report.blocklist_view');
    }

    public function blockListedAnsarInfoDetails()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        if ((Auth::user()->type) == 22) {
            $unit = Auth::user()->district_id;
        } else {
            $unit = Input::get('unit');
        }
        $thana = Input::get('thana');
        $view = Input::get('view');

        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        } else {
            if (strcasecmp($view, 'view') == 0) {
                if (!is_null($unit) && !is_null($thana)) {
                    return CustomQuery::getBlocklistedAnsar($offset, $limit, $unit, $thana);
                }
            } else {
                if (!is_null($unit) && !is_null($thana)) {
                    return CustomQuery::getTotalBlockedAnsarCount($unit, $thana);
                }
            }
        }
    }

    public function blackListView()
    {
        return view('HRM::Report.blacklist_view');
    }

    public function blackListedAnsarInfoDetails()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        if ((Auth::user()->type) == 22) {
            $unit = Auth::user()->district_id;
        } else {
            $unit = Input::get('unit');
        }
        $thana = Input::get('thana');
        $view = Input::get('view');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        } else {
            if (strcasecmp($view, 'view') == 0) {
                if (!is_null($unit) && !is_null($thana)) {
                    return CustomQuery::getBlacklistedAnsar($offset, $limit, $unit, $thana);
                }
            } else {
                if (!is_null($unit) && !is_null($thana)) {
                    return CustomQuery::getTotalBlackedAnsarCount($unit, $thana);
                }

            }
        }
    }

    public function getAnserTransferHistory()
    {
        DB::enableQueryLog();
        $ansar_id = Input::get('ansar_id');
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        } else {
            $transfer_history = DB::table('tbl_transfer_ansar')
                ->join(DB::raw('tbl_kpi_info as pk'), 'tbl_transfer_ansar.present_kpi_id', '=', 'pk.id')
                ->join(DB::raw('tbl_kpi_info as tk'), 'tbl_transfer_ansar.transfered_kpi_id', '=', 'tk.id')
                ->join('tbl_units', 'pk.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'pk.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_transfer_ansar.ansar_id', $ansar_id)
                ->select('tbl_transfer_ansar.present_kpi_join_date as joiningDate', 'tbl_transfer_ansar.transfered_kpi_join_date as transferDate',
                    'pk.kpi_name as FromkpiName', 'tk.kpi_name as TokpiName', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana');
            //return DB::getQueryLog();
            return $transfer_history->get();
        }
    }

    public function ansarEmbodimentReportView()
    {
        return view('HRM::Report.ansar_embodiment_report_view');
//        return view('report.embodiment_rough');
    }

    public function embodedAnsarInfo()
    {
        $view = Input::get('view');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $from = Input::get('from_date');
        $to = Input::get('to_date');
        $unit = Input::get('unit_id');
        $thana = Input::get('thana_id');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'from_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/'],
            'to_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/'],
            'unit_id' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana_id' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        } else {
            if (!is_null($from) && !is_null($to) && !is_null($unit) && !is_null($thana)) {
                $from_date = Carbon::parse($from)->format('Y-m-d');
                $to_date = Carbon::parse($to)->format('Y-m-d');
                if (strcasecmp($view, 'view') == 0) {
                    return CustomQuery::embodedAnsarListforReport($offset, $limit, $from_date, $to_date, $unit, $thana);
                } else {
                    return CustomQuery::embodedAnsarListforReportCount($from_date, $to_date, $unit, $thana);
                }
            }
        }
    }

    public function threeYearsOverListView()
    {
        return view('HRM::Report.three_years_over_report');
    }

    public function threeYearsOverAnsarInfo()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
//        if ((Auth::user()->type) == 22) {
//            $unit = Auth::user()->district_id;
//        } else {
        $unit = Input::get('unit');
//        }
        $ansar_rank = Input::get('ansar_rank');
        $ansar_sex = Input::get('ansar_sex');
        //$thana = Input::get('thana');
        $view = Input::get('view');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => 'numeric',
            'ansar_rank' => 'numeric',
            'ansar_sex' => 'regex:/^[A-Za-z]+$/',
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        } else {
            if (strcasecmp($view, 'view') == 0) {
                if (!is_null($unit) && !is_null($ansar_rank) && !is_null($ansar_sex)) {
                    return CustomQuery::threeYearsOverAnsarList($offset, $limit, $unit, $ansar_rank, $ansar_sex);
                }

            } else {
                if (!is_null($unit) && !is_null($ansar_rank) && !is_null($ansar_sex)) {
                    return CustomQuery::threeYearsOverAnsarCount($unit, $ansar_rank, $ansar_sex);
                }
            }
        }
    }

    public function anserTransferHistory()
    {
        return View::make('HRM::Report.ansar_transfer_history');

    }

    public function viewAnsarServiceRecord()
    {
        return View::make('HRM::Report.ansar_service_record');
    }

    public function serviceRecordUnitWise()
    {
        return view('HRM::Report.service_record_unitwise');
    }

    public function ansarInfoForServiceRecordUnitWise()
    {
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $rules = [
            'unit' => ['regex:/^[0-9]+$/'],
            'thana' => ['regex:/^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        if (!$unit && !$thana) {
            $ansar_details = '';
            return Response::json($ansar_details);
        }
        if ($unit && !$thana) {
            $ansar_details = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->orderBy('tbl_embodiment.ansar_id', 'asc')
                ->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
                    'tbl_units.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi')
                ->get();
            return Response::json($ansar_details);
        }
        elseif (!$unit && $thana) {
            $ansar_details = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->where('tbl_kpi_info.thana_id', '=', $thana)
                ->orderBy('tbl_embodiment.ansar_id', 'asc')
                ->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
                    'tbl_units.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi')
                ->get();
            return Response::json($ansar_details);
        }
        elseif ($unit && $thana) {
            $ansar_details = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana)
                ->orderBy('tbl_embodiment.ansar_id', 'asc')
                ->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
                    'tbl_units.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi')
                ->get();
            return Response::json($ansar_details);
        }
    }

    public function getPrintIdList()
    {
        $rules = [
            'f_date' => 'required|date_format:d-M-Y',
            't_date' => 'required|date_format:d-M-Y',
        ];
        $message = [
            'f_date.required' => 'From date field is required',
            't_date.required' => 'To date field is required',
            'f_date.date_format' => 'From date field is invalid',
            't_date.date_format' => 'To date field is invalid',
        ];
        $valid = Validator::make(Input::all(), $rules, $message);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 400, ['Content-Type', 'application/json']);
        }
        $f_date = Carbon::createFromFormat("d-M-Y", Input::get('f_date'))->format("Y-m-d");
        $t_date = Carbon::createFromFormat("d-M-Y", Input::get('t_date'))->format("Y-m-d");
//        return Response::json(["f"=>$f_date,"t"=>$t_date]);
        $ansars = AnsarIdCard::whereBetween('created_at', [$f_date, $t_date])->get();
        return Response::json(['ansars' => $ansars]);
    }

    public function ansarCardStatusChange()
    {
        $rules = [
            'action' => 'required|regex:/^[a-z]+$/',
            'ansar_id' => 'required|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
        switch (Input::get('action')) {
            case 'block':
                $ansar = AnsarIdCard::where('ansar_id', Input::get('ansar_id'))->first();
                $ansar->status = 0;
                if ($ansar->save()) {
                    return Response::json(['status' => 1]);
                } else {
                    return Response::json(['status' => 0]);
                }
                break;
            case 'active':
                $ansar = AnsarIdCard::where('ansar_id', Input::get('ansar_id'))->first();
                $ansar->status = 1;
                if ($ansar->save()) {
                    return Response::json(['status' => 1]);
                } else {
                    return Response::json(['status' => 0]);
                }
                break;
            default:
                return response("Invalid request(400)", 400);
        }
    }

    public function printIdList()
    {
        return View::make('HRM::Report.ansar_print_id_list');
    }

    public function checkFile()
    {
        return Response::json(['status' => file_exists(public_path() . '/' . Input::get('path'))]);
    }

    public function offerReportView()
    {
        return View::make('HRM::Report.offer_report');
    }

    public function getOfferedAnsar()
    {
        $unit = Input::get('unit');
        $past = Input::get('report_past');
        $type = Input::get('type');
        $rules = [
            'unit' => 'numeric',
            'report_past' => 'numeric',
            'type' => 'numeric',
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
        $c_date = Carbon::now();
        switch ($type) {
            case 0:
            case 1:
                $c_date = $c_date->subDays($past);
                break;
            case 2:
                $c_date = $c_date->subMonths($past);
                break;
            case 3:
                $c_date = $c_date->subYears($past);
                break;
        }
        $offer_not_respond = DB::table('tbl_sms_offer_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')->where('tbl_sms_offer_info.sms_send_datetime', '>=', $c_date)->where('tbl_sms_offer_info.district_id', $unit)
            ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_offer_info.sms_send_datetime')->unionAll(DB::table('tbl_sms_send_log')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')->where('tbl_sms_send_log.action_date', '>=', $c_date)->where('tbl_sms_send_log.offered_district', $unit)->where('tbl_sms_send_log.reply_type', 'No Reply')
                ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.offered_date as sms_send_datetime'))->get();

        $offer_received = DB::table('tbl_sms_receive_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')->where('tbl_sms_receive_info.sms_received_datetime', '>=', $c_date)->where('tbl_sms_receive_info.offered_district', $unit)
            ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_receive_info.sms_received_datetime')->unionAll(DB::table('tbl_sms_send_log')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')->where('tbl_sms_send_log.action_date', '>=', $c_date)->where('tbl_sms_send_log.offered_district', $unit)->where('tbl_sms_send_log.reply_type', 'Yes')
                ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.action_date as sms_received_datetime'))->get();
        $offer_reject = DB::table('tbl_sms_send_log')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')->where('tbl_sms_send_log.action_date', '>=', $c_date)->where('tbl_sms_send_log.offered_district', $unit)->where('tbl_sms_send_log.reply_type', 'No')
            ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.action_date as reject_date')->get();
        return Response::json(['onr' => $offer_not_respond, 'or' => $offer_received, 'orj' => $offer_reject]);
    }

    public function rejectedOfferListView()
    {
        return View::make('HRM::Offer.rejected_offer_list');
    }

    public function getRejectedAnsarList()
    {
        $rules = [
            'from_date' => 'required|date_format:d-M-Y',
            'to_date' => 'required|date_format:d-M-Y',
            'rejection_no' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $message = [
            'from_date.required' => 'From date field is required',
            'to_date.required' => 'To date field is required',
            'from_date.date_format' => 'From date field is invalid',
            'to_date.date_format' => 'To date field is invalid',
            'rejection_no.required' => 'Rejection no required',
            'rejection_no.numeric' => 'Rejection no must be integer.eg 1,2...',
            'rejection_no.regex' => 'Rejection no must be integer.eg 1,2...',
        ];
        $valid = Validator::make(Input::all(), $rules, $message);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        $fd = Carbon::createFromFormat("d-M-Y", Input::get('from_date'))->format("Y-m-d");
        $td = Carbon::createFromFormat("d-M-Y", Input::get('to_date'))->format("Y-m-d");
        $rejection_no = Input::get('rejection_no');
        $ansars = [];
        $rejected_ansar = OfferSmsLog::whereBetween('action_date', [$fd, $td])->whereIn('reply_type', ['No Reply', 'No'])->groupBy('ansar_id')->having(DB::raw('count(ansar_id)'), '>=', $rejection_no)->select('ansar_id', DB::raw('count(ansar_id)'))->get();
        foreach ($rejected_ansar as $ra) {
            $is_embodied = EmbodimentModel::where('ansar_id', $ra->ansar_id)->whereBetween('joining_date', [$fd, $td])->exists() || EmbodimentLogModel::where('ansar_id', $ra->ansar_id)->whereBetween('joining_date', [$fd, $td])->exists();
            $is_rest = RestInfoModel::where('ansar_id', $ra->ansar_id)->whereBetween('rest_date', [$fd, $td])->exists() || RestInfoLogModel::where('ansar_id', $ra->ansar_id)->whereBetween('rest_date', [$fd, $td])->exists();
            if (!$is_embodied && !$is_rest) {
                $a = DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')->where('tbl_ansar_parsonal_info.ansar_id', $ra->ansar_id)
                    ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng', 'tbl_ansar_status_info.*')->first();
                array_push($ansars, $a);
            }
            //return Response::json(['isEmbodied'=>$is_embodied,'isRest'=>$is_rest]);
        }
        return $ansars;
    }
}
