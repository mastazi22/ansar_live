<?php


namespace App\modules\HRM\Models;


use App\Helper\Facades\GlobalParameterFacades;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Services_Twilio;

class CustomQuery
{
    protected $connection = 'hrm';

    public static function getAnsarInfo($pc = array('male' => 0, 'female' => 0), $apc = array('male' => 0, 'female' => 0), $ansar = array('male' => 0, 'female' => 0), $unit_id = [], $exclude_district = null)
    {
        DB::enableQueryLog();
        $query = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_panel_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_division', 'tbl_ansar_parsonal_info.division_id', '=', 'tbl_division.id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_units as pu', 'tbl_ansar_parsonal_info.unit_id', '=', 'pu.id');
        if (Auth::user()->type == 22) {
            if (in_array($exclude_district, Config::get('app.offer'))) {
                $query = $query->where('pu.id', '!=', $exclude_district);
            } else {
                $query = $query->join('tbl_units as du', 'tbl_division.id', '=', 'du.division_id')
                    ->where('pu.id', '!=', $exclude_district)->where('du.id', '=', $exclude_district);
            }
        }
        else if(Auth::user()->type == 11||Auth::user()->type == 33||Auth::user()->type == 66){
            if(is_array($unit_id)){
                $query = $query->whereIn('pu.id', $unit_id);
            }
        }
        $query = $query->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_ansar_status_info.block_list_status', 0)->whereRaw('DATEDIFF(NOW(),tbl_ansar_parsonal_info.data_of_birth)/365<50');
        $pc_male = clone $query;
        $pc_female = clone $query;
        $apc_male = clone $query;
        $apc_female = clone $query;
        $ansar_male = clone $query;
        $ansar_female = clone $query;
        $pc_male->where('tbl_ansar_parsonal_info.designation_id', '=', 3)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')
            ->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_division.division_name_eng', 'pu.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng')
            ->take($pc['male']);
        $pc_female->where('tbl_ansar_parsonal_info.designation_id', '=', 3)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')
            ->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_division.division_name_eng', 'pu.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng')
            ->take($pc['female']);
        $ansar_male->where('tbl_ansar_parsonal_info.designation_id', '=', 1)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')
            ->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_division.division_name_eng', 'pu.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng')
            ->take($ansar['male']);
        $ansar_female->where('tbl_ansar_parsonal_info.designation_id', '=', 1)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')
            ->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_division.division_name_eng', 'pu.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng')
            ->take($ansar['female']);
         $apc_male->where('tbl_ansar_parsonal_info.designation_id', '=', 2)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Male')
            ->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_division.division_name_eng', 'pu.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng')
            ->take($apc['male']);
        $apc_female->where('tbl_ansar_parsonal_info.designation_id', '=', 2)
            ->where('tbl_ansar_parsonal_info.sex', '=', 'Female')
            ->orderBy('tbl_panel_info.id')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_division.division_name_eng', 'pu.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng')
            ->take($apc['female']);

        $b = $pc_male->unionAll($pc_female)->unionAll($apc_male)->unionAll($apc_female)->unionAll($ansar_male)->unionAll($ansar_female)->get();
        return $b;
    }

