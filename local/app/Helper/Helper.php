<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 11/22/2016
 * Time: 11:07 AM
 */

namespace App\Helper;


use App\Helper\Facades\GlobalParameterFacades;
use App\modules\HRM\Models\OfferQuota;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Helper
{
    public static function getOfferQuota($user){
        if($user->type==22){
//            $offered = OfferSMS::where('district_id', $user->district_id)->count('ansar_id');
//            $offeredr = SmsReceiveInfoModel::where('offered_district', $user->district_id)->count('ansar_id');
//            $embodied_ansar_total = DB::table('tbl_embodiment')
//                ->join('tbl_kpi_info','tbl_kpi_info.id','=','tbl_embodiment.kpi_id')
//                ->where('tbl_kpi_info.unit_id',$user->district_id)
//                ->where('tbl_embodiment.emboded_status','Emboded')->count();
//            $quota = OfferQuota::where('unit_id',$user->district_id)->first();
//            if(isset($quota->quota))
//            $offer_limit = (($quota->quota*$embodied_ansar_total)/100)-(intval($offered)+intval($offeredr));
//            return intval(ceil($offer_limit<0?0:$offer_limit));
            DB::enableQueryLog();
            $offered = OfferSMS::where('district_id', $user->district_id)->count('ansar_id');
            $offeredr = SmsReceiveInfoModel::where('offered_district', $user->district_id)->count('ansar_id');
            $embodied_ansar_total = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info','tbl_kpi_info.id','=','tbl_embodiment.kpi_id')
                ->where('tbl_kpi_info.unit_id',$user->district_id)
                ->whereRaw('DATE_SUB(tbl_embodiment.service_ended_date,INTERVAL '.GlobalParameterFacades::getValue(GlobalParameter::OFFER_QUOTA_DAY).' '.strtoupper(GlobalParameterFacades::getUnit(GlobalParameter::OFFER_QUOTA_DAY)).') <=NOW() ')->count();
            $q = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info','tbl_kpi_info.id','=','tbl_embodiment.kpi_id')
                ->join('tbl_kpi_detail_info','tbl_kpi_info.id','=','tbl_kpi_detail_info.kpi_id')
                ->where('tbl_kpi_info.unit_id',$user->district_id)
                ->groupBy('tbl_kpi_info.id')
                ->select(DB::raw('(tbl_kpi_detail_info.total_ansar_given-COUNT(tbl_embodiment.ansar_id)) as vacency'));
            $vacency = DB::table(DB::raw("(".$q->toSql().") src"))->mergeBindings($q)->sum('vacency');
            //return DB::getQueryLog();
            //return $vacency;
            $offer_limit = ($vacency+$embodied_ansar_total)-(intval($offered)+intval($offeredr));
            return intval(ceil($offer_limit<0?0:$offer_limit));
        }
        return false;
    }

    public static function getAnsarRetirementAge(){
        $ansar_retirement_age = GlobalParameterFacades::getValue(\App\Helper\GlobalParameter::RETIREMENT_AGE_ANSAR);
        $ansar_retirement_age_unit = GlobalParameterFacades::getUnit(\App\Helper\GlobalParameter::RETIREMENT_AGE_ANSAR);

        switch ($ansar_retirement_age_unit){
            case 'Day':
                return floor(intval($ansar_retirement_age)/365);
            case 'Month':
                return floor(intval($ansar_retirement_age)/12);
            default:
                return $ansar_retirement_age;
        }
    }
    public static function getPcApcRetirementAge(){
        $ansar_retirement_age = GlobalParameterFacades::getValue(\App\Helper\GlobalParameter::RETIREMENT_AGE_PC_APC);
        $ansar_retirement_age_unit = GlobalParameterFacades::getUnit(\App\Helper\GlobalParameter::RETIREMENT_AGE_PC_APC);

        switch ($ansar_retirement_age_unit){
            case 'Day':
                return floor(intval($ansar_retirement_age)/365);
            case 'Month':
                return floor(intval($ansar_retirement_age)/12);
            default:
                return $ansar_retirement_age;
        }
    }
}