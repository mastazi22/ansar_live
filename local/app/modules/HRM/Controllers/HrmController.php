<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\GlobalParameter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class HrmController extends Controller
{
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
        if (Input::exists('division_id')) {
            $tseity = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->where('tbl_embodiment.emboded_status', 'Emboded')
                ->where('tbl_ansar_status_info.block_list_status', 0)
                ->where('tbl_ansar_status_info.black_list_status', 0)
                ->where('tbl_kpi_info.division_id', Input::get('division_id'))
                ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')->count('tbl_embodiment.ansar_id');
            $arfyoa = DB::table("tbl_ansar_parsonal_info")->where('division_id', Input::get('division_id'))->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50)->count('tbl_ansar_parsonal_info.ansar_id');
            $tnimutt = DB::table('tbl_sms_offer_info')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                ->where('tbl_ansar_parsonal_info.division_id', Input::get('division_id'))
                ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id')
                ->get();
            $i = 0;
            $uiui = array();
            foreach ($tnimutt as $tttt) {

                $uiui[$i] = $tttt->ansar_id;
                $i++;
            }
//            return $tseity;
        } else if (Input::exists('district_id')) {
            $tseity = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->where('tbl_embodiment.emboded_status', 'Emboded')
                ->where('tbl_ansar_status_info.block_list_status', 0)
                ->where('tbl_ansar_status_info.black_list_status', 0)
                ->where('tbl_kpi_info.unit_id', Input::get('district_id'))
                ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')->count('tbl_embodiment.ansar_id');
            $arfyoa = DB::table("tbl_ansar_parsonal_info")->where('unit_id', Input::get('district_id'))->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50)->count('tbl_ansar_parsonal_info.ansar_id');
            $tnimutt = DB::table('tbl_sms_offer_info')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                ->where('tbl_ansar_parsonal_info.unit_id', Input::get('district_id'))
                ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id')
                ->get();
//            return $tseity;
            $i = 0;
            $uiui = array();
            foreach ($tnimutt as $tttt) {

                $uiui[$i] = $tttt->ansar_id;
                $i++;
            }
        } else {
            $tseity = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->where('tbl_embodiment.emboded_status', 'Emboded')
                ->where('tbl_ansar_status_info.block_list_status', 0)
                ->where('tbl_ansar_status_info.black_list_status', 0)
                ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')->count('tbl_embodiment.ansar_id');
            $arfyoa = DB::table("tbl_ansar_parsonal_info")->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50)->count('tbl_ansar_parsonal_info.ansar_id');
            $tnimutt = DB::table('tbl_sms_offer_info')
                ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id')
                ->get();
            //return $tnimutt;
            $i = 0;
            $uiui = array();
            foreach ($tnimutt as $tttt) {

                $uiui[$i] = $tttt->ansar_id;
                $i++;
            }
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

    public function graphEmbodiment()
    {

        $embodied_ansars = DB::select(DB::raw('SELECT count(ansar_id) as total,DATE_FORMAT(joining_date,"%b,%y") as month FROM tbl_embodiment_log WHERE joining_date BETWEEN DATE_SUB(NOW(),INTERVAL 1 YEAR) AND NOW() GROUP BY MONTH(joining_date) ORDER BY YEAR(joining_date) ASC,MONTH(joining_date) ASC'));
        $disembodied_ansars = DB::select(DB::raw('SELECT count(ansar_id) as total,DATE_FORMAT(release_date,"%b,%y") as month FROM tbl_embodiment_log WHERE release_date BETWEEN DATE_SUB(NOW(),INTERVAL 1 YEAR) AND NOW() GROUP BY MONTH(release_date)'));
//        return $ansars;
        return Response::json(["ea" => $embodied_ansars, 'da' => $disembodied_ansars]);
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
            'recentPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->join('tbl_units','tbl_sms_offer_info.district_id','=','tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereIn('tbl_sms_offer_info.district_id', $unit)->count(),
            'recentEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
        );
        if($request->division_id){
            $allStatus['recentAnsar']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['recentNotVerified']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['recentFree']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['recentPanel']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['recentOffered']->where('tbl_units.division_id',$request->division_id);
            $allStatus['recentEmbodied']->where('tbl_kpi_info.division_id',$request->division_id);
            $allStatus['recentEmbodiedOwn']->where('tbl_kpi_info.division_id',$request->division_id);
            $allStatus['recentEmbodiedDiff']->where('tbl_kpi_info.division_id',$request->division_id);
            $allStatus['recentFreeze']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['recentBlockList']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['recentBlackList']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['recentRest']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
        }
        if($request->unit_id){
            $allStatus['recentAnsar']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['recentNotVerified']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['recentFree']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['recentPanel']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['recentOffered']->where('tbl_units.id',$request->unit_id);
            $allStatus['recentEmbodied']->where('tbl_kpi_info.unit_id',$request->unit_id);
            $allStatus['recentEmbodiedOwn']->where('tbl_kpi_info.unit_id',$request->unit_id);
            $allStatus['recentEmbodiedDiff']->where('tbl_kpi_info.unit_id',$request->unit_id);
            $allStatus['recentFreeze']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['recentBlockList']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['recentBlackList']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['recentRest']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
        }
