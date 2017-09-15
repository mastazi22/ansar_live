<?php


namespace App\modules\HRM\Models;


use App\Helper\Facades\GlobalParameterFacades;
use App\Helper\Helper;
use App\Helper\QueryHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Services_Twilio;

class CustomQuery
{
    const ALL_TIME = 1;
    const RECENT = 2;
    protected $connection = 'hrm';

    public static function getAnsarInfo($pc = array('male' => 0, 'female' => 0), $apc = array('male' => 0, 'female' => 0), $ansar = array('male' => 0, 'female' => 0), $unit_id = [], $exclude_district = null, $user)
    {
        $ansar_retirement_age = Helper::getAnsarRetirementAge() - 3;
        $pc_apc_retirement_age = Helper::getPcApcRetirementAge() - 3;
        DB::enableQueryLog();
        $query = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_panel_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_division', 'tbl_ansar_parsonal_info.division_id', '=', 'tbl_division.id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_units as pu', 'tbl_ansar_parsonal_info.unit_id', '=', 'pu.id')
            ->where('tbl_panel_info.locked', 0)
            ->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^[0-9]{11}$"')
            ->where('tbl_ansar_status_info.pannel_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if ($user->type == 22) {
            if (in_array($exclude_district, Config::get('app.offer'))) {
                $d = Config::get('app.exclude_district');
                if (isset($d[$exclude_district])) {
                    $query->whereNotIn('pu.id', $d[$exclude_district]);
                } else $query->where('pu.id', '!=', $exclude_district);
            } else {
                $query->join('tbl_units as du', 'tbl_division.id', '=', 'du.division_id')
                    ->where('pu.id', '!=', $exclude_district)->where('du.id', '=', $exclude_district);
            }
        } else if ($user->type == 11 || $user->type == 33 || $user->type == 66) {
            if (is_array($unit_id)) {
                $query = $query->whereIn('pu.id', $unit_id);
            }
        }
        $pc_male = clone $query;
        $pc_female = clone $query;
        $apc_male = clone $query;
        $apc_female = clone $query;
        $ansar_male = clone $query;
        $ansar_female = clone $query;
        $pc_male->where('tbl_ansar_parsonal_info.designation_id', '=', 3)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')->whereRaw('DATEDIFF(NOW(),tbl_ansar_parsonal_info.data_of_birth)/365<' . $pc_apc_retirement_age)
            ->orderBy('tbl_panel_info.panel_date')->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id')
            ->take($pc['male']);
//        return DB::getQueryLog();
        $pc_female->where('tbl_ansar_parsonal_info.designation_id', '=', 3)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')->whereRaw('DATEDIFF(NOW(),tbl_ansar_parsonal_info.data_of_birth)/365<' . $pc_apc_retirement_age)
            ->orderBy('tbl_panel_info.panel_date')->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id')
            ->take($pc['female']);
        $ansar_male->where('tbl_ansar_parsonal_info.designation_id', '=', 1)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')->whereRaw('DATEDIFF(NOW(),tbl_ansar_parsonal_info.data_of_birth)/365<' . $ansar_retirement_age)
            ->orderBy('tbl_panel_info.panel_date')->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id')
            ->take($ansar['male']);
        $ansar_female->where('tbl_ansar_parsonal_info.designation_id', '=', 1)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')->whereRaw('DATEDIFF(NOW(),tbl_ansar_parsonal_info.data_of_birth)/365<' . $ansar_retirement_age)
            ->orderBy('tbl_panel_info.panel_date')->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id')
            ->take($ansar['female']);
        $apc_male->where('tbl_ansar_parsonal_info.designation_id', '=', 2)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')->whereRaw('DATEDIFF(NOW(),tbl_ansar_parsonal_info.data_of_birth)/365<' . $pc_apc_retirement_age)
            ->orderBy('tbl_panel_info.panel_date')->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id')
            ->take($apc['male']);
        $apc_female->where('tbl_ansar_parsonal_info.designation_id', '=', 2)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')->whereRaw('DATEDIFF(NOW(),tbl_ansar_parsonal_info.data_of_birth)/365<' . $pc_apc_retirement_age)
            ->orderBy('tbl_panel_info.panel_date')->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id')->take($apc['female']);

        $b = $pc_male->unionAll($pc_female)->unionAll($apc_male)->unionAll($apc_female)->unionAll($ansar_male)->unionAll($ansar_female)->pluck('ansar_id');
//        return DB::getQueryLog();
        return $b;
    }

