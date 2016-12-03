<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 11/22/2016
 * Time: 11:07 AM
 */

namespace App\Helper;


use App\modules\HRM\Models\OfferQuota;
use App\modules\HRM\Models\OfferSMS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Helper
{
    public static function getOfferQuota($user){
        if($user->type==22){
            $offered = OfferSMS::where('district_id', $user->district_id)->count('ansar_id');
            $embodied_ansar_total = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info','tbl_kpi_info.id','=','tbl_embodiment.kpi_id')
                ->where('tbl_kpi_info.unit_id',$user->district_id)
                ->where('tbl_embodiment.emboded_status','Emboded')->count();
            $quota = OfferQuota::where('unit_id',$user->district_id)->first();
            if(isset($quota->quota))
            $offer_limit = (($quota->quota*$embodied_ansar_total)/100)-$offered;
            return $offer_limit<0?0:$offer_limit;
        }
        return false;
    }
}