//        if (Input::exists('division_id')) {
//            $units = District::where('division_id', Input::get('division_id'))->select('id')->get();
//            $unit = [];
//            foreach ($units as $u) array_push($unit, $u->id);
//            $recentStatus = array(
//                'recentAnsar' => DB::table('tbl_ansar_parsonal_info')->where('division_id', Input::get('division_id'))->whereBetween('created_at', array($backTime, $recentTime))->count('ansar_id'),
//                'recentNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('division_id', Input::get('district_id'))->whereBetween('tbl_ansar_parsonal_info.updated_at', array($backTime, $recentTime))->select('ansar_id')->count(),
//                'recentFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_status_info.ansar_id'),
//                //'recentReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->whereBetween('tbl_sms_receive_info.created_at', array($backTime, $recentTime))->count(),
//                'recentEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 0)->where('embodied_status', 1)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_embodiment.ansar_id'),
//                'recentFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//            );
//        }
//        else if (Input::exists('district_id')) {
//
//            $recentStatus = array(
//                'recentAnsar' => DB::table('tbl_ansar_parsonal_info')->where('unit_id', Input::get('district_id'))->whereBetween('created_at', array($backTime, $recentTime))->count('ansar_id'),
//                'recentNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_parsonal_info.updated_at', array($backTime, $recentTime))->select('ansar_id')->count(),
//                'recentFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_status_info.ansar_id'),
//                //'recentReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->whereBetween('tbl_sms_receive_info.created_at', array($backTime, $recentTime))->count(),
//                'recentEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 0)->where('embodied_status', 1)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_embodiment.ansar_id'),
//                'recentFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'recentRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//            );
//        }
//        else {
//            $recentStatus = array(
//                'recentAnsar' => DB::table('tbl_ansar_parsonal_info')->whereBetween('created_at', array($backTime, $recentTime))->count('ansar_id'),
//                'recentNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->whereBetween('tbl_ansar_parsonal_info.updated_at', array($backTime, $recentTime))->select('ansar_id')->count(),
//                'recentFree' => DB::table('tbl_ansar_status_info')->where('free_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
//                'recentPanel' => DB::table('tbl_ansar_status_info')->where('pannel_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
//                'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_status_info.ansar_id'),
//                //'recentReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereBetween('tbl_sms_receive_info.created_at', array($backTime, $recentTime))->count(),
//                'recentEmbodied' => DB::table('tbl_ansar_status_info')->where('embodied_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
//                'recentFreeze' => DB::table('tbl_ansar_status_info')->where('freezing_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
//                'recentBlockList' => DB::table('tbl_ansar_status_info')->where('block_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
//                'recentBlackList' => DB::table('tbl_ansar_status_info')->where('black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
//                'recentRest' => DB::table('tbl_ansar_status_info')->where('rest_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
//            );
//        }
        $results = [];
        foreach($allStatus as $key=>$q){
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
        }
        return View::make('HRM::Dashboard.view_recent_ansar_list')->with(['type' => $type, 'pageTitle' => $pageTitle]);
    }

    public function offerAcceptLastFiveDays()
    {
        return view('HRM::Dashboard.offer_accept_last_5_days');
    }

    public function getAnsarList()
    {
        $type = Input::get('type');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $rank = Input::get('rank');
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
        switch ($type) {
            case 'all_ansar':
                return CustomQuery::getAllAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'not_verified_ansar':
                return CustomQuery::getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'free_ansar':
                return CustomQuery::getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'paneled_ansar':
                return CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'embodied_ansar':
                return CustomQuery::getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'rest_ansar':
                return CustomQuery::getTotalRestAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'freezed_ansar':
                return CustomQuery::getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'blocked_ansar':
                return CustomQuery::getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'blacked_ansar':
                return CustomQuery::getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'offerred_ansar':
                return CustomQuery::getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'own_embodied_ansar':
                return CustomQuery::getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
            case 'embodied_ansar_in_different_district':
                return CustomQuery::getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank,$q);
        }
    }

    public function getRecentAnsarList()
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
                return CustomQuery::getAllAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'not_verified_ansar':
                return CustomQuery::getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'free_ansar':
                return CustomQuery::getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'paneled_ansar':
                return CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'embodied_ansar':
                return CustomQuery::getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'embodied_ansar_in_different_district':
                return CustomQuery::getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'own_embodied_ansar':
                return CustomQuery::getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'rest_ansar':
                return CustomQuery::getTotalRestAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'freezed_ansar':
                return CustomQuery::getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'blocked_ansar':
                return CustomQuery::getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'blacked_ansar':
                return CustomQuery::getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
            case 'offerred_ansar':
                return CustomQuery::getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::RECENT, $rank,$q);
        }
    }

    public function showAnsarForServiceEnded($count)
    {
        $pages = ceil($count / 10);
        return View::make('HRM::Dashboard.ansar_service_ended_list')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public function serviceEndedInfoDetails()
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
        return CustomQuery::ansarListForServiceEnded($offset, $limit, $unit, $thana, $division, $interval,$q);
    }

    public function showAnsarForReachedFifty($count)
    {
        $pages = ceil($count / 10);
        return View::make('HRM::Dashboard.ansar_fifty_age_list')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public function ansarReachedFiftyDetails()
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
        return CustomQuery::ansarListWithFiftyYears($offset, $limit, $unit, $thana, $division,$q);
    }

    public function showAnsarForNotInterested($count)
    {
        $pages = ceil($count / 10);
        return View::make('HRM::Dashboard.ansar_not_interested')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public function notInterestedInfoDetails()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $view = Input::get('view');
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
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::ansarListForNotInterested($offset, $limit, $unit, $thana, $division);
        } else {
            return CustomQuery::getansarForNotInterestedCount($unit, $thana, $division);
        }
    }

    public function getTotalAnsar(Request $request)
    {
//        return "pppp";
        DB::enableQueryLog();
        $allStatus = array(
            'totalAnsar' => DB::table('tbl_ansar_parsonal_info'),
            'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0),
            'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0),
            'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0),
            'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->join('tbl_units','tbl_sms_offer_info.district_id','=','tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0),
            //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereIn('tbl_sms_offer_info.district_id', $unit)->count(),
            'totalEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0),
            'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1),
            'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1),
            'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0),
            'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1),
            'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1),
            'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0),
        );
        if($request->division_id){
            $allStatus['totalAnsar']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['totalNotVerified']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['totalFree']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['totalPanel']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['totalOffered']->where('tbl_units.division_id',$request->division_id);
            $allStatus['totalEmbodied']->where('tbl_kpi_info.division_id',$request->division_id);
            $allStatus['totalEmbodiedOwn']->where('tbl_kpi_info.division_id',$request->division_id);
            $allStatus['totalEmbodiedDiff']->where('tbl_kpi_info.division_id',$request->division_id);
            $allStatus['totalFreeze']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['totalBlockList']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['totalBlackList']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
            $allStatus['totalRest']->where('tbl_ansar_parsonal_info.division_id',$request->division_id);
        }
        if($request->unit_id){
            $allStatus['totalAnsar']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['totalNotVerified']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['totalFree']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['totalPanel']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['totalOffered']->where('tbl_units.id',$request->unit_id);
            $allStatus['totalEmbodied']->where('tbl_kpi_info.unit_id',$request->unit_id);
            $allStatus['totalEmbodiedOwn']->where('tbl_kpi_info.unit_id',$request->unit_id);
            $allStatus['totalEmbodiedDiff']->where('tbl_kpi_info.unit_id',$request->unit_id);
            $allStatus['totalFreeze']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['totalBlockList']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['totalBlackList']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
            $allStatus['totalRest']->where('tbl_ansar_parsonal_info.unit_id',$request->unit_id);
        }