    public static function offerQuota($range = 'all')
    {
        DB::enableQueryLog();
        $offered = DB::table('tbl_sms_offer_info')
            ->rightJoin('tbl_units', 'tbl_sms_offer_info.district_id', '=', 'tbl_units.id')
            ->groupBy('tbl_units.id')
            ->select(DB::raw('count(tbl_sms_offer_info.ansar_id) as total_offer_ansar'), 'tbl_units.unit_name_bng as unit_name');
        $offeredr = DB::table('tbl_sms_receive_info')
            ->rightJoin('tbl_units', 'tbl_sms_receive_info.offered_district', '=', 'tbl_units.id')
            ->groupBy('tbl_units.id')
            ->select(DB::raw('count(tbl_sms_receive_info.ansar_id) as total_offer_ansar'), 'tbl_units.unit_name_bng as unit_name');
        $embodied_ansar_total = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->rightJoin('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->groupBy('tbl_units.id')
            ->whereRaw('DATE_SUB(tbl_embodiment.service_ended_date,INTERVAL ' . GlobalParameterFacades::getValue(\App\Helper\GlobalParameter::OFFER_QUOTA_DAY) . ' ' . strtoupper(GlobalParameterFacades::getUnit(\App\Helper\GlobalParameter::OFFER_QUOTA_DAY)) . ') <=NOW() ')
            ->select(DB::raw('count(tbl_embodiment.ansar_id) as total_ansar'), 'tbl_units.unit_name_bng as unit_name');
        $q = DB::table('tbl_embodiment')
            ->rightJoin('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_info.id', '=', 'tbl_kpi_detail_info.kpi_id')
            ->rightJoin('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->where('tbl_kpi_info.status_of_kpi', 1)
            ->groupBy('tbl_kpi_info.id')
            ->select(DB::raw('(tbl_kpi_detail_info.total_ansar_given-COUNT(tbl_embodiment.ansar_id)) as vacency'), 'tbl_units.unit_name_bng as unit_name');
        if (strcasecmp($range, 'all')) {
            $offered->where('tbl_units.division_id', $range);
            $offeredr->where('tbl_units.division_id', $range);
            $embodied_ansar_total->where('tbl_units.division_id', $range);
            $q->where('tbl_units.division_id', $range);
        }
        $vacency = DB::table(DB::raw("(" . $q->toSql() . ") src"))->mergeBindings($q)->select(DB::raw("sum(vacency) as total_ansar"), 'unit_name')->groupBy('unit_name');

        $tqu = DB::table(DB::raw("(" . $offered->unionAll($offeredr)->toSql() . ") src"))->mergeBindings($offered)->select(DB::raw("sum(total_offer_ansar) as total_offered_ansar"), 'unit_name')->groupBy('unit_name')->get();
        $total_offer_quota = DB::table(DB::raw("(" . $embodied_ansar_total->unionAll($vacency)->toSql() . ") src"))->mergeBindings($embodied_ansar_total)->select(DB::raw("sum(total_ansar) as total_ansar"), 'unit_name')->groupBy('unit_name')->get();
//       return DB::getQueryLog();
        //return $total_offer_quota;
        $quota_used = [];
        $total_quota = [];
        foreach ($tqu as $item) {
            $quota_used[$item->unit_name] = $item->total_offered_ansar;
        }
        foreach ($total_offer_quota as $item) {
            $total_quota[$item->unit_name] = $item->total_ansar;
        }
        $offer_quota = [];
//        return $quota_used;
        foreach ($quota_used as $key => $value) {
            //echo $key;
            array_push($offer_quota, ['unit_name_bng' => $key, 'total_quota' => isset($total_quota[$key]) ? $total_quota[$key] : 0, 'quota_used' => $value]);
        }

        return $offer_quota;
    }

    public static function getOfferSMSInfo($district_id)
    {
        $offer_noreply_ansar = DB::table('tbl_sms_offer_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->where('tbl_sms_offer_info.district_id', '=', $district_id)
            ->select('tbl_sms_offer_info.ansar_id', 'tbl_sms_offer_info.sms_send_datetime', 'tbl_sms_offer_info.sms_end_datetime', 'tbl_sms_offer_info.district_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng');
        $offer_accepted_ansar = DB::table('tbl_sms_receive_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->where('tbl_sms_receive_info.offered_district', '=', $district_id)
            ->select('tbl_sms_receive_info.ansar_id', 'tbl_sms_receive_info.sms_send_datetime', 'tbl_sms_receive_info.sms_end_datetime', 'tbl_sms_receive_info.offered_district', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng');

        return $offer_noreply_ansar->unionAll($offer_accepted_ansar)->get();
    }


    public static function getUserInformation($limit, $offset, $q)
    {
        $t = strtotime(Carbon::now());
        $s = (int)config('session.lifetime');
        $users = DB::connection('hrm')->table('tbl_user')
            ->leftJoin('sessions', 'sessions.user_id', '=', 'tbl_user.id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_user_log', 'tbl_user_log.user_id', '=', 'tbl_user.id')
            ->select(DB::raw(" {$s}-(({$t}-sessions.last_activity)/60)as total_time"), 'tbl_user.id', 'tbl_user.status', 'tbl_user.user_name', 'tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.email', 'tbl_user_log.last_login', 'tbl_user_log.user_status', 'tbl_user.status')
            ->orderBy('tbl_user.user_name');
        if ($q) {
            $users->where('tbl_user.user_name', 'LIKE', "%{$q}%");
        }
        $t = clone $users;
        $total = $t->count();
//        return $total;
//        return $users->skip($offset)->take($limit)->get();
        return ['total' => $total, 'users' => $users->skip($offset)->take($limit)->get()];
    }

    public static function getNotVerifiedChunkAnsar($limit, $offset, $division = null, $unit = null, $thana = null)
    {
        DB::enableQueryLog();
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->where(function ($query) {
                $query->where('tbl_ansar_parsonal_info.verified', 0)->orWhere('tbl_ansar_parsonal_info.verified', 1);
            })->orderBy('tbl_ansar_parsonal_info.ansar_id', 'asc');
        if ($division && $division != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit && $unit != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.unit_id', $unit);
        }
        if ($thana && $thana != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.thana_id', $thana);
        }
        $b = $ansar->skip($offset)->take($limit)
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.sex', 'tbl_units.unit_name_bng', 'tbl_thana.thana_name_bng', 'tbl_designations.name_bng')
            ->get();
//        return DB::getQueryLog();
        return $b;

    }

    public static function getNotVerifiedAnsar($limit, $offset, $sort = "desc", $division = null, $unit = null, $thana = null, $type)
    {
        $user = Auth::user();
        $usertype = $user->type;
        $ansar = [];
        DB::enableQueryLog();
        if ($usertype == 11 || $usertype == 77 || $usertype == 22 || $usertype == 33 || $usertype == 66) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where(function ($q) {
                    $q->where('tbl_ansar_parsonal_info.verified', 0)->orWhere('tbl_ansar_parsonal_info.verified', 1);
                });

//            return $ansar;
        } elseif ($usertype == 44) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 1)->where('tbl_ansar_parsonal_info.ansar_id', '>', GlobalParameterFacades::getValue("last_ansar_id"));
//            return $ansar;
        } elseif ($usertype == 55) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 0)->where('tbl_ansar_parsonal_info.user_id', $user->id);
//            return $ansar;
        } else {
            return false;
        }
        if ($division && $division != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit && $unit != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.unit_id', $unit);
        }
        if ($thana && $thana != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.thana_id', $thana);
        }
        if ($type == 'view') {
            $b = $ansar->skip($offset)->take($limit)->orderBy('tbl_ansar_parsonal_info.ansar_id', $sort)
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.data_of_birth')
                ->get();
            return $b;
        } else {
//            return "asdaasdasdsa";
            $t = $ansar->count();
//            return DB::getQueryLog();
            return ['total' => $t];
        }
//        return DB::getQueryLog();

    }