    public static function offerQuota()
    {
        $offer_quota = DB::table('tbl_offer_quota')
            ->rightJoin('tbl_units', 'tbl_units.id', '=', 'tbl_offer_quota.unit_id')
            ->select('tbl_units.unit_name_eng', 'tbl_units.id as unit', 'tbl_offer_quota.quota', 'tbl_offer_quota.id')->get();
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


    public static function getUserInformation($limit, $offset)
    {
        $users = DB::connection('hrm')->table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_user_log', 'tbl_user_log.user_id', '=', 'tbl_user.id')->skip($offset)->take($limit)
            ->select('tbl_user.id', 'tbl_user.user_name', 'tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.email', 'tbl_user_log.last_login', 'tbl_user_log.user_status', 'tbl_user.status')
            ->get();
        return $users;
    }

    public static function getNotVerifiedChunkAnsar($limit, $offset)
    {
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->where('tbl_ansar_parsonal_info.verified', 0)->orWhere('tbl_ansar_parsonal_info.verified', 1)->orderBy('tbl_ansar_parsonal_info.ansar_id', 'asc')->skip($offset)->take($limit)
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.sex', 'tbl_units.unit_name_bng', 'tbl_thana.thana_name_bng', 'tbl_designations.name_bng')
            ->get();
        return $ansar;

    }

    public static function getNotVerifiedAnsar($limit, $offset)
    {
        $user = Auth::user();
        $usertype = $user->type;
        if ($usertype == 11 || $usertype == 22 || $usertype == 33) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 0)->orWhere('tbl_ansar_parsonal_info.verified', 1)->skip($offset)->take($limit)->orderBy('tbl_ansar_parsonal_info.ansar_id', 'ASC')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.data_of_birth')
                ->get();
            return $ansar;
        } elseif ($usertype == 44) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 1)->where('tbl_ansar_parsonal_info.ansar_id', '>', GlobalParameterFacades::getValue("last_ansar_id"))->skip($offset)->take($limit)->orderBy('tbl_ansar_parsonal_info.ansar_id', 'ASC')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.data_of_birth')
                ->get();
            return $ansar;
        } elseif ($usertype == 55) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 0)->where('tbl_ansar_parsonal_info.user_id', $user->id)->skip($offset)->take($limit)->orderBy('tbl_ansar_parsonal_info.ansar_id', 'ASC')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.data_of_birth')
                ->get();
            return $ansar;
        }
    }

    public static function getVerifiedAnsar($limit, $offset)
    {
        $user = Auth::user();
        $usertype = $user->type;
        $userId = $user->id;
        if ($usertype == 11 || $usertype == 22 || $usertype == 33) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 2)->skip($offset)->take($limit)
                ->orderBy('tbl_ansar_parsonal_info.ansar_id')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.data_of_birth')
                ->get();
            return $ansar;
        } elseif ($usertype == 44) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->join('tbl_user_action_log', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_user_action_log.ansar_id')
                ->where('tbl_user_action_log.to_state', '=', 'FREE')
                ->where('tbl_user_action_log.action_by', '=', $userId)
                ->where('tbl_ansar_parsonal_info.verified', 2)->where('tbl_ansar_parsonal_info.ansar_id', '>', GlobalParameterFacades::getValue("last_ansar_id"))->skip($offset)->take($limit)
                ->orderBy('tbl_ansar_parsonal_info.ansar_id')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.data_of_birth')
                ->get();
            return $ansar;
        } elseif ($usertype == 55) {
            $ansar = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_user_action_log', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_user_action_log.ansar_id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_ansar_parsonal_info.verified', 1)->where('tbl_user_action_log.action_type', 'VERIFIED')->where('tbl_user_action_log.action_by', $user->id)->skip($offset)->take($limit)
                ->orderBy('tbl_ansar_parsonal_info.ansar_id')
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_ansar_parsonal_info.data_of_birth')
                ->get();
            return $ansar;
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


    public static function getFreezeList()
    {
        $user = Auth::user();
        $filter = Input::get('filter');
        switch ($user->type) {
            case 22:
                if ($filter == "1") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Guard Withdraw')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->where('tbl_kpi_info.unit_id', $user->district_id)
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "2") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Guard Reduce')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->where('tbl_kpi_info.unit_id', $user->district_id)
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "3") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Disciplinary Actions')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->where('tbl_kpi_info.unit_id', $user->district_id)
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "0") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->where('tbl_kpi_info.unit_id', $user->district_id)
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                break;
            case 66:
                if ($filter == "1") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Guard Withdraw')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->where('tbl_kpi_info.division_id', $user->division_id)
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "2") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Guard Reduce')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->where('tbl_kpi_info.division_id', $user->division_id)
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "3") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Disciplinary Actions')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->where('tbl_kpi_info.division_id', $user->division_id)
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "0") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->where('tbl_kpi_info.division_id', $user->division_id)
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                break;
            default:
                if ($filter == "1") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Guard Withdraw')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "2") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Guard Reduce')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "3") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_freezing_info.freez_reason', '=', 'Disciplinary Actions')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
                if ($filter == "0") {
                    $freeze = DB::table('tbl_freezing_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                        ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                        ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                        ->orderBy('tbl_freezing_info.freez_date', 'asc')
                        ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_embodiment.reporting_date', 'tbl_embodiment.reporting_date',
                            'tbl_units.unit_name_bng', 'tbl_designations.name_bng', 'tbl_freezing_info.*', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status')->get();
                    return $freeze;
                }
        }

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
        if ($usertype == 11 || $usertype == 22 || $usertype == 33) {
            $verified = $type == 0 ? [0, 1] : 2;
            if (is_array($verified)) {
                $ansar = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansarId)->whereIn('tbl_ansar_parsonal_info.verified', $verified)
                    ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng')
                    ->get();
            } else {
                $ansar = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansarId)->where('tbl_ansar_parsonal_info.verified', $verified)
                    ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng')
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
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng')
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
                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.verified', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng')
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

    public static function getAllAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        //DB::enableQueryLog();
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_units.id', $unit)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->where('tbl_ansar_parsonal_info.division_id', $division)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_ansar_parsonal_info.division_id', $division)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)->distinct();
            }
        }
        $ansars = $ansarQuery->orderBy('tbl_ansar_parsonal_info.ansar_id')->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        //return DB::getQueryLog();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);

    }

    public static function getAllAnsarCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->distinct();
            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_units.id', $unit)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->where('tbl_ansar_parsonal_info.division_id', $division)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_ansar_parsonal_info.division_id', $division)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);

    }

    public static function getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        if (!is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function getTotalFreeAnsarCount($unit, $thana, $division = null)
    {
        if (!is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_panel_info.panel_date', 'asc');

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()->orderBy('tbl_panel_info.panel_date', 'asc');
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()->orderBy('tbl_panel_info.panel_date', 'asc');
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()->orderBy('tbl_panel_info.panel_date', 'asc');
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()->orderBy('tbl_panel_info.panel_date', 'asc');

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()->orderBy('tbl_panel_info.panel_date', 'asc');
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()->orderBy('tbl_panel_info.panel_date', 'asc');
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()->orderBy('tbl_panel_info.panel_date', 'asc');
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_panel_info.created_at', 'tbl_panel_info.memorandum_id')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'pannel']);
    }

    public static function getTotalPaneledAnsarCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        // DB::enableQueryLog();
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ot.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ou.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ot.id', $thana)->where('ou.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ou.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ou.division_id', $division)
                    ->where('ot.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ou.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ot.id', $thana)->where('ou.id', $unit)->where('ou.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_sms_offer_info.sms_send_datetime', 'ou.unit_name_eng as offer_unit')->skip($offset)->limit($limit)->get();
        //return DB::getQueryLog();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'offer']);
    }

    public static function getTotalOfferedAnsarCount($unit, $thana, $division = null)
    {
        //DB::enableQueryLog();
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ot.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ou.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ot.id', $thana)->where('ou.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ou.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ou.division_id', $division)
                    ->where('ot.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ou.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('ot.id', $thana)->where('ou.id', $unit)->where('ou.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }

        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        //return DB::getQueryLog();
        return Response::json(['total' => $total]);
    }

    public static function getTotalRestAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->where('tbl_ansar_status_info.rest_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_rest_info.rest_date')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'rest']);
    }

    public static function getTotalRestAnsarCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->where('tbl_ansar_status_info.rest_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_freezing_info.freez_date', 'asc');

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_freezing_info.freez_date', 'asc');
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_freezing_info.freez_date', 'asc');
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_freezing_info.freez_date', 'asc');
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_freezing_info.freez_date', 'asc');

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_freezing_info.freez_date', 'asc');
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_freezing_info.freez_date', 'asc');
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct()
                    ->orderBy('tbl_freezing_info.freez_date', 'asc');
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_freezing_info.freez_reason', 'tbl_freezing_info.freez_date')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'freeze']);
    }

    public static function getTotalFreezedAnsarCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 1)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_blocklist_info.comment_for_block', 'tbl_blocklist_info.date_for_block')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'block']);
    }

    public static function getTotalBlockedAnsarCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_blacklist_info.black_list_comment as reason', 'tbl_blacklist_info.black_listed_date as date')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'black']);

    }

    public static function getTotalBlackedAnsarCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_kpi_info.thana_id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_kpi_info.thana_id', $thana)->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_kpi_info.thana_id', $thana)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_kpi_info.thana_id', $thana)->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_kpi_info.kpi_name', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'embodied']);
    }

    public static function getTotalEmbodiedAnsarCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');

        return Response::json(['total' => $total]);
    }

    public static function getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_ansar_status_info.embodied_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if($division&&$division!='all'){
            $ansarQuery->where('tbl_kpi_info.division_id', $division);
        }
        if($unit!='all'){
            $ansarQuery->where('ku.id', $unit);
        }
        if($thana!='all'){
            $ansarQuery->where('kt.id', $thana);
        }
        $ansars = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_kpi_info.kpi_name', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'embodied']);
    }

    public static function getTotalOwnEmbodiedAnsarCount($unit, $thana, $division = null)
    {
        DB::enableQueryLog();
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_ansar_status_info.embodied_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if($division&&$division!='all'){
            $ansarQuery->where('tbl_kpi_info.division_id', $division);
        }
        if($unit!='all'){
            $ansarQuery->where('ku.id', $unit);
        }
        if($thana!='all'){
            $ansarQuery->where('kt.id', $thana);
        }
        $total = $ansarQuery->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
        //return DB::getQueryLog();
        return Response::json(['total' => $total]);
    }

    public static function getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_ansar_status_info.embodied_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if($division&&$division!='all'){
            $ansarQuery->where('tbl_kpi_info.division_id', '!=', $division);
        }
        if($unit!='all'){
            $ansarQuery->where('ku.id', '!=', $unit);
            $ansarQuery->where('pu.id', '=', $unit);
        }
        if($thana!='all'){
            $ansarQuery->where('kt.id', '!=', $thana);
            $ansarQuery->where('pt.id', '=', $thana);
        }
        $ansars = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_kpi_info.kpi_name', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'diff_embodied']);
    }

    public static function getTotalDiffEmbodiedAnsarCount($unit, $thana, $division = null)
    {
        DB::enableQueryLog();
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_ansar_status_info.embodied_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if($division&&$division!='all'){
            $ansarQuery->where('tbl_kpi_info.division_id', '!=', $division);
        }
        if($unit!='all'){
            $ansarQuery->where('ku.id', '!=', $unit);
            $ansarQuery->where('pu.id', '=', $unit);
        }
        if($thana!='all'){
            $ansarQuery->where('kt.id', '!=', $thana);
            $ansarQuery->where('pt.id', '=', $thana);
        }
        $total = $ansarQuery->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
//        return DB::getQueryLog();
        return Response::json(['total' => $total]);
    }

    public static function getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function getTotalNotVerifiedAnsarCount($unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.block_list_status', 0)->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function ansarListForServiceEnded($offset, $limit, $unit, $thana, $division = null)
    {
        DB::enableQueryLog();
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
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
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
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
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();
            } else {
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
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_kpi_info.thana_id', $thana)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.division_id', '=', $division)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.division_id', '=', $division)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_kpi_info.division_id', '=', $division)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.division_id', '=', $division)
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_kpi_info.thana_id', $thana)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();
            }
        }

        $ansars = $ansarQuery->select('tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_kpi_info.kpi_name as kpi', 'tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return View::make('HRM::Dashboard.selected_service_ended_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
//        return DB::getQueryLog();
    }

    public static function ansarListForServiceEndedCount($unit, $thana, $division = null)
    {
        DB::enableQueryLog();
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_kpi_info.thana_id', $thana)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.division_id', '=', $division)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.division_id', '=', $division)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_kpi_info.division_id', '=', $division)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_embodiment')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0)
                    ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                    ->where('tbl_kpi_info.division_id', '=', $division)
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->where('tbl_kpi_info.thana_id', $thana)
                    ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)')
                    ->distinct();
            }
        }

        $total = $ansarQuery->count('tbl_embodiment.ansar_id');