//        $p = DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('tbl_ansar_status_info.block_list_status', 0)->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_kpi_info.unit_id', 9)->distinct()->count('tbl_embodiment.ansar_id');
//        return $p;
//        if (Input::exists('division_id')) {
//            $units = District::where('division_id', Input::get('division_id'))->select('id')->get();
//            $unit = [];
//            foreach ($units as $u) array_push($unit, $u->id);
//            $allStatus = array(
//                'totalAnsar' => DB::table('tbl_ansar_parsonal_info')->where('division_id', Input::get('division_id'))->count('ansar_id'),
//                'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('tbl_ansar_parsonal_info.division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0)->whereIn('tbl_sms_offer_info.district_id', $unit)->distinct()->count('tbl_ansar_status_info.ansar_id'),
//                //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereIn('tbl_sms_offer_info.district_id', $unit)->count(),
//                'totalEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('embodied_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.division_id', Input::get('division_id'))->distinct()->count('tbl_embodiment.ansar_id'),
//                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.division_id','!=',Input::get('division_id'))->distinct()->count('tbl_embodiment.ansar_id'),
//                'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//            );
//        } else if (Input::exists('district_id')) {
//            $allStatus = array(
//                'totalAnsar' => DB::table('tbl_ansar_parsonal_info')->where('unit_id', Input::get('district_id'))->count('ansar_id'),
//                'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->distinct()->count('tbl_ansar_status_info.ansar_id'),
//                //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->count(),
//                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 0)->where('embodied_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.unit_id', Input::get('district_id'))->distinct()->count('tbl_embodiment.ansar_id'),
////                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.unit_id','!=', Input::get('district_id'))->distinct()->count('tbl_embodiment.ansar_id'),
//                'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 0)->where('rest_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//            );
//        }
//        else {
//            $allStatus = array(
//                'totalAnsar' => $totalAnsar = DB::table('tbl_ansar_parsonal_info')->distinct()->count('ansar_id'),
//                'totalNotVerified' => $notVerified = DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
//                'totalFree' => $totalFreeStatus = AnsarStatusInfo::where('free_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
//                'totalPanel' => $totalpPanel = AnsarStatusInfo::where('pannel_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
//                'totalOffered' => $totalOfferred = AnsarStatusInfo::where('offer_sms_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
//                //'offerReceived' => $totalOfferedStatus = ReceiveSMSModel::where('sms_status', 'ACCEPTED')->count(),
//                'totalEmbodied' => $totalEmbodied = AnsarStatusInfo::where('embodied_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
//                'totalFreeze' => $totalFreeze = AnsarStatusInfo::where('freezing_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
//                'totalBlockList' => $totalBlockList = AnsarStatusInfo::where('block_list_status', 1)->distinct()->count('ansar_id'),
//                'totalBlackList' => $totalBlackList = AnsarStatusInfo::where('black_list_status', 1)->distinct()->count('ansar_id'),
//                'totalRest' => $totalBlackList = AnsarStatusInfo::where('rest_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
//            );
//        }
//        return DB::getQueryLog();
        $results = [];
        foreach($allStatus as $key=>$q){
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
            'division' => ['required','regex:/^(all)||[0-9]+$/'],
            'unit' => ['required','regex:/^(all)||[0-9]+$/'],
            'thana' => ['required','regex:/^(all)||[0-9]+$/'],
            'rank' => ['required','regex:/^(all)||[0-9]+$/'],
            'sex' => ['required','regex:/^(all)||[0-9]+$/'],
            'offset'=>'numeric',
            'limit'=>'numeric',
        ];
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return response($valid->messages()->toJson(),422,['Content-Type'=>'application/json']);
        }
        $result = CustomQuery::ansarAcceptOfferLastFiveDays($request->division,$request->unit,$request->thana,$request->rank,$request->sex,$request->offset,$request->limit,$request->q,$request->type);
        if($result===false){
            return response("Invalid Request",400,['Content-Type'=>'text/html']);
        }
        return $result;
    }
}