    public static function getVerifiedAnsar($limit, $offset, $sort = 'desc', $division = null, $unit = null, $thana = null, $type)
    {
        $user = Auth::user();
        $usertype = $user->type;
        $userId = $user->id;
        $ansar = '';
        if ($usertype == 11 || $usertype == 77 || $usertype == 22 || $usertype == 33) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 2);
            //return $ansar;
        } elseif ($usertype == 44) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->join('tbl_user_action_log', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_user_action_log.ansar_id')
                ->where('tbl_user_action_log.to_state', '=', 'FREE')
                ->where('tbl_user_action_log.action_by', '=', $userId)
                ->where('tbl_ansar_parsonal_info.verified', 2)->where('tbl_ansar_parsonal_info.ansar_id', '>', GlobalParameterFacades::getValue("last_ansar_id"));
            // return $ansar;
        } elseif ($usertype == 55) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_user_action_log', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_user_action_log.ansar_id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 1)->where('tbl_user_action_log.action_type', 'VERIFIED')->where('tbl_user_action_log.action_by', $user->id);
            //return $ansar;
        } else {
            return false;
        }
        if ($division && $division != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit && $unit != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.unit_id', $unit);
        }
        if ($thana && $thana != 'all') {
            $ansar->where('tbl_ansar_parsonal_info.thana_id', $thana);
        }
        if ($type == 'view') {
            $b = $ansar->skip($offset)->take($limit)
                ->orderBy('tbl_ansar_parsonal_info.ansar_id', $sort)
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.data_of_birth')
                ->get();
            return $b;
        } else {
//            return "asdaasdasdsa";
            $t = $ansar->count();
//            return DB::getQueryLog();
            return ['total' => $t];
        }
    }

    public static function getEmbodiedAnsar($kpi_id)
    {
//        DB::enableQueryLog();
        $ansars = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_division', 'tbl_ansar_parsonal_info.division_id', '=', 'tbl_division.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.kpi_id', $kpi_id)->where('tbl_embodiment.emboded_status', 'Emboded')
            ->orderBy('tbl_embodiment.transfered_date', 'desc')
            ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.id as rank', 'tbl_division.division_name_bng', 'tbl_units.unit_name_bng', 'tbl_embodiment.*', 'tbl_kpi_info.kpi_name', 'tbl_designations.name_bng')->get();
        return $ansars;
    }

    public static function getEmbodiedAnsarV($unit_id, $thana_id)
    {
        DB::enableQueryLog();


        $ansars = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_division as ad', 'tbl_ansar_parsonal_info.division_id', '=', 'ad.id')
            ->join('tbl_units as au', 'tbl_ansar_parsonal_info.unit_id', '=', 'au.id')
            ->join('tbl_thana as at', 'tbl_ansar_parsonal_info.thana_id', '=', 'at.id')
            ->join('tbl_division as kd', 'tbl_kpi_info.division_id', '=', 'kd.id')
            ->join('tbl_units as ku', 'tbl_kpi_info.unit_id', '=', 'ku.id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_kpi_info.unit_id', $unit_id)->where('tbl_kpi_info.thana_id', $thana_id)->where('tbl_embodiment.emboded_status', 'Emboded')->orderBy('tbl_embodiment.ansar_id')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_ansar_parsonal_info.mobile_no_self as phone', 'tbl_ansar_parsonal_info.father_name_eng as fatherName', 'tbl_ansar_parsonal_info.mother_name_eng as motherName', 'tbl_ansar_parsonal_info.data_of_birth as birthDate', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.national_id_no as id', 'ad.division_name_eng', 'au.unit_name_eng', 'at.thana_name_eng', 'tbl_ansar_parsonal_info.union_name_eng', 'kd.division_name_eng as kdd', 'ku.unit_name_eng as kuu', 'kt.thana_name_eng as ktt')
            ->get();
        //return count($ansars)/6;
        return $ansars;
    }


    public static function getFreezeList($division, $unit, $thana, $kpi)
    {
        $freeze = DB::table('tbl_freezing_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id');
        if ($division && $division != 'all') {
            $freeze->where('tbl_kpi_info.division_id', $division);
        }
        if ($unit && $unit != 'all') {
            $freeze->where('tbl_kpi_info.unit_id', $unit);
        }
        if ($thana && $thana != 'all') {
            $freeze->where('tbl_kpi_info.thana_id', $thana);
        }
        if ($kpi && $kpi != 'all') {
            $freeze->where('tbl_kpi_info.id', $kpi);
        }
        $data = $freeze->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();

        return $data;

    }

    public static function sendSMS($ansar_id)
    {
        $phone_no = PersonalInfo::where('ansar_id', $ansar_id)->first()->mobile_no_self;
        $twilio = new Services_Twilio(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
        $twilio->account->messages->create(array(
            'To' => $phone_no,
            'From' => env('TWILIO_PHONE_NO'),
            'StatusCallback' => URL::to('/get_sms_status')
        ));
    }

    /**
     * @param $ansarId
     * @return string
     */

    public static function getSearchAnsar($ansarId, $type)
    {

        $user = Auth::user();
        $usertype = $user->type;
        $userId = $user->id;
        if ($usertype == 11 || $usertype == 77 || $usertype == 22 || $usertype == 33) {
            $verified = $type == 0 ? [0, 1] : 2;
            if (is_array($verified)) {
                $ansar = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansarId)->whereIn('tbl_ansar_parsonal_info.verified', $verified)
                    ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng')
                    ->get();
            } else {
                $ansar = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansarId)->where('tbl_ansar_parsonal_info.verified', $verified)
                    ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng')
                    ->get();
            }
            return $ansar;
        } elseif ($usertype == 44) {

            $verified = $type == 0 ? 1 : 2;
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_user_action_log', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_user_action_log.ansar_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansarId)->where('tbl_ansar_parsonal_info.verified', $verified)
                ->where('tbl_user_action_log.action_by', '=', $userId)->where('tbl_user_action_log.action_type', '=', 'VERIFIED')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng')
                ->get();
            return $ansar;
        } elseif ($usertype == 55) {
            $verified = $type == 0 ? 0 : 1;
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansarId)
                ->where('tbl_ansar_parsonal_info.user_id', $userId)->where('tbl_ansar_parsonal_info.verified', $verified)
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng')
                ->get();
            return $ansar;
        }
    }

    public static function getAdvancedSearchAnsar($ansarId)
    {
        $ansarAdvancedSearch = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_division', 'tbl_ansar_parsonal_info.division_id', '=', 'tbl_division.id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->where('data_of_birth', '<', $ansarId['birth_from_name'])
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.sex',
                'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_division.division_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng')
            ->paginate(15);
        return $ansarAdvancedSearch;
    }