//        print_r(DB::getQueryLog());
        return Response::json(['total' => $total]);
    }

    public static function ansarListWithFiftyYears($offset, $limit, $unit, $thana, $division = null)
    {

        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
            }
        }

        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return View::make('HRM::Dashboard.selected_ansar_fifty_age_list')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function getansarWithFiftyYearsCount($unit, $thana, $division = null)
    {
        DB::enableQueryLog();
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->where(DB::raw("TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())"), ">=", 50);
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
//        print_r(DB::getQueryLog());
        return Response::json(['total' => $total]);
    }

    public static function ansarListForNotInterested($offset, $limit, $unit, $thana, $division = null)
    {
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');

            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', '=', $division)
                    ->where('tbl_ansar_parsonal_info.unit_id', '=', $unit)
                    ->where('tbl_ansar_parsonal_info.thana_id', '=', $thana)
                    ->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');
            }
        }

        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return View::make('HRM::Dashboard.selected_ansar_not_interested')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);

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

    public static function getBlocklistedAnsar($offset, $limit, $unit, $thana)
    {
        if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
            $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.block_list_status', 1)
                ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                ->distinct();

        } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
            $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.block_list_status', 1)
                ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                ->where('tbl_thana.id', $thana)
                ->distinct();
        } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
            $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.block_list_status', 1)
                ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                ->where('tbl_units.id', $unit)
                ->distinct();
        } else {
            $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.block_list_status', 1)
                ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                ->distinct();
        }
        $ansars = $ansarQuery->select('tbl_blocklist_info.*', 'tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return View::make('HRM::Report.selected_blocklist_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function getBlacklistedAnsar($offset, $limit, $unit, $thana)
    {
        if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
            $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_blacklist_info', 'tbl_blacklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.black_list_status', 1)
                ->distinct();

        } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
            $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_blacklist_info', 'tbl_blacklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.black_list_status', 1)
                ->where('tbl_thana.id', $thana)
                ->distinct();
        } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
            $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_blacklist_info', 'tbl_blacklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.black_list_status', 1)
                ->where('tbl_units.id', $unit)
                ->distinct();
        } else {
            $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_blacklist_info', 'tbl_blacklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.black_list_status', 1)
                ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                ->distinct();
        }
        $ansars = $ansarQuery->select('tbl_blacklist_info.*', 'tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_ansar_parsonal_info.sex',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return View::make('HRM::Report.selected_blacklist_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }


    //get recent ansar
    public static function getRecentTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $recentTime = Carbon::now();
        $backTime = Carbon::now()->subDays(7);
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_ansar_status_info.embodied_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if($division&&$division!='all'){
            $ansarQuery->where('tbl_kpi_info.division_id', $division);
        }
        if($unit!='all'){
            $ansarQuery->where('ku.id', $unit);
        }
        if($thana!='all'){
            $ansarQuery->where('kt.id', $thana);
        }
        $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at',[$backTime,$recentTime]);
        $ansars = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_kpi_info.kpi_name', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'embodied']);
    }

    public static function getRecentTotalOwnEmbodiedAnsarCount($unit, $thana, $division = null)
    {
        DB::enableQueryLog();
        $recentTime = Carbon::now();
        $backTime = Carbon::now()->subDays(7);
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_ansar_status_info.embodied_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if($division&&$division!='all'){
            $ansarQuery->where('tbl_kpi_info.division_id', $division);
        }
        if($unit!='all'){
            $ansarQuery->where('ku.id', $unit);
        }
        if($thana!='all'){
            $ansarQuery->where('kt.id', $thana);
        }
        $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at',[$backTime,$recentTime]);
        $total = $ansarQuery->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
        //return DB::getQueryLog();
        return Response::json(['total' => $total]);
    }

    public static function getAllRecentAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $recentTime = Carbon::now();
        $backTime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);

    }

    public static function getAllRecentAnsarCount($unit, $thana, $division = null)
    {
        $recentTime = Carbon::now()->addHours(6);
        $backTime = Carbon::now()->addHours(6)->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_parsonal_info.created_at', [$backTime, $recentTime])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);

    }

    public static function getRecentTotalFreeAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function getRecentTotalFreeAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now()->addHours(6);;
        $backtime = Carbon::now()->addHours(6)->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_status_info.free_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.pannel_status', 1);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_thana.id', $thana);
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_units.id', $unit);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit);

            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_parsonal_info.division_id', $division);
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division);
            }
        }
        $ansars = $ansarQuery->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
            ->distinct()
            ->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
                'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_panel_info.created_at', 'tbl_panel_info.memorandum_id')->skip($offset)->limit($limit)->get();
        //return DB::getQueryLog();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'pannel']);
    }

    public static function getRecentTotalPaneledAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now()->addHours(6);;
        $backtime = Carbon::now()->addHours(6)->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.pannel_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_panel_info.panel_date', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_units.unit_name_bng as unit')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function getRecentTotalNotVerifiedAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now()->addHours(6);;
        $backtime = Carbon::now()->addHours(6)->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->whereBetween('tbl_ansar_parsonal_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('ot.id', $thana)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('ou.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('ot.id', $thana)->where('ou.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('ou.division_id', $division)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('ou.division_id', $division)
                    ->where('ot.id', $thana)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('ou.id', $unit)->where('ou.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->join('tbl_thana as ot', 'ou.id', '=', 'ot.unit_id')
                    //->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('ot.id', $thana)->where('ou.id', $unit)->where('ou.division_id', $division)
                    ->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_sms_offer_info.sms_send_datetime', 'ou.unit_name_eng as offer_unit')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'offer']);
    }

    public static function getRecentTotalOfferedAnsarCount($unit, $thana, $division = null)
    {
        DB::enableQueryLog();
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_units.id', $unit)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '!=', 'tbl_sms_offer_info.ansar_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        //return DB::getQueryLog();
        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalRestAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_rest_info.rest_date')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'rest']);
    }

    public static function getRecentTotalRestAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now()->addHours(6);;
        $backtime = Carbon::now()->addHours(6)->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_rest_info.updated_at', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        }
        $ansars = $ansarQuery->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
            'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_freezing_info.freez_reason', 'tbl_freezing_info.freez_date')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'freeze']);
    }

    public static function getRecentTotalFreezedAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.freezing_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_thana.id', $thana);
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)->where('tbl_units.id', $unit);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit);
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana);
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division);
            }
        }
        $ansars = $ansarQuery->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
            ->distinct()->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
                'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_blocklist_info.comment_for_block', 'tbl_blocklist_info.date_for_block')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'block']);
    }

    public static function getRecentTotalBlockedAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now()->addHours(6);;
        $backtime = Carbon::now()->addHours(6)->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_thana.id', $thana)->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)->where('tbl_units.id', $unit)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)
                    ->whereBetween('tbl_blocklist_info.date_for_block', [$backtime, $now])
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana);
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_units.id', $unit);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit);
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.black_list_status', 1);
            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.black_list_status', 1)
                    ->where('tbl_thana.id', $thana);
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_units.id', $unit);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit);
            }
        }
        $ansars = $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
            ->distinct()
            ->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
                'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_blacklist_info.black_list_comment as reason', 'tbl_blacklist_info.black_listed_date as date')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'black']);
    }

    public static function getRecentTotalBlackedAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now()->addHours(6);;
        $backtime = Carbon::now()->addHours(6)->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.black_list_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        if (is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.embodied_status', 1);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_thana.id', $thana);
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_units.id', $unit);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit);
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.embodied_status', 1);

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_thana.id', $thana);
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_units.id', $unit);
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit);
            }
        }
        $ansars = $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
            ->distinct()
            ->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
                'tbl_designations.name_bng as rank', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id', 'tbl_kpi_info.kpi_name')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'embodied']);
    }

    public static function getRecentTotalEmbodiedAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now()->addHours(6);;
        $backtime = Carbon::now()->addHours(6)->subDays(7);
        if (!is_null($division)) {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.division_id', $division)
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        } else {
            if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();

            } else if (strcasecmp($unit, 'all') == 0 && strcasecmp($thana, 'all') != 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->where('tbl_thana.id', $thana)
                    ->distinct();
            } else if (strcasecmp($unit, 'all') != 0 && strcasecmp($thana, 'all') == 0) {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            } else {
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_thana.id', $thana)->where('tbl_units.id', $unit)->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])
                    ->distinct();
            }
        }
        $total = $ansarQuery->count('tbl_ansar_parsonal_info.ansar_id');

        return Response::json(['total' => $total]);
    }

    public static function getRecentTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division = null)
    {
        $now = Carbon::now();
        $backtime = Carbon::now()->subDays(7);
        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_ansar_status_info.embodied_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if($division&&$division!='all'){
            $ansarQuery->where('tbl_kpi_info.division_id', '!=', $division);
        }
        if($unit!='all'){
            $ansarQuery->where('ku.id', '!=', $unit);
            $ansarQuery->where('pu.id', '=', $unit);
        }
        if($thana!='all'){
            $ansarQuery->where('kt.id', '!=', $thana);
            $ansarQuery->where('pt.id', '=', $thana);
        }
        $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at',[$backtime,$now]);
        $ansars = $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at', [$backtime, $now])->distinct()
            ->select('tbl_ansar_parsonal_info.ansar_id as id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date',
                'tbl_designations.name_bng as rank', 'pu.unit_name_bng as unit', 'pt.thana_name_bng as thana', 'tbl_embodiment.joining_date', 'tbl_embodiment.memorandum_id')->skip($offset)->limit($limit)->get();
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars, 'type' => 'embodied']);
    }

    public static function getRecentTotalDiffEmbodiedAnsarCount($unit, $thana, $division = null)
    {
        $now = Carbon::now()->addHours(6);;
        $backtime = Carbon::now()->addHours(6)->subDays(7);

        $ansarQuery = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
            ->where('tbl_ansar_status_info.embodied_status', 1)
            ->where('tbl_ansar_status_info.block_list_status', 0);
        if($division&&$division!='all'){
            $ansarQuery->where('tbl_kpi_info.division_id', '!=', $division);
        }
        if($unit!='all'){
            $ansarQuery->where('ku.id', '!=', $unit);
            $ansarQuery->where('pu.id', '=', $unit);
        }
        if($thana!='all'){
            $ansarQuery->where('kt.id', '!=', $thana);
            $ansarQuery->where('pt.id', '=', $thana);
        }
        $ansarQuery->whereBetween('tbl_ansar_status_info.updated_at',[$backtime,$now]);
        $total = $ansarQuery->distinct()->count('tbl_ansar_parsonal_info.ansar_id');

        return Response::json(['total' => $total]);
    }

    public static function threeYearsOverAnsarList($offset, $limit, $unit, $ansar_rank, $ansar_sex)
    {
        $ansarQuery = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.service_ended_date', '<', Carbon::now())
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded');
        if ($unit != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($ansar_rank != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_designations.id', '=', $ansar_rank);
        }
        if ($ansar_sex != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_ansar_parsonal_info.sex', '=', $ansar_sex);
        }
//        }
        $ansars = $ansarQuery->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date','tbl_designations.id as did',
            'tbl_embodiment.service_ended_date as se_date', 'tbl_kpi_info.kpi_name as kpi', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.data_of_birth as birth_date', 'tbl_units.unit_name_bng as unit', 'tbl_designations.name_bng as rank')->skip($offset)->limit($limit)->get();
        return View::make('HRM::Report.selected_three_years_over_list_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);

    }

    public static function threeYearsOverAnsarCount($unit, $ansar_rank, $ansar_sex)
    {
        DB::enableQueryLog();
//        if (is_null($unit) && is_null($ansar_rank) && is_null($ansar_sex)) {
//            $ansarQuery = DB::table('tbl_embodiment')
//                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
//                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
//                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
//                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                ->where('tbl_embodiment.service_ended_date', '<', Carbon::today()->addHours(6))
//                ->where('tbl_embodiment.emboded_status', '=', 'Emboded');
//        } else {

        $ansarQuery = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.service_ended_date', '<', Carbon::now())
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded');
        if ($unit != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($ansar_rank != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_designations.id', '=', $ansar_rank);
        }
        if ($ansar_sex != 'all') {
            $ansarQuery = $ansarQuery->where('tbl_ansar_parsonal_info.sex', '=', $ansar_sex);
        }
//        }
        $total = $ansarQuery->groupBy('tbl_designations.id')->orderBy('tbl_designations.id')->select(DB::raw('count(tbl_designations.id) as t'))->pluck('t');
//        return DB::getQueryLog();
        return Response::json(['total' => $total]);
    }


    public static function disembodedAnsarListforReport($offset, $limit, $from_date, $to_date, $unit, $thana)
    {
        if ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $ansarQuery = DB::table('tbl_embodiment_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))->distinct();

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $ansarQuery = DB::table('tbl_embodiment_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))
                ->where('tbl_kpi_info.unit_id', '=', $unit)->distinct();

        } elseif ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $ansarQuery = DB::table('tbl_embodiment_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))->distinct();

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $ansarQuery = DB::table('tbl_embodiment_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))
                ->where('tbl_kpi_info.thana_id', '=', $thana)->distinct();
        }
        $ansars = $ansarQuery->select('tbl_embodiment_log.ansar_id as id', 'tbl_embodiment_log.reporting_date as r_date', 'tbl_embodiment_log.joining_date as j_date', 'tbl_embodiment_log.release_date as re_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
            'tbl_units.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi', 'tbl_disembodiment_reason.reason_in_bng as reason')->skip($offset)->limit($limit)->get();
        return View::make('HRM::Report.selected_ansar_disembodiment_report')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function disembodedAnsarListforReportCount($from_date, $to_date, $unit, $thana)
    {
        if ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $ansarQuery = DB::table('tbl_embodiment_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))->distinct();

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $ansarQuery = DB::table('tbl_embodiment_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))
                ->where('tbl_kpi_info.unit_id', '=', $unit)->distinct();

        } elseif ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $ansarQuery = DB::table('tbl_embodiment_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))->distinct();

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $ansarQuery = DB::table('tbl_embodiment_log')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                ->whereBetween('tbl_embodiment_log.release_date', array($from_date, $to_date))
                ->where('tbl_kpi_info.thana_id', '=', $thana)->distinct();
        }
        $total = $ansarQuery->count('tbl_embodiment_log.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function embodedAnsarListforReport($offset, $limit, $from_date, $to_date, $unit, $thana)
    {
        DB::enableQueryLog();
        $ansarQuery = "";
        if ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $ansarQuery = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date));

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $ansarQuery = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date))
                ->where('tbl_kpi_info.unit_id', '=', $unit);

        } elseif ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $ansarQuery = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date));
//                ->where('tbl_kpi_info.thana_id', '=', $thana);

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $ansarQuery = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date))
//                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        }

        $ansars = $ansarQuery->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
            'tbl_units.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi')->skip($offset)->limit($limit)->get();
        //return DB::getQueryLog();
        return View::make('HRM::Report.selected_ansar_embodiment_report')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'ansars' => $ansars]);
    }

    public static function embodedAnsarListforReportCount($from_date, $to_date, $unit, $thana)
    {
        if ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $ansarQuery = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date));

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $ansarQuery = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date))
                ->where('tbl_kpi_info.unit_id', '=', $unit);

        } elseif ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $ansarQuery = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date));
