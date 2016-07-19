<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\ForgetPasswordRequest;
use App\modules\HRM\Models\GlobalParameter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
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
                ->join('tbl_ansar_parsonal_info','tbl_ansar_parsonal_info.ansar_id','=','tbl_sms_offer_info.ansar_id')
                ->where('tbl_ansar_parsonal_info.division_id', Input::get('division_id'))
                ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id')
                ->get();
            $i=0;
            $uiui=array();
            foreach($tnimutt as $tttt){

                $uiui[$i]=$tttt->ansar_id;
                $i++;
            }
//            return $tseity;
        }
        else if (Input::exists('district_id')) {
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
                ->join('tbl_ansar_parsonal_info','tbl_ansar_parsonal_info.ansar_id','=','tbl_sms_offer_info.ansar_id')
                ->where('tbl_ansar_parsonal_info.unit_id', Input::get('district_id'))
                ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id')
                ->get();
//            return $tseity;
            $i=0;
            $uiui=array();
            foreach($tnimutt as $tttt){

                $uiui[$i]=$tttt->ansar_id;
                $i++;
            }
        }
        else{
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
            $i=0;
            $uiui=array();
            foreach($tnimutt as $tttt){

                $uiui[$i]=$tttt->ansar_id;
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
        return Response::json(["ea"=>$embodied_ansars,'da'=>$disembodied_ansars]);
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

    public function getRecentAnsar()
    {

        $recentTime = Carbon::now();
        $backTime = Carbon::now()->subDays(7);
        if (Input::exists('division_id')) {
            $units = District::where('division_id', Input::get('division_id'))->select('id')->get();
            $unit = [];
            foreach ($units as $u) array_push($unit, $u->id);
            $recentStatus = array(
                'recentAnsar' => DB::table('tbl_ansar_parsonal_info')->where('division_id', Input::get('division_id'))->whereBetween('created_at', array($backTime, $recentTime))->count('ansar_id'),
                'recentNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('division_id', Input::get('district_id'))->whereBetween('tbl_ansar_parsonal_info.updated_at', array($backTime, $recentTime))->select('ansar_id')->count(),
                'recentFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_status_info.ansar_id'),
                //'recentReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->whereBetween('tbl_sms_receive_info.created_at', array($backTime, $recentTime))->count(),
                'recentEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('embodied_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
            );
        } else if (Input::exists('district_id')) {

            $recentStatus = array(
                'recentAnsar' => DB::table('tbl_ansar_parsonal_info')->where('unit_id', Input::get('district_id'))->whereBetween('created_at', array($backTime, $recentTime))->count('ansar_id'),
                'recentNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_parsonal_info.updated_at', array($backTime, $recentTime))->select('ansar_id')->count(),
                'recentFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_status_info.ansar_id'),
                //'recentReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->whereBetween('tbl_sms_receive_info.created_at', array($backTime, $recentTime))->count(),
                'recentEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('embodied_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'recentRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
            );
        } else {
            $recentStatus = array(
                'recentAnsar' => DB::table('tbl_ansar_parsonal_info')->whereBetween('created_at', array($backTime, $recentTime))->count('ansar_id'),
                'recentNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->whereBetween('tbl_ansar_parsonal_info.updated_at', array($backTime, $recentTime))->select('ansar_id')->count(),
                'recentFree' => DB::table('tbl_ansar_status_info')->where('free_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
                'recentPanel' => DB::table('tbl_ansar_status_info')->where('pannel_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
                'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('tbl_ansar_status_info.ansar_id'),
                //'recentReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereBetween('tbl_sms_receive_info.created_at', array($backTime, $recentTime))->count(),
                'recentEmbodied' => DB::table('tbl_ansar_status_info')->where('embodied_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
                'recentFreeze' => DB::table('tbl_ansar_status_info')->where('freezing_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
                'recentBlockList' => DB::table('tbl_ansar_status_info')->where('block_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
                'recentBlackList' => DB::table('tbl_ansar_status_info')->where('black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
                'recentRest' => DB::table('tbl_ansar_status_info')->where('rest_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime))->distinct()->count('ansar_id'),
            );
        }
        return Response::json($recentStatus);
    }

    public function showAnsarList($type)
    {
        return View::make('HRM::Dashboard.view_ansar_list')->with(['type' => $type]);
    }

    public function showRecentAnsarList($type)
    {
        return View::make('HRM::Dashboard.view_recent_ansar_list')->with(['type' => $type]);
    }

    public function getAnsarList()
    {
        $type = Input::get('type');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $view = Input::get('view');
        if (strcasecmp($view, 'view') == 0) {
            switch ($type) {
                case 'all_ansar':
                    return CustomQuery::getAllAnsarList($offset, $limit, $unit, $thana, $division);
                case 'not_verified_ansar':
                    return CustomQuery::getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'free_ansar':
                    return CustomQuery::getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division);
                case 'paneled_ansar':
                    return CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division);
                case 'embodied_ansar':
                    return CustomQuery::getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'rest_ansar':
                    return CustomQuery::getTotalRestAnsarList($offset, $limit, $unit, $thana, $division);
                case 'freezed_ansar':
                    return CustomQuery::getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'blocked_ansar':
                    return CustomQuery::getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'blacked_ansar':
                    return CustomQuery::getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'offerred_ansar':
                    return CustomQuery::getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'own_embodied_ansar':
                    return CustomQuery::getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'embodied_ansar_in_different_district':
                    return CustomQuery::getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division);
            }
        } else {
            switch ($type) {
                case 'all_ansar':
                    return CustomQuery::getAllAnsarCount($unit, $thana, $division);
                case 'not_verified_ansar':
                    return CustomQuery::getTotalNotVerifiedAnsarCount($unit, $thana, $division);
                case 'free_ansar':
                    return CustomQuery::getTotalFreeAnsarCount($unit, $thana, $division);
                case 'paneled_ansar':
                    return CustomQuery::getTotalPaneledAnsarCount($unit, $thana, $division);
                case 'embodied_ansar':
                    return CustomQuery::getTotalEmbodiedAnsarCount($unit, $thana, $division);
                case 'rest_ansar':
                    return CustomQuery::getTotalRestAnsarCount($unit, $thana, $division);
                case 'freezed_ansar':
                    return CustomQuery::getTotalFreezedAnsarCount($unit, $thana, $division);
                case 'blocked_ansar':
                    return CustomQuery::getTotalBlockedAnsarCount($unit, $thana, $division);
                case 'blacked_ansar':
                    return CustomQuery::getTotalBlackedAnsarCount($unit, $thana, $division);
                case 'offerred_ansar':
                    return CustomQuery::getTotalOfferedAnsarCount($unit, $thana, $division);
                case 'own_embodied_ansar':
                    return CustomQuery::getTotalOwnEmbodiedAnsarCount($unit, $thana, $division);
                case 'embodied_ansar_in_different_district':
                    return CustomQuery::getTotalDiffEmbodiedAnsarCount($unit, $thana, $division);
            }
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
        $division = Input::get('division');
        if (strcasecmp($view, 'view') == 0) {
            switch ($type) {
                case 'all_ansar':
                    return CustomQuery::getAllRecentAnsarList($offset, $limit, $unit, $thana, $division);
                case 'not_verified_ansar':
                    return CustomQuery::getRecentTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'free_ansar':
                    return CustomQuery::getRecentTotalFreeAnsarList($offset, $limit, $unit, $thana, $division);
                case 'paneled_ansar':
                    return CustomQuery::getRecentTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division);
                case 'embodied_ansar':
                    return CustomQuery::getRecentTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'diff_embodied_ansar':
                    return CustomQuery::getRecentTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'rest_ansar':
                    return CustomQuery::getRecentTotalRestAnsarList($offset, $limit, $unit, $thana, $division);
                case 'freezed_ansar':
                    return CustomQuery::getRecentTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'blocked_ansar':
                    return CustomQuery::getRecentTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'blacked_ansar':
                    return CustomQuery::getRecentTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division);
                case 'offerred_ansar':
                    return CustomQuery::getRecentTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division);
            }
        } else {
            switch ($type) {
                case 'all_ansar':
                    return CustomQuery::getAllRecentAnsarCount($unit, $thana, $division);
                case 'not_verified_ansar':
                    return CustomQuery::getRecentTotalNotVerifiedAnsarCount($unit, $thana, $division);
                case 'free_ansar':
                    return CustomQuery::getRecentTotalFreeAnsarCount($unit, $thana, $division);
                case 'paneled_ansar':
                    return CustomQuery::getRecentTotalPaneledAnsarCount($unit, $thana, $division);
                case 'embodied_ansar':
                    return CustomQuery::getRecentTotalEmbodiedAnsarCount($unit, $thana, $division);
                case 'diff_embodied_ansar':
                    return CustomQuery::getRecentTotalDiffEmbodiedAnsarCount($unit, $thana, $division);
                case 'rest_ansar':
                    return CustomQuery::getRecentTotalRestAnsarCount($unit, $thana, $division);
                case 'freezed_ansar':
                    return CustomQuery::getRecentTotalFreezedAnsarCount($unit, $thana, $division);
                case 'blocked_ansar':
                    return CustomQuery::getRecentTotalBlockedAnsarCount($unit, $thana, $division);
                case 'blacked_ansar':
                    return CustomQuery::getRecentTotalBlackedAnsarCount($unit, $thana, $division);
                case 'offerred_ansar':
                    return CustomQuery::getRecentTotalOfferedAnsarCount($unit, $thana, $division);
            }
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
        $view = Input::get('view');
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::ansarListForServiceEnded($offset, $limit, $unit, $thana);
        } else {
            return CustomQuery::ansarListForServiceEndedCount($unit, $thana);
        }
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
        $view = Input::get('view');
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::ansarListWithFiftyYears($offset, $limit, $unit, $thana);
        } else {
            return CustomQuery::getansarWithFiftyYearsCount($unit, $thana);
        }
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
        if (strcasecmp($view, 'view') == 0) {
            return CustomQuery::ansarListForNotInterested($offset, $limit, $unit, $thana);
        } else {
            return CustomQuery::getansarForNotInterestedCount($unit, $thana);
        }
    }
    public function getTotalAnsar()
    {
//        return "pppp";
        DB::enableQueryLog();
//        $p = DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('tbl_ansar_status_info.block_list_status', 0)->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_kpi_info.unit_id', 9)->distinct()->count('tbl_embodiment.ansar_id');
//        return $p;
        if (Input::exists('division_id')) {
            $units = District::where('division_id', Input::get('division_id'))->select('id')->get();
            $unit = [];
            foreach ($units as $u) array_push($unit, $u->id);
            $allStatus = array(
                'totalAnsar' => DB::table('tbl_ansar_parsonal_info')->where('division_id', Input::get('division_id'))->count('ansar_id'),
                'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('tbl_ansar_parsonal_info.division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0)->whereIn('tbl_sms_offer_info.district_id', $unit)->distinct()->count('tbl_ansar_status_info.ansar_id'),
                //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereIn('tbl_sms_offer_info.district_id', $unit)->count(),
                'totalEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('embodied_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereIn('tbl_kpi_info.unit_id', $unit)->distinct()->count('tbl_embodiment.ansar_id'),
                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereIn('tbl_kpi_info.unit_id','!=', $unit)->distinct()->count('tbl_embodiment.ansar_id'),
                'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
            );
        } else if (Input::exists('district_id')) {
            $allStatus = array(
                'totalAnsar' => DB::table('tbl_ansar_parsonal_info')->where('unit_id', Input::get('district_id'))->count('ansar_id'),
                'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->distinct()->count('tbl_ansar_status_info.ansar_id'),
                //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->count(),
                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 0)->where('embodied_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.unit_id', Input::get('district_id'))->distinct()->count('tbl_embodiment.ansar_id'),
//                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.unit_id','!=', Input::get('district_id'))->distinct()->count('tbl_embodiment.ansar_id'),
                'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 0)->where('rest_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
            );
        } else {
            $allStatus = array(
                'totalAnsar' => $totalAnsar = DB::table('tbl_ansar_parsonal_info')->distinct()->count('ansar_id'),
                'totalNotVerified' => $notVerified = DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalFree' => $totalFreeStatus = AnsarStatusInfo::where('free_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                'totalPanel' => $totalpPanel = AnsarStatusInfo::where('pannel_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                'totalOffered' => $totalOfferred = AnsarStatusInfo::where('offer_sms_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                //'offerReceived' => $totalOfferedStatus = ReceiveSMSModel::where('sms_status', 'ACCEPTED')->count(),
                'totalEmbodied' => $totalEmbodied = AnsarStatusInfo::where('embodied_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                'totalFreeze' => $totalFreeze = AnsarStatusInfo::where('freezing_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                'totalBlockList' => $totalBlockList = AnsarStatusInfo::where('block_list_status', 1)->distinct()->count('ansar_id'),
                'totalBlackList' => $totalBlackList = AnsarStatusInfo::where('black_list_status', 1)->distinct()->count('ansar_id'),
                'totalRest' => $totalBlackList = AnsarStatusInfo::where('rest_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
            );
        }
//        return DB::getQueryLog();
        return Response::json($allStatus);
    }
    function updateGlobalParameter()
    {
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
}