// Dashboard all ansar list
    public static function getAllAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        //DB::enableQueryLog();
        $ansarQuery = QueryHelper::getQuery(QueryHelper::ALL_ANSARS);
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_thana.id', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->distinct()->orderBy('tbl_ansar_parsonal_info.ansar_id')->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        // DB::getQueryLog();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars];

    }


// Dashboard free ansar list
    public static function getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        DB::enableQueryLog();
        $ansarQuery = QueryHelper::getQuery(QueryHelper::FREE);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_thana.id', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        $b = ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars];
//        return DB::getQueryLog();
        return $b;
    }


// Dashboard panel ansar list
    public static function getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division = null, $sex, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::PANEL);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_thana.id', $thana);
        }
        if ($sex != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.sex', $sex);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_panel_info.panel_date', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->orderBy('tbl_panel_info.panel_date')->orderBy('tbl_panel_info.id')->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_panel_info.panel_date', 'tbl_panel_info.memorandum_id')->skip($offset)->limit($limit)->get();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'pannel'];
    }


// Dashboard offered ansar list
    public static function getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        DB::enableQueryLog();
        $ansarQuery = QueryHelper::getQuery(QueryHelper::OFFER);
        $ansarQuery1 = QueryHelper::getQuery(QueryHelper::OFFER_RECEIVED);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
            $ansarQuery1->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('ou.division_id', $division);
            $ansarQuery1->where('ou.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('ou.id', $unit);
            $ansarQuery1->where('ou.id', $unit);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backTime, $recentTime]);
            $ansarQuery1->whereBetween('tbl_ansar_status_info.updated_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
            $ansarQuery1->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total1 = clone $ansarQuery1;
        $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_sms_offer_info.sms_send_datetime', 'ou.unit_name_eng as offer_unit');
        $ansarQuery1->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_sms_receive_info.sms_send_datetime', 'ou.unit_name_eng as offer_unit');
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $total1->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->unionAll($ansarQuery1)->distinct()->skip($offset)->limit($limit)->get();
        $t = DB::table(DB::raw("({$total->unionAll($total1)->toSql()}) x"))->mergeBindings($total)->select(DB::raw('SUM(t) as t,code'))->groupBy('code')->get();
        return ['total' => collect($t)->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'offer'];
    }


// Dashboard rested ansar list
    public static function getTotalRestAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::REST);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_thana.id', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_rest_info.updated_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_rest_info.rest_date')->skip($offset)->limit($limit)->get();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'rest'];
    }


// Dashboard freezed ansar list
    public static function getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::FREEZE);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_thana.id', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->distinct()->orderBy('tbl_freezing_info.freez_date', 'asc')->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_freezing_info.freez_reason', 'tbl_freezing_info.freez_date')->skip($offset)->limit($limit)->get();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'freeze'];
    }


// Dashboard blocked ansar list
    public static function getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::BLOCK);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_thana.id', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_blocklist_info.date_for_block', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_blocklist_info.comment_for_block', 'tbl_blocklist_info.date_for_block')->skip($offset)->limit($limit)->get();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'block'];
    }


// Dashboard blacked ansar list
    public static function getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::BLACK);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_thana.id', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_blacklist_info.black_list_comment as reason', 'tbl_blacklist_info.black_listed_date as date')->skip($offset)->limit($limit)->get();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'black'];

    }


// Dashboard embodied ansar list
    public static function getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::EMBODIED);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_kpi_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_kpi_info.unit_id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_kpi_info.thana_id', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;

        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_kpi_info.kpi_name', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id')->orderBy('tbl_embodiment.joining_date', 'desc')->orderBy('tbl_embodiment.id')->skip($offset)->limit($limit)->get();

        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'embodied'];
    }


// Dashboard own embodied ansar list(DC,RC)
    public static function getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::OWN_EMBODIED);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_kpi_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('ku.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('kt.id', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->orderBy('tbl_ansar_parsonal_info.ansar_id', 'asc')->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_kpi_info.kpi_name', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id')->skip($offset)->limit($limit)->get();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'embodied'];
    }