//                ->where('tbl_kpi_info.thana_id', '=', $thana);

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $ansarQuery = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->whereBetween('tbl_embodiment.joining_date', array($from_date, $to_date))
//                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        $total = $ansarQuery->count('tbl_embodiment.ansar_id');
        return Response::json(['total' => $total]);
    }

    public static function kpiInfo($offset, $limit, $division, $unit, $thana)
    {
        $kpiQuery = "";
        if ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id');

        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.thana_id', '=', $thana);

        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.unit_id', '=', $unit);

        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.unit_id', '=', $unit);
        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.division_id', '=', $division);
        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.division_id', '=', $division)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.division_id', '=', $division)
                ->where('tbl_kpi_info.unit_id', '=', $unit);
        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.division_id', '=', $division)
                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        }

        $kpis = $kpiQuery->select('tbl_kpi_info.id', 'tbl_kpi_info.status_of_kpi', 'tbl_kpi_info.kpi_name as kpi_bng', 'tbl_kpi_info.kpi_name_eng as kpi_eng', 'tbl_kpi_info.kpi_address as address', 'tbl_kpi_info.kpi_contact_no as contact', 'tbl_division.division_name_eng as division_eng', 'tbl_division.division_name_bng as division_bng', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')->orderBy('tbl_kpi_info.id', 'asc')->skip($offset)->limit($limit)->get();
//        return View::make('kpi.selected_kpi_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);

    }

    public static function kpiInfoCount($division, $unit, $thana)
    {
        DB::enableQueryLog();
        $kpiQuery = "";
        if ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id');

        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.thana_id', '=', $thana);

        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.unit_id', '=', $unit);

        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.unit_id', '=', $unit);
        } elseif ((strcasecmp($division, "all") == 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.division_id', '=', $division);
        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.division_id', '=', $division)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.division_id', '=', $division)
                ->where('tbl_kpi_info.unit_id', '=', $unit);
        } elseif ((strcasecmp($division, "all") != 0) && (strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.division_id', '=', $division)
                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        $total = $kpiQuery->count('tbl_kpi_info.id');
//        print_r(DB::getQueryLog());
        return Response::json(['total' => $total]);
    }

    public static function withdrawnKpiInfo($offset, $limit, $unit, $thana,$division)
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

    public static function withdrawnKpiInfoCount($unit, $thana,$division)
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

    public static function inactiveKpiInfo($offset, $limit, $unit, $thana)
    {

        if ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.withdraw_status', '=', 1);

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.withdraw_status', '=', 1)
                ->where('tbl_kpi_info.unit_id', '=', $unit);

        } elseif ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.withdraw_status', '=', 1)
                ->where('tbl_kpi_info.thana_id', '=', $thana);

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.withdraw_status', '=', 1)
                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        }

        $kpis = $kpiQuery->select('tbl_kpi_info.id', 'tbl_kpi_info.kpi_name as kpi', 'tbl_kpi_info.withdraw_status', 'tbl_kpi_detail_info.kpi_withdraw_date as date', 'tbl_division.division_name_eng as division', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')->skip($offset)->limit($limit)->get();
//        return View::make('kpi.selected_kpi_view')->with(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);
        return Response::json(['index' => ((ceil($offset / $limit)) * $limit) + 1, 'kpis' => $kpis]);

    }

    public static function inactiveKpiInfoCount($unit, $thana)
    {
        DB::enableQueryLog();
        if ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.withdraw_status', '=', 1);

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") == 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.withdraw_status', '=', 1)
                ->where('tbl_kpi_info.unit_id', '=', $unit);

        } elseif ((strcasecmp($unit, "all") == 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.withdraw_status', '=', 1)
                ->where('tbl_kpi_info.thana_id', '=', $thana);

        } elseif ((strcasecmp($unit, "all") != 0) && (strcasecmp($thana, "all") != 0)) {
            $kpiQuery = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.withdraw_status', '=', 1)
                ->where('tbl_kpi_info.unit_id', '=', $unit)
                ->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        $total = $kpiQuery->count('tbl_kpi_info.id');
//        print_r(DB::getQueryLog());
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

        $units = $unitQuery->select('tbl_units.id', 'tbl_units.unit_name_eng', 'tbl_units.unit_name_bng', 'tbl_units.unit_code', 'tbl_division.division_name_eng', 'tbl_division.division_code')->skip($offset)->limit($limit)->get();
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
        $total = $unitQuery->count('tbl_units.id');
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
}