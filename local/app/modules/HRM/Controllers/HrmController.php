<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\ExportData;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\DataExportStatus;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\ExportDataJob;
use App\modules\HRM\Models\GlobalParameter;
use App\modules\HRM\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\ExportDataToExcel;

class HrmController extends Controller
{
    use ExportDataToExcel;

    function hrmDashboard()
    {
        // return $optional.' '.$type;
        $type = auth()->user()->type;
        if ($type == 22 || $type == 66) {
            return View::make('HRM::Dashboard.hrm-rc-dc');
        } else {

            return View::make('HRM::Dashboard.hrm');
        }
    }

    function progressInfo()
    {
        DB::enableQueryLog();
        $tseity = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->where('tbl_embodiment.emboded_status', 'Emboded')
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_status_info.black_list_status', 0)
            ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)');
        $arfyoa = DB::table("tbl_ansar_parsonal_info")
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_ansar_status_info.retierment_status',0)
            ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
        $tnimutt = DB::table('tbl_sms_offer_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');
        if (Input::exists('division_id')) {
            $tseity->where('tbl_kpi_info.division_id', Input::get('division_id'));
            $arfyoa->where('division_id', Input::get('division_id'));
            $tnimutt->where('tbl_ansar_parsonal_info.division_id', Input::get('division_id'));
        }
        if (Input::exists('district_id')) {
            $tseity->where('tbl_kpi_info.unit_id', Input::get('district_id'));
            $arfyoa->where('unit_id', Input::get('district_id'));
            $tnimutt->where('tbl_ansar_parsonal_info.unit_id', Input::get('district_id'));

        }
        $tseity = $tseity->count('tbl_embodiment.ansar_id');
        $arfyoa = $arfyoa->count('tbl_ansar_parsonal_info.ansar_id');
        $tnimutt = $tnimutt->get();
        //return $tnimutt;
        $i = 0;
        $uiui = array();
        foreach ($tnimutt as $tttt) {

            $uiui[$i] = $tttt->ansar_id;
            $i++;
        }

//return $tnimutt;
        //return (DB::getQueryLog());
        // $t = DB::select(DB::raw("(SELECT count(ansar_id) as t FROM tbl_embodiment WHERE emboded_status = 'Emboded' AND service_ended_date BETWEEN NOW() AND DATE_ADD(NOW(),INTERVAL 2 MONTH)) UNION ALL (SELECT count(ansar_id) as t FROM tbl_ansar_parsonal_info WHERE TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())>=50) UNION ALL (SELECT IFNULL((SELECT count(ansar_id) as t FROM tbl_sms_offer_info  GROUP BY ansar_id HAVING count(ansar_id)>10),0))"));
        $progressInfo = array(
            'totalServiceEndedInThreeYears' => $tseity,
            'totalAnsarReachedFiftyYearsOfAge' => $arfyoa,
            'totalNotInterestedMembersUptoTenTimes' => $i
        );
        return Response::json($progressInfo);
    }

    public function graphEmbodiment(Request $request)
    {
        DB::enableQueryLog();
        $ea1 = DB::table('tbl_embodiment_log')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->whereRaw('joining_date BETWEEN DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(),INTERVAL 1 YEAR),INTERVAL 1 MONTH),"%Y-%m-01") AND NOW()')
            ->groupBy(DB::raw('MONTH(joining_date)'))->orderBy(DB::raw('YEAR(joining_date)'))->orderBy(DB::raw('MONTH(joining_date)'))
            ->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total,joining_date as month'));
        $ea2 = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->whereRaw('joining_date BETWEEN DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(),INTERVAL 1 YEAR),INTERVAL 1 MONTH),"%Y-%m-01") AND NOW()')
            ->groupBy(DB::raw('MONTH(joining_date)'))->orderBy(DB::raw('YEAR(joining_date)'))->orderBy(DB::raw('MONTH(joining_date)'))
            ->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total,joining_date as month'));
        $da = DB::table('tbl_embodiment_log')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->whereRaw('release_date BETWEEN DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(),INTERVAL 1 YEAR),INTERVAL 1 MONTH),"%Y-%m-01") AND NOW()')
            ->groupBy(DB::raw('MONTH(release_date)'))->orderBy(DB::raw('YEAR(release_date)'))->orderBy(DB::raw('MONTH(release_date)'))
            ->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total,DATE_FORMAT(release_date,"%b,%y") as month'));
        if ($request->division_id) {
            $ea1->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $ea2->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $da->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
        }
        if ($request->district_id) {
            $ea1->where('tbl_ansar_parsonal_info.unit_id', $request->district_id);
            $ea2->where('tbl_ansar_parsonal_info.unit_id', $request->district_id);
            $da->where('tbl_ansar_parsonal_info.unit_id', $request->district_id);
        }
        $sql = $ea1->unionAll($ea2);
        $ea = DB::table(DB::raw("({$sql->toSql()}) x"))->mergeBindings($sql)->select(DB::raw('SUM(total) as total,DATE_FORMAT(month,"%b,%y") as month'))->orderBy(DB::raw('YEAR(month)'))->orderBy(DB::raw('MONTH(month)'))->groupBy(DB::raw('MONTH(month)'));
        $b = Response::json(["ea" => $ea->get(), 'da' => $da->get()]);
        //return DB::getQueryLog();
        return $b;
    }

    public function graphDisembodiment()
    {
        $ansars = DB::select(DB::raw('(select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =1 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =2 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =3 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =4 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =5 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =6 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =7 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =8 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =9 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =10 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =11 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =12 )'));
        $graph_disembodiment = array(
            'jan_ansar' => $ansars[0]->em,
            'feb_ansar' => $ansars[1]->em,
            'march_ansar' => $ansars[2]->em,
            'april_ansar' => $ansars[3]->em,
            'may_ansar' => $ansars[4]->em,
            'june_ansar' => $ansars[5]->em,
            'july_ansar' => $ansars[6]->em,
            'aug_ansar' => $ansars[7]->em,
            'sep_ansar' => $ansars[8]->em,
            'oct_ansar' => $ansars[9]->em,
            'nov_ansar' => $ansars[10]->em,
            'dec_ansar' => $ansars[11]->em
        );
        return Response::json($graph_disembodiment);
    }

    public function getRecentAnsar(Request $request)
    {
        DB::enableQueryLog();

        $recentTime = Carbon::now();
        $backTime = Carbon::now()->subDays(7);
        $allStatus = array(
            'recentAnsar' => DB::table('tbl_ansar_parsonal_info')->whereBetween('tbl_ansar_parsonal_info.created_at', array($backTime, $recentTime)),
            'recentNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->whereBetween('tbl_ansar_parsonal_info.updated_at', array($backTime, $recentTime)),
            'recentFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->whereBetween('tbl_panel_info.panel_date', array($backTime, $recentTime)),
            'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->join('tbl_units', 'tbl_sms_offer_info.district_id', '=', 'tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentOfferedReceived' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_receive_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')->join('tbl_units', 'tbl_sms_receive_info.offered_district', '=', 'tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereIn('tbl_sms_offer_info.district_id', $unit)->count(),
            'recentEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentRetire' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('retierment_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentOfferBlock' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('offer_block_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
        );
        if ($request->division_id) {
            $allStatus['recentAnsar']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentNotVerified']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentFree']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            /*if((Auth::user()->type==11||Auth::user()->type==33))*/
            $allStatus['recentPanel']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentOffered']->where('tbl_units.division_id', $request->division_id);
            $allStatus['recentOfferedReceived']->where('tbl_units.division_id', $request->division_id);
            $allStatus['recentEmbodied']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['recentEmbodiedOwn']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['recentEmbodiedDiff']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentFreeze']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentBlockList']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentBlackList']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentRest']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentRetire']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentOfferBlock']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
        }
        if ($request->unit_id) {
            $allStatus['recentAnsar']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentNotVerified']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentFree']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            /*if((Auth::user()->type==11||Auth::user()->type==33))*/
            $allStatus['recentPanel']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentOffered']->where('tbl_units.id', $request->unit_id);
            $allStatus['recentOfferedReceived']->where('tbl_units.id', $request->unit_id);
            $allStatus['recentEmbodied']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['recentEmbodiedOwn']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['recentEmbodiedDiff']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentFreeze']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentBlockList']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentBlackList']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentRest']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentRetire']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentOfferBlock']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
        }
        $results = [];
        foreach ($allStatus as $key => $q) {
            $results[$key] = $q->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
        }
        //return DB::getQueryLog();
        return Response::json($results);
    }

    public function showAnsarList($type)
    {
        $pageTitle = '';
        if (strcasecmp($type, 'all_ansar') == 0) {
            $pageTitle = "Total Ansars";
        } elseif (strcasecmp($type, 'not_verified_ansar') == 0) {
            $pageTitle = "Total Unverified Ansars";
        } elseif (strcasecmp($type, 'offerred_ansar') == 0) {
            $pageTitle = "Total Offered Ansars";
        } elseif (strcasecmp($type, 'freezed_ansar') == 0) {
            $pageTitle = "Total Frozen Ansars";
        } elseif (strcasecmp($type, 'free_ansar') == 0) {
            $pageTitle = "Total Free Ansars";
        } elseif (strcasecmp($type, 'paneled_ansar') == 0) {
            $pageTitle = "Total Paneled Ansars";
        } elseif (strcasecmp($type, 'rest_ansar') == 0) {
            $pageTitle = "Total Resting Ansars";
        } elseif (strcasecmp($type, 'blocked_ansar') == 0) {
            $pageTitle = "Total Blocklisted Ansars";
        } elseif (strcasecmp($type, 'blacked_ansar') == 0) {
            $pageTitle = "Total Blacklisted Ansars";
        } elseif (strcasecmp($type, 'embodied_ansar') == 0) {
            $pageTitle = "Total Embodied Ansars";
        } elseif (strcasecmp($type, 'own_embodied_ansar') == 0) {
            $pageTitle = "Own Embodied Ansars";
        } elseif (strcasecmp($type, 'embodied_ansar_in_different_district') == 0) {
            $pageTitle = "Embodied Ansar in Different District";
        } elseif (strcasecmp($type, 'offer_block') == 0) {
            $pageTitle = "Total Offer Blocked";
        }elseif (strcasecmp($type, 'retire_ansar') == 0) {
            $pageTitle = "Total Retire(Over Aged) Ansar";
        }

        return View::make('HRM::Dashboard.view_ansar_list')->with(['type' => $type, 'pageTitle' => $pageTitle]);
    }

    public function showRecentAnsarList($type)
    {
        $pageTitle = '';
        if (strcasecmp($type, 'all_ansar') == 0) {
            $pageTitle = "Total Ansars (Recent)";
        } elseif (strcasecmp($type, 'not_verified_ansar') == 0) {
            $pageTitle = "Total Unverified Ansars (Recent)";
        } elseif (strcasecmp($type, 'offerred_ansar') == 0) {
            $pageTitle = "Total Offered Ansars (Recent)";
        } elseif (strcasecmp($type, 'freezed_ansar') == 0) {
            $pageTitle = "Total Frozen Ansars (Recent)";
        } elseif (strcasecmp($type, 'free_ansar') == 0) {
            $pageTitle = "Total Free Ansars (Recent)";
        } elseif (strcasecmp($type, 'paneled_ansar') == 0) {
            $pageTitle = "Total Paneled Ansars (Recent)";
        } elseif (strcasecmp($type, 'rest_ansar') == 0) {
            $pageTitle = "Total Resting Ansars (Recent)";
        } elseif (strcasecmp($type, 'blocked_ansar') == 0) {
            $pageTitle = "Total Block-listed Ansars (Recent)";
        } elseif (strcasecmp($type, 'blacked_ansar') == 0) {
            $pageTitle = "Total Blacklisted Ansars (Recent)";
        } elseif (strcasecmp($type, 'embodied_ansar') == 0) {
            $pageTitle = "Total Embodied Ansars (Recent)";
        } elseif (strcasecmp($type, 'embodied_ansar_in_different_district') == 0) {
            $pageTitle = "Total Embodied Ansars in Diffrenet District (Recent)";
        } elseif (strcasecmp($type, 'own_embodied_ansar') == 0) {
            $pageTitle = "Total Embodied Ansars in Own District (Recent)";
        }elseif (strcasecmp($type, 'offer_block') == 0) {
            $pageTitle = "Total Offer Blocked(Recent)";
        }elseif (strcasecmp($type, 'retire') == 0) {
            $pageTitle = "Total Retire(Over Aged) Ansar (Recent)";
        }
        return View::make('HRM::Dashboard.view_recent_ansar_list')->with(['type' => $type, 'pageTitle' => $pageTitle]);
    }

    public function offerAcceptLastFiveDays()
    {
        return view('HRM::Dashboard.offer_accept_last_5_days');
    }

    public function getAnsarList(Request $request)
    {
        $type = Input::get('type');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        $data = [];
        switch ($type) {
            case 'all_ansar':
                $data = CustomQuery::getAllAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'not_verified_ansar':
                $data = CustomQuery::getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'free_ansar':
                $data = CustomQuery::getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'paneled_ansar':
                $data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank,$request->filter_mobile_no,$request->filter_age, $q);
                break;
            case 'embodied_ansar':
                $data = CustomQuery::getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q,$sex);
                break;
            case 'rest_ansar':
                $data = CustomQuery::getTotalRestAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'retire_ansar':
                $data = CustomQuery::getTotalRetireAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'freezed_ansar':
                $data = CustomQuery::getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'blocked_ansar':
                $data = CustomQuery::getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'blacked_ansar':
                $data = CustomQuery::getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'offerred_ansar':
                $data = CustomQuery::getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'offer_block':
                $data = CustomQuery::getTotalOfferBlockAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'own_embodied_ansar':
                $data = CustomQuery::getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'embodied_ansar_in_different_district':
                $data = CustomQuery::getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q);
                break;

        }
        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data,'HRM::export.ansar_view_excel',$type);

        }
        return Response::json($data);
    }

    public function getRecentAnsarList(Request $request)
    {
        $type = Input::get('type');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $view = Input::get('view');
        $rank = Input::get('rank');
        $division = Input::get('division');
        $q = Input::get('q');

        $sex = Input::get('gender');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'view' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        switch ($type) {
            case 'all_ansar':
                $data = CustomQuery::getAllAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'not_verified_ansar':
                $data = CustomQuery::getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'free_ansar':
                $data = CustomQuery::getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'paneled_ansar':
                $data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank,$request->filter_mobile_no,$request->filter_age, $q);
                break;
            case 'embodied_ansar':
                $data = CustomQuery::getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'embodied_ansar_in_different_district':
                $data = CustomQuery::getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'own_embodied_ansar':
                $data = CustomQuery::getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'retire_ansar':
                $data = CustomQuery::getTotalRetireAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'rest_ansar':
                $data = CustomQuery::getTotalRestAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'freezed_ansar':
                $data = CustomQuery::getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'blocked_ansar':
                $data = CustomQuery::getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'blacked_ansar':
                $data = CustomQuery::getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'offerred_ansar':
                $data = CustomQuery::getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
            case 'offer_block':
                $data = CustomQuery::getTotalOfferBlockAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank, $q);
                break;
        }
        if ($request->exists('export')) {
            //export data
            return $this->exportData($data['ansars'], $type);

        }
        return Response::json($data);
    }

    public function showAnsarForServiceEnded($count)
    {
        $pages = ceil($count / 10);
        return View::make('HRM::Dashboard.ansar_service_ended_list')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public function serviceEndedInfoDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $q = Input::get('q');
        $division = Input::get('division');
        $interval = Input::get('interval');
//        return $offset;
        $rules = [
            'view' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'interval' => 'numeric|regex:/^[0-9]+$/',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        $data =  CustomQuery::ansarListForServiceEnded($offset, $limit, $unit, $thana, $division, $interval, $q);
        if($request->exists('export')){
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data,'HRM::export.selected_service_ended_ansar_list');
        }
        return Response::json($data);
    }

    public function showAnsarForReachedFifty($count)
    {
        $pages = ceil(intval($count) / 10);
        return View::make('HRM::Dashboard.ansar_fifty_age_list')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public function ansarReachedFiftyDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $q = Input::get('q');
        $division = Input::get('division');
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        $data =  CustomQuery::ansarListWithFiftyYears($offset, $limit, $unit, $thana, $division, $q);
        if($request->exists('export')){
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data,'HRM::export.ansar_fifty_age_report');
        }

        return Response::json($data);
    }

    public
    function showAnsarForNotInterested($count)
    {
        $pages = ceil($count / 10);
        return View::make('HRM::Dashboard.ansar_not_interested')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public
    function notInterestedInfoDetails()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $q = Input::get('q');
        $division = Input::get('division');
        $rules = [
            'view' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        return CustomQuery::ansarListForNotInterested($offset, $limit, $unit, $thana, $division, $q);
    }

    public
    function getTotalAnsar(Request $request)
    {
//        return "pppp";
        DB::enableQueryLog();
        $allStatus = array(
            'totalAnsar' => DB::table('tbl_ansar_parsonal_info'),
            'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('black_list_status', 0),
            'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0),
            'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0),
            'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->join('tbl_units', 'tbl_sms_offer_info.district_id', '=', 'tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0),
            'totalOfferedReceived' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_receive_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')->join('tbl_units', 'tbl_sms_receive_info.offered_district', '=', 'tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0),
            //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereIn('tbl_sms_offer_info.district_id', $unit)->count(),
            'totalEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0),
            'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1),
            'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1),
            'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0),
            'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1),
            'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1),
            'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0),
            'totalRetire' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('retierment_status', 1)->where('block_list_status', 0),
            'totalOfferBlock' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('offer_block_status', 1)->where('block_list_status', 0),
        );
        if ($request->division_id) {
            $allStatus['totalAnsar']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalNotVerified']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalFree']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            /*if((Auth::user()->type==11||Auth::user()->type==33))*/
            $allStatus['totalPanel']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalOffered']->where('tbl_units.division_id', $request->division_id);
            $allStatus['totalOfferedReceived']->where('tbl_units.division_id', $request->division_id);
            $allStatus['totalEmbodied']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['totalEmbodiedOwn']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['totalEmbodiedDiff']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalFreeze']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalBlockList']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalBlackList']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalRest']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalRetire']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalOfferBlock']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
        }
        if ($request->unit_id) {
            $allStatus['totalAnsar']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalNotVerified']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalFree']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            /*if((Auth::user()->type==11||Auth::user()->type==33))*/
            $allStatus['totalPanel']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalOffered']->where('tbl_units.id', $request->unit_id);
            $allStatus['totalOfferedReceived']->where('tbl_units.id', $request->unit_id);
            $allStatus['totalEmbodied']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['totalEmbodiedOwn']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['totalEmbodiedDiff']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalFreeze']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalBlockList']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalBlackList']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalRest']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalRetire']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalOfferBlock']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
        }

        $results = [];
        foreach ($allStatus as $key => $q) {
            $results[$key] = $q->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
        }
        return Response::json($results);
    }

    function updateGlobalParameter()
    {
        $rules = [
            'id' => 'required|numeric',
            'pv' => 'required|numeric|regex:/^[0-9]+$/',
            'pu' => 'regex:/^[a-zA-Z]+$/',
            'pd' => 'regex:/^[a-zA-Z\s]+$/',
            'pp' => 'numeric|regex:/^[0-9]+$/'
        ];
        $messages = [
            'pv.required' => 'Parameter value is required',
            'pv.numeric' => 'Parameter value must be numeric.eg 1,2..',
            'pv.regex' => 'Parameter value must be numeric.eg 1,2..',
            'pp.numeric' => 'Parameter unit must be numeric.eg 1,2..',
            'pp.regex' => 'Parameter unit must be numeric.eg 1,2..',
            'pu.regex' => 'Parameter unit is invalid',
            'pd.regex' => 'Parameter description only contain a-z,A-Z and space',
        ];
        $valid = Validator::make(Input::all(), $rules, $messages);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        $id = Input::get('id');
        $pv = Input::get('pv');
        $pd = Input::get('pd');
        $pp = Input::get('pp');
        $pu = Input::get('pu');
        DB::beginTransaction();
        try {
            $gp = GlobalParameter::find($id);
            $gp->param_value = $pv;
            $gp->param_description = $pd;
            $gp->param_piority = $pp;
            $gp->param_unit = $pu;
            $gp->save();
            DB::commit();
        } catch (Exception $e) {
            return Response::json(['status' => false, 'data' => 'Unable to update. try again later']);
        }

        return Response::json(['status' => true, 'data' => 'Update complete successfully']);
    }

    function globalParameterView()
    {
        return View::make('HRM::global_perameter')->with('gp', GlobalParameter::all());
    }

    function getTemplate($key)
    {
        return View::make('HRM::Partial_view.' . $key . '_list');
    }

    function ansarAcceptOfferLastFiveDays(Request $request)
    {
//        return $request->all();
        $rules = [
            'division' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'unit' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'thana' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'rank' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'sex' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'offset' => 'numeric',
            'limit' => 'numeric',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 422, ['Content-Type' => 'application/json']);
        }
        $result = CustomQuery::ansarAcceptOfferLastFiveDays($request->division, $request->unit, $request->thana, $request->rank, $request->sex, $request->offset, $request->limit, $request->q, $request->type);
        if ($result === false) {
            return response("Invalid Request", 400, ['Content-Type' => 'text/html']);
        }
        return $result;
    }

    function getAnsarInfoinExcel()
    {
        return View::make('HRM::Entryform.ansar_info_excel');
    }

    function generateAnsarInfoExcel(Request $request)
    {
        $ansar_ids = explode(",", $request->ansar_ids);
        $data = DB::table(DB::raw('(SELECT @i:=0) as i,tbl_ansar_parsonal_info'))
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->whereIn('tbl_ansar_parsonal_info.ansar_id', $ansar_ids)
            ->select(DB::raw('@i:=@i+1 sl_no'), 'tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng',
                'tbl_ansar_parsonal_info.father_name_bng', 'tbl_designations.name_bng', 'tbl_ansar_parsonal_info.data_of_birth',
                'tbl_ansar_parsonal_info.village_name_bng', 'tbl_ansar_parsonal_info.union_name_bng',
                'tbl_ansar_parsonal_info.post_office_name_bng', 'tbl_thana.thana_name_bng',
                'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.mobile_no_self')->get();
//        $data = collect($data)->toArray();

        $d = array();
        foreach ($data as $dd) {
            array_push($d, collect($dd)->toArray());
        }
//                var_dump($d);die;
        Excel::create('test', function ($excel) use ($d) {
            $excel->sheet('sheet1', function ($sheet) use ($d) {
                $sheet->fromArray($d);
            });
        })->download('xls');
//        return $data;
    }

    function systemSettingIndex()
    {

        $system_setting = SystemSetting::all();
        return view("HRM::system_setting", ['data' => $system_setting]);

    }

    function systemSettingUpdate(Request $request, $id)
    {
        $data = [
            'setting_value' => $request->exists('values') ? implode(',', $request->values) : null,
            'active' => $request->exists('active') ? $request->active : 0,
            'description' => $request->description
        ];
        DB::beginTransaction();
        try {
            SystemSetting::find($id)->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getTraceAsString());
            return redirect()->route('system_setting')->with('error', $e->getMessage());
        }
        return redirect()->route('system_setting')->with('success', "Setting updated successfully");

    }

    function systemSettingEdit(Request $request, $id)
    {

        $system_setting = SystemSetting::find($id);
        $units = District::where('id', '<>', 0)->get();
        return view("HRM::system_setting_edit", ['data' => $system_setting, 'units' => $units]);

    }
}