// Dashboard diff embodied ansar list(DC,RC)
    public static function getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::DIFF_EMBODIED);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', '=', $division);
        }
        if ($unit != 'all') {
//            $ansarQuery->where('ku.id', '!=', $unit);
            $ansarQuery->where('pu.id', '=', $unit);
        }
        if ($thana != 'all') {
//            $ansarQuery->where('kt.id', '!=', $thana);
            $ansarQuery->where('pt.id', '=', $thana);
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backTime, $recentTime]);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
//        return $ansarQuery->toSql();
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->orderBy('tbl_ansar_parsonal_info.ansar_id', 'asc')->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_kpi_info.kpi_name', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id')->skip($offset)->limit($limit)->get();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'diff_embodied'];
    }


// Dashboard not verified ansar list
    public static function getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division = null, $time, $rank, $q)
    {
        $ansarQuery = QueryHelper::getQuery(QueryHelper::UNVERIFIED);
        if ($rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $rank);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_thana.id', $thana);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        if ($time == self::RECENT) {
            $recentTime = Carbon::now();
            $backTime = Carbon::now()->subDays(7);
            $ansarQuery->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backTime, $recentTime]);
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $ansars = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return ['total' => collect($total->get())->pluck('t', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars];
    }

//End dashboard


    public static function ansarListForServiceEnded($offset, $limit, $unit, $thana, $division = null, $interval = 2, $q)
    {
        DB::enableQueryLog();
        $ansarQuery = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_status_info.black_list_status', 0)
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
            ->whereRaw("service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL {$interval} MONTH)");
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_kpi_info.division_id', '=', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_embodiment.ansar_id') as total"), 'tbl_designations.code');
        $ansars = $ansarQuery->select('tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_kpi_info.kpi_name as kpi', 'tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return Response::json(['total' => collect($total->get())->pluck('total', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
//        return DB::getQueryLog();
    }


    public static function ansarListWithFiftyYears($offset, $limit, $unit, $thana, $division, $q)
    {
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', '=', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.unit_id', '=', $unit);
        }
        if ($thana != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.thana_id', '=', $thana);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        $total = clone $ansarQuery;
        $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_embodiment.ansar_id') as total"), 'tbl_designations.code');
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return Response::json(['total' => collect($total->get())->pluck('total', 'code'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function ansarListForNotInterested($offset, $limit, $unit, $thana, $division, $q)
    {
        DB::enableQueryLog();
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_sms_send_log', 'tbl_sms_send_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_units.division_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_sms_send_log.reply_type', 'No Reply')
            ->havingRaw('count(tbl_sms_send_log.ansar_id)>=10');
        if ($division != 'all') {
            $ansarQuery->where('tbl_division.id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery->where('tbl_units.id', $unit);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }

        $total = clone $ansarQuery;
//        $total->groupBy('tbl_designations.id');
        $ansars = $ansarQuery->groupBy('tbl_sms_send_log.ansar_id')->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        $b = Response::json(['total' => $total->count(), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
        //return DB::getQueryLog();
        return $b;
    }

    public static function getansarForNotInterestedCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->distinct()->groupBy('tbl_sms_offer_info.ansar_id');

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->distinct()->groupBy('tbl_sms_offer_info.ansar_id');

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->distinct()->groupBy('tbl_sms_offer_info.ansar_id');
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->distinct()->groupBy('tbl_sms_offer_info.ansar_id');
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->distinct()->groupBy('tbl_sms_offer_info.ansar_id');

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->distinct()->groupBy('tbl_sms_offer_info.ansar_id');

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->distinct()->groupBy('tbl_sms_offer_info.ansar_id');
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->distinct()->groupBy('tbl_sms_offer_info.ansar_id');
            }
        }
        $total = $ansarQuery->count('tbl_sms_offer_info.ansar_id');
//        print_r(DB::getQueryLog());
        return Response::json(['total' => $total]);
    }

    public static function getBlocklistedAnsar($offset, $limit, $division, $unit, $thana, $q)
    {
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_ansar_status_info.block_list_status', 1)
            ->where('tbl_blocklist_info.date_for_unblock', '=', null);
        if ($unit && $unit != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.unit_id', '=', $unit);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', '=', $division);
        }
        if ($thana && $thana != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.thana_id', '=', $thana);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', $q);
        }
        $total = clone $ansarQuery;
        $ansars = $ansarQuery->select('tbl_blocklist_info.*', 'tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return Response::json(['total' => $total->count(), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function getBlacklistedAnsar($offset, $limit, $division, $unit, $thana, $q)
    {
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_blacklist_info', 'tbl_blacklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_ansar_status_info.black_list_status', 1)
            ->distinct();
        if ($unit && $unit != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.unit_id', '=', $unit);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.division_id', '=', $division);
        }
        if ($thana && $thana != 'all') {
            $ansarQuery->where('tbl_ansar_parsonal_info.thana_id', '=', $thana);
        }
        if ($q) {
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', $q);
        }
        $total = clone $ansarQuery;
        $ansars = $ansarQuery->distinct()->select('tbl_blacklist_info.*', 'tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return Response::json(['total' => $total->distinct()->count(), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }


    public static function threeYearsOverAnsarList($offset, $limit, $division, $unit, $ansar_rank, $ansar_sex)
    {
        $ansarQuery = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.service_ended_date', '<', Carbon::now())
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded');
        if ($unit != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($division != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_kpi_info.division_id', '=', $division);
        }
        if ($ansar_rank != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_designations.id', '=', $ansar_rank);
        }
        if ($ansar_sex != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_ansar_parsonal_info.sex', '=', $ansar_sex);
        }
        $total = clone $ansarQuery;
        $ansars = $ansarQuery->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_designations.id as did',
            'tbl_kpi_info.kpi_name as kpi', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'pu.unit_name_bng as unit', 'ku.unit_name_bng as k_unit', 'tbl_designations.name_bng as rank')->skip($offset)->limit($limit)->get();
        $total = $total->groupBy('tbl_designations.id')->orderBy('tbl_designations.id')->select(DB::raw('count(tbl_designations.id) as t'), 'tbl_designations.code as code')->pluck('t', 'code');

        return Response::json(['total' => $total, 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);

    }

    public static function disembodedAnsarListforReport($offset, $limit, $from_date, $to_date, $division, $unit, $thana)
    {
        DB::enableQueryLog();
        $ansarQuery = DB::table('tbl_embodiment_log')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
            ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))->distinct();
        if ($unit && $unit != 'all') {
            $ansarQuery->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_kpi_info.division_id', '=', $division);
        }
        if ($thana && $thana != 'all') {
            $ansarQuery->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        $ansarQuery->select('tbl_embodiment_log.ansar_id as id', 'tbl_embodiment_log.reporting_date as r_date', 'tbl_embodiment_log.joining_date as j_date', 'tbl_embodiment_log.release_date as re_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
            'tbl_units.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi', 'tbl_disembodiment_reason.reason_in_bng as reason');
        $total = clone $ansarQuery;
        $ansars = $ansarQuery->skip($offset)->limit($limit)->get();
//        return DB::getQueryLog();
        return Response::json(['total' => DB::table(DB::raw("({$total->toSql()}) x"))->mergeBindings($total)->count(), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }


    public static function embodedAnsarListforReport($offset, $limit, $from_date, $to_date, $division, $unit, $thana)
    {
        DB::enableQueryLog();
        $ansarQuery = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_units as pu', 'tbl_ansar_parsonal_info.unit_id', '=', 'pu.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
            ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date));
        if ($unit && $unit != 'all') {
            $ansarQuery->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($division && $division != 'all') {
            $ansarQuery->where('tbl_kpi_info.division_id', '=', $division);
        }
        if ($thana && $thana != 'all') {
            $ansarQuery->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        $total = clone $ansarQuery;
        $ansars = $ansarQuery->distinct()->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
            'pu.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi')->skip($offset)->limit($limit)->get();
        //return DB::getQueryLog();
        return Response::json(['total' => $total->distinct()->count(), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function kpiInfo($offset, $limit, $division, $unit, $thana, $q)
    {
        DB::enableQueryLog();
        $kpiQuery = $kpiQuery = DB::table('tbl_kpi_info')
            ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_info.id', '=', 'tbl_kpi_detail_info.kpi_id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->leftJoin('tbl_embodiment', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->where('tbl_kpi_info.status_of_kpi', 1)->where('tbl_kpi_info.withdraw_status', 0)->whereNull('tbl_kpi_detail_info.kpi_withdraw_date');
        if ($thana != 'all') {
            $kpiQuery->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        if ($division != 'all') {
            $kpiQuery->where('tbl_kpi_info.division_id', '=', $division);
        }
        if ($unit != 'all') {
            $kpiQuery->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($q) {
            global $name;
            $name = $q;
            $kpiQuery->where(function ($q) {
                global $name;
                $q->where('tbl_kpi_info.kpi_name_eng', 'LIKE', '%' . $name . '%')
                    ->orWhere('tbl_kpi_info.kpi_name', 'LIKE', '%' . $name . '%');
            });
        }
        $total = clone $kpiQuery;
        $kpis = $kpiQuery->select('tbl_kpi_info.id', 'tbl_kpi_info.status_of_kpi', 'tbl_kpi_info.kpi_name as kpi_bng', 'tbl_kpi_info.kpi_name_eng as kpi_eng', 'tbl_kpi_info.kpi_address as address', 'tbl_kpi_info.kpi_contact_no as contact', 'tbl_division.division_name_eng as division_eng', 'tbl_division.division_name_bng as division_bng', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana', 'tbl_kpi_detail_info.total_ansar_request', DB::raw('COUNT(tbl_embodiment.ansar_id) as total_embodied'))->groupBy('tbl_kpi_info.id')->orderBy('tbl_kpi_info.id', 'asc')->skip($offset)->limit($limit)->get();
//        return View::make('kpi.selected_kpi_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);
//        return DB::getQueryLog();
        return Response::json(['total' => $total->distinct()->count('tbl_kpi_info.id'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);

    }

    public static function withdrawnKpiInfo($offset, $limit, $unit, $thana, $division)
    {
        $kpiQuery = DB::table('tbl_kpi_info')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->whereNotNull('tbl_kpi_detail_info.kpi_withdraw_date')
            ->where('tbl_kpi_info.withdraw_status', '=', 0)
            ->where('tbl_kpi_info.status_of_kpi', '=', 1);
        if (strcasecmp($unit, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.unit_id', '=', $unit);

        }
        if (strcasecmp($thana, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.thana_id', '=', $thana);

        }
        if (strcasecmp($division, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.division_id', '=', $division);

        }
        $kpis = $kpiQuery->select('tbl_kpi_info.id', 'tbl_kpi_info.kpi_name as kpi', 'tbl_kpi_info.withdraw_status', 'tbl_kpi_detail_info.kpi_withdraw_date as date', 'tbl_division.division_name_eng as division', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')->skip($offset)->limit($limit)->get();
//        return View::make('kpi.selected_kpi_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);

    }

    public static function withdrawnKpiInfoCount($unit, $thana, $division)
    {
        DB::enableQueryLog();
        $kpiQuery = DB::table('tbl_kpi_info')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->whereNotNull('tbl_kpi_detail_info.kpi_withdraw_date')
            ->where('tbl_kpi_info.withdraw_status', '=', 0)
            ->where('tbl_kpi_info.status_of_kpi', '=', 1);
        if (strcasecmp($unit, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.unit_id', '=', $unit);

        }
        if (strcasecmp($thana, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.thana_id', '=', $thana);

        }
        if (strcasecmp($division, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.division_id', '=', $division);

        }
        $total = $kpiQuery->count('tbl_kpi_info.id');
//        print_r(DB::getQueryLog());
        return Response::json(['total' => $total]);
    }

    public static function inactiveKpiInfo($offset, $limit, $unit, $thana, $division = "all", $q)
    {
        $kpiQuery = DB::table('tbl_kpi_info')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->where(function ($query) {
                $query->whereNotNull('tbl_kpi_detail_info.kpi_withdraw_date')
                    ->orWhere('tbl_kpi_info.withdraw_status', '=', 1)
                    ->orWhere('tbl_kpi_info.status_of_kpi', '=', 0);
            });
        if (strcasecmp($unit, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.unit_id', '=', $unit);

        }
        if (strcasecmp($thana, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.thana_id', '=', $thana);

        }
        if (strcasecmp($division, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.division_id', '=', $division);

        }
        if ($q) {
            global $name;
            $name = $q;
            $kpiQuery->where(function ($q) {
                global $name;
                $q->where('tbl_kpi_info.kpi_name_eng', 'LIKE', '%' . $name . '%')
                    ->orWhere('tbl_kpi_info.kpi_name', 'LIKE', '%' . $name . '%');
            });
        }
        $total = clone $kpiQuery;
        $kpis = $kpiQuery->select('tbl_kpi_info.id', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.withdraw_status', 'tbl_kpi_info.status_of_kpi as status', 'tbl_kpi_detail_info.kpi_withdraw_date as date', 'tbl_division.division_name_eng as division', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')->skip($offset)->limit($limit)->get();
//        return View::make('kpi.selected_kpi_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);
        return Response::json(['total' => $total->distinct()->count('tbl_kpi_info.id'), 'index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);

    }

    public static function inactiveKpiInfoCount($unit, $thana, $division = "all")
    {
        DB::enableQueryLog();
        $kpiQuery = DB::table('tbl_kpi_info')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->where(function ($query) {
                $query->whereNotNull('tbl_kpi_detail_info.kpi_withdraw_date')
                    ->orWhere('tbl_kpi_info.withdraw_status', '=', 1)
                    ->orWhere('tbl_kpi_info.status_of_kpi', '=', 0);
            });
        if (strcasecmp($unit, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.unit_id', '=', $unit);

        }
        if (strcasecmp($thana, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.thana_id', '=', $thana);

        }
        if (strcasecmp($division, "all") != 0) {
            $kpiQuery->where('tbl_kpi_info.division_id', '=', $division);

        }

        $total = $kpiQuery->count('tbl_kpi_info.id');
//        return (DB::getQueryLog());
        return Response::json(['total' => $total]);
    }

    public static function unitInfo($offset, $limit, $division)
    {
        if (strcasecmp($division, 'all') == 0) {
            $unitQuery = DB::table('tbl_units')
                ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id');
        } else {
            $unitQuery = DB::table('tbl_units')
                ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
                ->where('tbl_units.division_id', '=', $division);
        }

        $units = $unitQuery->where('tbl_division.id', '!=', 0)->select('tbl_units.id', 'tbl_units.unit_name_eng', 'tbl_units.unit_name_bng', 'tbl_units.unit_code', 'tbl_division.division_name_eng', 'tbl_division.division_code')->skip($offset)->limit($limit)->get();
//        return View::make('kpi.selected_kpi_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'units' => $units]);

    }

    public static function unitInfoCount($division)
    {
        DB::enableQueryLog();
        if (strcasecmp($division, 'all') == 0) {
            $unitQuery = DB::table('tbl_units')
                ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id');
        } else {
            $unitQuery = DB::table('tbl_units')
                ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
                ->where('tbl_units.division_id', '=', $division);
        }
        $total = $unitQuery->where('tbl_division.id', '!=', 0)->count('tbl_units.id');
//        print_r(DB::getQueryLog());
        return Response::json(['total' => $total]);
    }

    public static function thanaInfo($offset, $limit, $division, $unit)
    {
        if ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") == 0)) {
            $thanaQuery = DB::table('tbl_thana')
                ->join('tbl_division', 'tbl_thana.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_thana.unit_id', '=', 'tbl_units.id');

        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") == 0)) {
            $thanaQuery = DB::table('tbl_thana')
                ->join('tbl_division', 'tbl_thana.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_thana.unit_id', '=', 'tbl_units.id')
                ->where('tbl_thana.division_id', '=', $division);

        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") != 0)) {
            $thanaQuery = DB::table('tbl_thana')
                ->join('tbl_division', 'tbl_thana.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_thana.unit_id', '=', 'tbl_units.id')
                ->where('tbl_thana.unit_id', '=', $unit);

        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") != 0)) {
            $thanaQuery = DB::table('tbl_thana')
                ->join('tbl_division', 'tbl_thana.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_thana.unit_id', '=', 'tbl_units.id')
                ->where('tbl_thana.division_id', '=', $division)
                ->where('tbl_thana.unit_id', '=', $unit);
        }

        $thanas = $thanaQuery->select('tbl_thana.id', 'tbl_thana.thana_name_eng', 'tbl_thana.thana_name_bng', 'tbl_thana.thana_code', 'tbl_units.unit_name_eng', 'tbl_division.division_name_eng')->skip($offset)->limit($limit)->get();
//        return View::make('kpi.selected_kpi_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'thanas' => $thanas]);

    }

    public static function thanaInfoCount($division, $unit)
    {
        DB::enableQueryLog();
        if ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") == 0)) {
            $thanaQuery = DB::table('tbl_thana')
                ->join('tbl_division', 'tbl_thana.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_thana.unit_id', '=', 'tbl_units.id');

        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") == 0)) {
            $thanaQuery = DB::table('tbl_thana')
                ->join('tbl_division', 'tbl_thana.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_thana.unit_id', '=', 'tbl_units.id')
                ->where('tbl_thana.division_id', '=', $division);

        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") != 0)) {
            $thanaQuery = DB::table('tbl_thana')
                ->join('tbl_division', 'tbl_thana.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_thana.unit_id', '=', 'tbl_units.id')
                ->where('tbl_thana.unit_id', '=', $unit);

        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") != 0)) {
            $thanaQuery = DB::table('tbl_thana')
                ->join('tbl_division', 'tbl_thana.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_thana.unit_id', '=', 'tbl_units.id')
                ->where('tbl_thana.division_id', '=', $division)
                ->where('tbl_thana.unit_id', '=', $unit);
        }
        $total = $thanaQuery->count('tbl_thana.id');
//        print_r(DB::getQueryLog());
        return Response::json(['total' => $total]);
    }

    static function addActionlog($user, $multiple = false)
    {
        if ($multiple) {
            foreach ($user as $u) {
                $action = new ActionUserLog;
                $action->ansar_id = $u['ansar_id'];
                $action->action_type = $u['action_type'];
                $action->from_state = $u['from_state'];
                $action->to_state = $u['to_state'];
                $action->action_by = $u['action_by'];
                $action->save();
            }
        } else {
            $action = new ActionUserLog;
            $action->ansar_id = $user['ansar_id'];
            $action->action_type = $user['action_type'];
            $action->from_state = $user['from_state'];
            $action->to_state = $user['to_state'];
            $action->action_by = $user['action_by'];
            $action->save();
        }
    }

    static function addDGlog($user)
    {
        $action = new DGAction;
        $action->ansar_id = $user['ansar_id'];
        $action->action = $user['action_type'];
        $action->from_state = $user['from_state'];
        $action->to_state = $user['to_state'];
        $action->save();
    }

    public static function isAnsarFreezeInDistrict($district)
    {
        $freeze = DB::table('tbl_freezing_info')->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_freezing_info.kpi_id')
            ->where('tbl_kpi_info.unit_id', $district)->where('tbl_freezing_info.freez_reason', '!=', 'Disciplinary Actions')->distinct()->count('tbl_freezing_info.ansar_id');
        return $freeze;
    }

    public static function ansarAcceptOfferLastFiveDays($division, $unit, $thana, $rank, $sex, $offset, $limit, $q, $type)
    {
        DB::enableQueryLog();
        $now = Carbon::now();
        $next = Carbon::now()->subDays(5)->setTime(0, 0, 0);
        $ansarQuery1 = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_receive_info.offered_district')
            ->join('tbl_division as od', 'od.id', '=', 'ou.division_id')
            ->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')
            ->whereBetween('tbl_sms_receive_info.sms_send_datetime', [$next, $now]);
        $ansarQuery2 = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_sms_send_log', 'tbl_sms_send_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_send_log.offered_district')
            ->join('tbl_division as od', 'od.id', '=', 'ou.division_id')
            ->where('tbl_sms_send_log.reply_type', 'Yes')
            ->whereBetween('tbl_sms_send_log.offered_date', [$next, $now]);
        if ($division != 'all') {
            $ansarQuery1->where('od.id', $division);
            $ansarQuery2->where('od.id', $division);
        }
        if ($unit != 'all') {
            $ansarQuery1->where('ou.id', $unit);
            $ansarQuery2->where('ou.id', $unit);
        }
//        if ($thana != 'all') {
//            $ansarQuery1->where('ot.id', $division);
//            $ansarQuery2->where('ot.id', $division);
//        }
        if ($rank != 'all') {
            $ansarQuery1->where('tbl_designations.id', $rank);
            $ansarQuery2->where('tbl_designations.id', $rank);
        }
        if ($sex != 'all') {
            $ansarQuery1->where('tbl_ansar_parsonal_info.sex', $sex);
            $ansarQuery2->where('tbl_ansar_parsonal_info.sex', $sex);
        }
        if ($q) {
            $ansarQuery1->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
            $ansarQuery2->where('tbl_ansar_parsonal_info.ansar_id', 'LIKE', '%' . $q . '%');
        }
        if ($type == 'view') {
            $t1 = clone $ansarQuery1;
            $t2 = clone $ansarQuery2;
            $ansarQuery1->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
                'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'ou.unit_name_bng as offer_unit', 'tbl_sms_receive_info.sms_send_datetime as offer_date');
            $ansarQuery2->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
                'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'ou.unit_name_bng as offer_unit', 'tbl_sms_send_log.offered_date as offer_date');
            $ansars = $ansarQuery1->unionAll($ansarQuery2)->skip($offset)->limit($limit)->get();
            $t1->groupBy('tbl_designations.id')->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total'), 'tbl_designations.code');
            $t2->groupBy('tbl_designations.id')->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total'), 'tbl_designations.code');
            $total = $t1->unionAll($t2);
            return Response::json(['total' => collect($total->get())->groupBy('code'), 'index' => ((ceil($offset / ($limit == 0 ? 1 : $limit))) * $limit) + 1, 'ansars' => $ansars, 'type' => 'offer']);

        } else {
            $ansarQuery1->groupBy('tbl_designations.id')->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total'), 'tbl_designations.code');
            $ansarQuery2->groupBy('tbl_designations.id')->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total'), 'tbl_designations.code');
            $total = $ansarQuery1->unionAll($ansarQuery2);
            return Response::json(['total' => collect($total->get())->groupBy('code')]);
        }
//        return $b;
    }
}