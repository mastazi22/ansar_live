<?php

namespace App\modules\HRM\Controllers;

use App\Helper\ExportDataToExcel;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\AnsarIdCard;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\RestInfoLogModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\TransferAnsar;
use Barryvdh\Snappy\Facades\SnappyImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use ExportDataToExcel;

    //
    function reportGuardSearchView()
    {
        return View::make('HRM::Report.report_guard_search');
    }

    function reportAllGuard(Request $request)
    {
        $kpi = Input::get('kpi_id');

        $rules = [
            'kpi_id' => 'required|regex:/^[0-9]+$/',
            'unit' => 'required|regex:/^[0-9]+$/',
            'thana' => 'required|regex:/^[0-9]+$/',
            'division' => 'required|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return $valid->messages();
            return response("Invalid Request(400)", 400);
        } else {
            //DB::enableQueryLog();
            $edu = DB::table('tbl_ansar_education_info')->select(DB::raw('MAX(education_id) edu_id'), 'ansar_id')
                ->groupBy('ansar_id')->toSql();
            $ansar = DB::table('tbl_kpi_info')
                ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_ansar_education_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_education_info.ansar_id')
                ->join('tbl_education_info', 'tbl_education_info.id', '=', 'tbl_ansar_education_info.education_id')
                ->join(DB::raw("($edu) edu"), function ($q) {
                    $q->on('edu.edu_id', '=', 'tbl_ansar_education_info.education_id');
                    $q->on('edu.ansar_id', '=', 'tbl_ansar_education_info.ansar_id');
                })
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_kpi_info.id', '=', $kpi)
                ->where('tbl_kpi_info.unit_id', '=', $request->unit)
                ->where('tbl_kpi_info.thana_id', '=', $request->thana)
                ->where('tbl_kpi_info.division_id', '=', $request->division)
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.embodied_status', '=', 1)
                ->groupBy('tbl_ansar_parsonal_info.ansar_id')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.data_of_birth as dob', DB::raw('CONCAT(hight_feet," feet ",hight_inch," inch") as height'), 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_designations.name_bng',
                    'tbl_units.unit_name_bng', 'tbl_embodiment.transfered_date', 'tbl_embodiment.joining_date'
                    , DB::raw("IF(tbl_education_info.id!=0,tbl_education_info.education_deg_bng,tbl_ansar_education_info.name_of_degree) AS education"))->orderBy('tbl_embodiment.joining_date', 'desc')->get();
            //return DB::getQueryLog();
            $guards = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.id', '=', $kpi)
                ->where('tbl_kpi_info.unit_id', '=', $request->unit)
                ->where('tbl_kpi_info.thana_id', '=', $request->thana)
                ->where('tbl_kpi_info.division_id', '=', $request->division)
                ->select('tbl_kpi_info.kpi_name', 'tbl_kpi_info.kpi_address', 'tbl_kpi_detail_info.total_ansar_given', 'tbl_units.unit_name_bng', 'tbl_thana.thana_name_bng')->first();
            $data = ['ansars' => $ansar, 'guard' => $guards];
            if (Input::exists('export')) {
                return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.ansar_in_guard');
            }
            return Response::json($data);
        }
    }

    function localizeReport()
    {
        $s = file_get_contents(public_path("report_" . Input::get('type') . ".json"));
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
//        return Input::all();
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
        $bng_data = ["title" => "বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী",
            "id_no" => "আইডি নং",
            "name" => "নাম",
            "rank" => "পদবী",
            "bg" => "রক্তের গ্রুপ",
            "unit" => "জেলা",
            "id" => "প্রদানের তারিখ",
            "ed" => "শেষের তারিখ",
            "bs" => "বাহকের স্বাক্ষর",
            "is" => "কর্তৃকারীর স্বাক্ষর",
            "footer_title" => "প্রদানকারী কর্তৃপক্ষ : বাংলাদেশ আনসার ও ভিডিপি"];
        $eng_data = [
            "title" => "Bangladesh Ansar and Village Defence Party",
            "name" => "Name",
            "id_no" => "ID NO",
            "rank" => "Rank",
            "bg" => "Blood Group",
            "unit" => "District",
            "id" => "Issue Date",
            "ed" => "Expire Date",
            "bs" => "Bearer`s Sign",
            "is" => "Issuer`s Sign",
            "footer_title" => "Issuing Authority: Bangladesh Ansar & VDP"
        ];
        $validation = Validator::make(Input::all(), $rules, $message);
        if ($validation->fails()) {
            return Response::json(['validation' => true, 'messages' => $validation->messages()]);
        }
        $report_data = ${$type . "_data"};
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_ansar_parsonal_info.division_id')
            ->join('tbl_blood_group', 'tbl_blood_group.id', '=', 'tbl_ansar_parsonal_info.blood_group_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $id)
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_' . $type . ' as name', 'tbl_designations.name_' . $type . ' as rank', 'tbl_blood_group.blood_group_name_' . $type . ' as blood_group', 'tbl_units.unit_name_' . $type . ' as unit_name', 'tbl_units.unit_code', 'tbl_division.division_code', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_ansar_parsonal_info.sign_pic')->first();
        if ($ansar) {
            $ansarIdHistory = AnsarIdCard::where('ansar_id', $id)->get();
            $id_card = new AnsarIdCard;
            $id_card->ansar_id = $id;
            $id_card->issue_date = Carbon::parse($issue_date)->format("Y-m-d");
            $id_card->expire_date = Carbon::parse($expire_date)->format("Y-m-d");
            $id_card->type = strtoupper($type);
            $id_card->status = 1;
            if (!$id_card->saveOrFail()) {
                return View::make('HRM::Report.no_ansar_found')->with('id', $id);
            }
            return View::make('HRM::Report.ansar_id_card_font', ['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse($issue_date)->format("d/m/Y"), 'ed' => Carbon::parse($expire_date)->format("d/m/Y"), 'type' => $type]);
            $path = public_path("{$id}.jpg");
            SnappyImage::loadView('HRM::Report.ansar_id_card_font', ['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse($issue_date)->format("d/m/Y"), 'ed' => Carbon::parse($expire_date)->format("d/m/Y"), 'type' => $type])->setOption('quality', 100)
                ->setOption('crop-x', 0)->setOption('crop-y', 0)->setOption('crop-h', 292)->setOption('crop-w', 340)->setOption('encoding', 'utf-8')->save($path);
            $image = Image::make($path)->encode('data-url');
            File::delete($path);
//            return View::make('HRM::Report.ansar_id_card_font',['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse( $issue_date)->format("d/m/Y"), 'ed' => Carbon::parse( $expire_date)->format("d/m/Y"), 'type' => $type]);
            return View::make('HRM::Report.id_card_print')->with(['image' => $image->encode('data-url'), 'history' => $ansarIdHistory]);
        }
        return View::make('HRM::Report.no_ansar_found')->with('id', $id);
    }

    function getAnsarIDHistory(Request $request)
    {

        $ansarIdHistory = AnsarIdCard::where('ansar_id', $request->ansar_id)->get();
        return $ansarIdHistory;

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
        $division = $request->input('division_id');
        $thana = $request->input('thana_id');
        $limit = Input::get('limit');
        $offset = Input::get('offset');

        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'from_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'to_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'unit_id' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
            'thana_id' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
            'division_id' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        if (!is_null($from) && !is_null($to) && !is_null($unit) && !is_null($thana)) {
            $from_date = Carbon::parse($from)->format('Y-m-d');
            $to_date = Carbon::parse($to)->format('Y-m-d');
            $data = CustomQuery::disembodedAnsarListforReport($offset, $limit, $from_date, $to_date, $division, $unit, $thana);
            if (Input::exists('export')) {
                return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.disembodied_report');
            }
            return response()->json($data);
        }


    }

    public function blockListView()
    {
        return view('HRM::Report.blocklist_view');
    }

    public function blockListedAnsarInfoDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $thana = Input::get('thana');
        $unit = Input::get('unit');
        $division = Input::get('division');

        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::getBlocklistedAnsar($offset, $limit, $division, $unit, $thana, $request->q);
//        return $data;
        if (Input::exists('export')) {

            return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.blocklist_report');
        }
        return response()->json($data);
    }

    public function blackListView()
    {
        return view('HRM::Report.blacklist_view');
    }

    public function blackListedAnsarInfoDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $thana = Input::get('thana');
        $unit = Input::get('unit');
        $division = Input::get('division');
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
        }
        $data = CustomQuery::getBlacklistedAnsar($offset, $limit, $division, $unit, $thana, $request->q);
        if (Input::exists('export')) {
            return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.blacklist_report');
        }
        return response()->json($data);
    }

    public function getAnserTransferHistory(Request $request)
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
            if ($request->unit) {
                $transfer_history->where(function ($query) use ($request) {
                    $query->where('pk.unit_id', $request->unit)->orWhere('tk.unit_id', $request->unit);
                });
            }
            if ($request->range) {
                $transfer_history->where(function ($query) use ($request) {
                    $query->where('pk.division_id', $request->range)->orWhere('tk.division_id', $request->range);
                });
            }
            $b = $transfer_history->get();
//            return DB::getQueryLog();
            return $b;
        }
    }

    public function ansarEmbodimentReportView()
    {
        return view('HRM::Report.ansar_embodiment_report_view');
//        return view('report.embodiment_rough');
    }

    public function embodedAnsarInfo()
    {
        $division = Input::get('division_id');
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
            'from_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'to_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'unit_id' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana_id' => ['regex:/^(all)$|^[0-9]+$/'],
            'division_id' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
//            return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        } else {
            if (!is_null($from) && !is_null($to) && !is_null($unit) && !is_null($thana)) {
                $from_date = Carbon::parse($from)->format('Y-m-d');
                $to_date = Carbon::parse($to)->format('Y-m-d');
                $data = CustomQuery::embodedAnsarListforReport($offset, $limit, $from_date, $to_date, $division, $unit, $thana);
                if (Input::exists('export')) {
                    return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.embodiment_report');
                }
                return response()->json($data);
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
        $division = Input::get('division');
        $thana = Input::get('thana');
//        }
        $ansar_rank = Input::get('ansar_rank');
        $ansar_sex = Input::get('ansar_sex');
        //$thana = Input::get('thana');
        $view = Input::get('view');
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => 'required',
            'ansar_rank' => 'required',
            'ansar_sex' => 'regex:/^[A-Za-z]+$/|required',
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::threeYearsOverAnsarList($offset, $limit, $division, $unit, $ansar_rank, $ansar_sex);
        if (Input::exists('export')) {
            return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.three_years_over_list_view');
        }
        return response()->json($data);
    }

    public function ansarOverAgedInfo(Request $request)
    {
        if ($request->ajax()) {
            $limit = Input::get('limit');
            $offset = Input::get('offset');
//        if ((Auth::user()->type) == 22) {
//            $unit = Auth::user()->district_id;
//        } else {
            $unit = Input::get('unit');
            $division = Input::get('range');
            $thana = Input::get('thana');
//        }
            $ansar_rank = Input::get('ansar_rank');
            $ansar_sex = Input::get('ansar_sex');
            //$thana = Input::get('thana');
            $view = Input::get('view');
            $rules = [
                'limit' => 'numeric',
                'offset' => 'numeric',
                'unit' => 'required',
            ];
            $valid = Validator::make(Input::all(), $rules);

            if ($valid->fails()) {
                //return print_r($valid->messages());
                return response("Invalid Request(400)", 400);
            }
            $data = CustomQuery::ansarListOveraged($offset, $limit, $unit, $thana, $division);
            if (Input::exists('export')) {
                return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.ansar_over_age_report');
            }
            return response()->json($data);
        }
        return view('HRM::Report.ansar_over_age_report');
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
        DB::enableQueryLog();
//        return Input::all();
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $rules = [
            'unit' => ['regex:/^[0-9]+$/'],
            'thana' => ['regex:/^[0-9]+$/'],
            'division' => ['regex:/^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        $ansar_details = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded');
        if ($unit) {
            $ansar_details->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($thana) {
            $ansar_details->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        if ($division) {
            $ansar_details->where('tbl_kpi_info.division_id', '=', $division);
        }
        $b = $ansar_details->orderBy('tbl_embodiment.ansar_id', 'asc')
            ->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
                'tbl_units.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi')->get();
        return $b;
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
        $f_date = Carbon::parse(Input::get('f_date'))->format("Y-m-d");
        $t_date = Carbon::parse(Input::get('t_date'))->format("Y-m-d");
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
//        return Input::all();
        $unit = Input::get('unit');
        $division = Input::get('division');
        $past = Input::get('report_past');
        $type = Input::get('type');
        $rules = [
            'unit' => 'numeric',
            'division' => 'numeric',
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
//        DB::enableQueryLog();
        $offer_not_respond = DB::table('tbl_sms_offer_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_offer_info.district_id')
            ->whereDate('tbl_sms_offer_info.sms_send_datetime', '>=', $c_date)
            ->where('tbl_units.id', $unit)
            ->where('tbl_units.division_id', $division)
            ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_offer_info.sms_send_datetime')
            ->unionAll(DB::table('tbl_sms_send_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
                ->whereDate('tbl_sms_send_log.offered_date', '>=', $c_date)
                ->where('tbl_units.id', $unit)
                ->where('tbl_units.division_id', $division)
                ->where('tbl_sms_send_log.reply_type', 'No Reply')
                ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.offered_date as sms_send_datetime'))->get();

        $offer_received_query = DB::table('tbl_sms_receive_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_receive_info.offered_district')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->whereDate('tbl_sms_receive_info.sms_send_datetime', '>=', $c_date)
            ->where('tbl_units.id', $unit)
            ->where('tbl_units.division_id', $division)
            ->select('tbl_sms_receive_info.sms_send_datetime as offered_date', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_receive_info.sms_received_datetime')
            ->unionAll(DB::table('tbl_sms_send_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->whereDate('tbl_sms_send_log.offered_date', '>=', $c_date)
                ->where('tbl_units.id', $unit)
                ->where('tbl_units.division_id', $division)
                ->where('tbl_sms_send_log.reply_type', 'Yes')
                ->select('tbl_sms_send_log.offered_date', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.action_date as sms_received_datetime'));

        $offer_received = DB::table(DB::raw("(" . $offer_received_query->toSql() . ") t"))->mergeBindings($offer_received_query)->groupBy('ansar_id')->get();


        $offer_reject = DB::table('tbl_sms_send_log')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
            ->whereDate('tbl_sms_send_log.offered_date', '>=', $c_date)
            ->where('tbl_units.id', $unit)
            ->where('tbl_units.division_id', $division)
            ->where('tbl_sms_send_log.reply_type', 'No')
            ->select('tbl_sms_send_log.offered_date', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.action_date as reject_date')->get();
        if (Input::exists('export') && Input::get('export') == 'true') {
            $e = Excel::create('offer_report', function ($excel) use ($offer_not_respond, $offer_received, $offer_reject) {
                $excel->sheet('offer_not_respond', function ($sheet) use ($offer_not_respond) {
                    $sheet->loadView('HRM::export.offer_not_respond', ['index' => 1, 'ansars' => $offer_not_respond]);
                });
                $excel->sheet('offer_received', function ($sheet) use ($offer_received) {
                    $sheet->loadView('HRM::export.offer_accepted', ['index' => 1, 'ansars' => $offer_received]);
                });
                $excel->sheet('offer_rejected', function ($sheet) use ($offer_reject) {
                    $sheet->loadView('HRM::export.offer_rejected', ['index' => 1, 'ansars' => $offer_reject]);
                });
            })->store('xls', storage_path());
            return response()->json(['status' => true, 'url' => url()->route('download_file_by_name', ['file' => base64_encode(storage_path('offer_report.xls'))])]);
        }
        $offer_not_respond_count = collect($offer_not_respond)->groupBy('code')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $offer_received_count = collect($offer_received)->groupBy('code')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $offer_reject_count = collect($offer_reject)->groupBy('code')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $r = Response::json([
            'onr' => [
                'data' => $offer_not_respond,
                'count' => $offer_not_respond_count
            ],
            'or' => [
                'data' => $offer_received,
                'count' => $offer_received_count
            ],
            'orj' => [
                'data' => $offer_reject,
                'count' => $offer_reject_count
            ]
        ]);
//        return DB::getQueryLog();
        return $r;
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
        $fd = Carbon::parse(Input::get('from_date'))->format("Y-m-d");
        $td = Carbon::parse(Input::get('to_date'))->format("Y-m-d");
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

    function ansarHistoryView()
    {
        if (Auth::user()->id == 1) {
            return View::make('HRM::Report.ansar_history');
        } else abort(401);
    }

    function getAnsarHistory(Request $request)
    {
        if (Auth::user()->id == 1) {
            $ansar_id = $request->ansar_id;
            if ($ansar_id) {
                $detail = ActionUserLog::with('user')->where('ansar_id', $ansar_id)->get();
                $ansarInfo = PersonalInfo::where('ansar_id', $ansar_id)->first();
                return Response::json(['logs' => $detail, 'ansarInfo' => $ansarInfo]);
            } else {
                return Response::json([]);
            }
        } else abort(401);
    }

    public function viewAnsarHistory()
    {
        return View::make('HRM::Report.view_ansar_history');
    }

    public function viewAnsarHistoryReport(Request $request)
    {
//        DB::enableQueryLog();
        $result = array();
        if (!isset($request->ansar_id) || empty($request->ansar_id) || $request->ansar_id == 0 || !is_numeric($request->ansar_id)) {
            return response("{'error':'Invalid Request'}", 400, ['Content-Type' => 'text/html']);
        }
        $ansar_id = $request->ansar_id;
        $ansar = PersonalInfo::where('ansar_id', $ansar_id)->first();
        $result["ansar"] = $ansar;
        $result["status"] = $ansar->status->getStatus();
        $result["designation"] = $ansar->designation;

        //offer information
        $result["cOffer"] = $ansar->offer_sms_info()->with("district.division")->first();
        $result["lOffer"] = $ansar->offerLog()->with("district.division")->orderBy("offered_date", "desc")->get();
        $result["bOffer"] = OfferBlockedAnsar::where('ansar_id', $ansar_id)->withTrashed()->orderBy("id", "desc")->get();

        //panel information
        $result["cPanel"] = $ansar->panel()->first();
        $result["lPanel"] = $ansar->panelLog()->orderBy("panel_date", "desc")->get();

        //embodiment information
        $result["cEmbodiment"] = $ansar->embodiment()->with("kpi.unit", "kpi.division", "kpi.thana")->first();
        $result["lEmbodiment"] = $ansar->embodiment_log()->with("disembodimentReason", "kpi.unit", "kpi.division", "kpi.thana")->orderBy("joining_date", "desc")->get();

        //freeze information
        $result["cFreeze"] = $ansar->freezing_info()->with("kpi.unit", "kpi.division", "kpi.thana")->first();
        $result["lFreeze"] = $ansar->freezingInfoLog()->orderBy("freez_date", "desc")->get();

        //Transfer information
        $result["transfer"] = TransferAnsar::where('ansar_id', $ansar_id)->with("presentKpi.unit", "presentKpi.division", "presentKpi.thana", "transferKpi.unit", "transferKpi.division", "transferKpi.thana")->orderBy("id", "desc")->get();

        //block information
        $result["block"] = $ansar->block()->orderBy("date_for_block", "desc")->get();

        //black information
        $result["cBlack"] = $ansar->black()->first();
        $result["lBlack"] = $ansar->blackLog()->orderBy("black_listed_date", "desc")->get();

//        dd(DB::getQueryLog());
        return Response::json($result);
    }
}
