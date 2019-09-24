<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Facades\GlobalParameterFacades;
use App\Helper\GlobalParameter;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\OfferSMSStatus;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\SMSLog;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class SMSController extends Controller
{
    //
    function sendSMS()
    {

    }

    function receiveSMS()
    {
        $body = preg_replace('/\s+/',' ',Input::get('body'));
        $sender_no = Input::get('sender');
        $body_part = explode(' ', $body);
        Log::info("SMS BODY" . $body);
        ///SMSLog::create(Input::all());
        if (strcasecmp($body_part[0], 'ans') == 0) {
            Log::info("SMS NO" . $sender_no);
            if (count($body_part) > 1) {
                switch (strtolower($body_part[1])) {
                    case 'yes':
                        return $this->changeAnsarOfferStatus($sender_no, 'YES');
                    case 'no':
                        return $this->changeAnsarOfferStatus($sender_no, 'NO');
                    default:
                        return "Invalid SMS Format";
                }
            }
            return "Invalid SMS Format";
        } else {
            if (count($body_part) > 1) {
                Log::info("SMS NO STATUS" . $sender_no);
                switch ($body_part[0]) {
                    case 'S':
                    case 's':
                        return $this->getAnsarStatus((int)$body_part[1],$sender_no);
                    case 'E':
                    case 'e':
                        return $this->getAnsarDetail((int)$body_part[1],$sender_no);
                    default:
                        return "Invalid SMS Format";
                }
            }
            return "Invalid SMS Format";
        }

        //DB::table('test')->insert(['body'=>$body,'sender'=>$sender_no]);


    }

    function changeAnsarOfferStatus($phone, $type)
    {
        //return "System in maintenance mode";
        if (strcasecmp(substr($phone, 0, 2), '88') == 0) {
            $phone = substr($phone, 2);
        } else if (strcasecmp(substr($phone, 0, 3), '+88') == 0) {
            $phone = substr($phone, 3);
        }
//        return $phone;
        $ansar = PersonalInfo::where('mobile_no_self', $phone)->pluck('ansar_id');
        $action_date = Carbon::now();
        $maximum_offer_limit = (int)GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT)-1;
        if (count($ansar)>0) {
            Log::info("SMS RECEIVE : ANSAR FOUND ".$ansar );
            switch ($type) {
                case 'YES':
                    DB::beginTransaction();
                    try {
                        $offered_ansar = OfferSMS::whereIn('ansar_id', $ansar)->first();

//                        return $offered_ansar->ansar_id;
                        if ($offered_ansar) {
                            $yes = new SmsReceiveInfoModel;
                            $yes->ansar_id = $offered_ansar->ansar_id;
                            $yes->sms_received_datetime = $action_date;
                            $yes->sms_status = 'ACCEPTED';
                            $yes->offered_district = $offered_ansar->district_id;
                            $yes->sms_send_datetime = $offered_ansar->sms_send_datetime;
                            $yes->sms_end_datetime = $offered_ansar->sms_end_datetime;
                            $yes->save();
                            /*switch (strtolower($offered_ansar->come_from)) {
                                case 'panel':
                                    $this->removeFromPanel($offered_ansar->ansar_id);
                                    break;
                                case 'rest':
                                    $this->removeFromRest($offered_ansar->ansar_id);
                                    break;
                            }*/
                            //$offered_ansar->deleteCount();
                            //$offered_ansar->deleteOfferStatus();
                            $offered_ansar->saveLog();
                            $offered_ansar->delete();
                            $dis = District::find($offered_ansar->district_id)->unit_name_eng;
                            DB::commit();
                            return "Please Join in " . $dis . " by " . Carbon::now()->addDay(7)->format('d-m-Y') . ' with Smart Card. Otherwise your offer will be cancelled -DC ' . strtoupper($dis);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::info($e->getMessage());
                        return "An error occur while accepting offer. Please try again some time.";
                    }
                    return "No Ansar found with this id in offer list";
                case 'NO':
                    DB::beginTransaction();
                    try {
                        $offered_ansar = OfferSMS::whereIn('ansar_id', $ansar)->first();
                        if ($offered_ansar) {
                            $ansar = $offered_ansar->ansar;
                            $pa =$ansar->panel;
                            $count = $offered_ansar->getOfferCount();
                            if($count>=$maximum_offer_limit){
                                $offered_ansar->deleteCount();
                                $offered_ansar->deleteOfferStatus();
                                $offered_ansar->blockAnsarOffer();
                                $offered_ansar->saveLog();
                                $offered_ansar->status()->update([
                                    'offer_sms_status' => 0,
                                    'offer_block_status' => 1,
                                ]);
                                if($pa){
                                    $pa->panelLog()->save(new PanelInfoLogModel([
                                        'ansar_id' => $pa->ansar_id,
                                        'merit_list' => $pa->ansar_merit_list,
                                        'panel_date' => $pa->panel_date,
                                        're_panel_date' => $pa->re_panel_date,
                                        'old_memorandum_id' => !$pa->memorandum_id ? "N\A" : $pa->memorandum_id,
                                        'movement_date' => Carbon::today(),
                                        'come_from' => $pa->come_from,
                                        'move_to' => 'Offer',
                                        'go_panel_position' => $pa->go_panel_position,
                                        're_panel_position' => $pa->re_panel_position
                                    ]));
                                    $pa->delete();
                                }
                            }
                            else{
                                $offered_ansar->saveCount();
                                $offer_status = OfferSMSStatus::where(['ansar_id'=>$offered_ansar->ansar_id])->first();
                                switch ($offered_ansar->come_from) {
                                    case 'Panel':
                                        if($pa){
                                            $t = explode(",",$offer_status->offer_type);
                                            if(is_array($t)){
                                                $len = count($t);
                                                if($len>0&&strcasecmp($t[$len-1],"RE")==0){
                                                    $pa->re_panel_date = Carbon::now()->format('Y-m-d');
                                                }else if($len>0&&(strcasecmp($t[$len-1],"GB")==0||strcasecmp($t[$len-1],"DG")==0||strcasecmp($t[$len-1],"CG")==0)){
                                                    $pa->panel_date = Carbon::now()->format('Y-m-d');
                                                }

                                            }
                                            $pa->locked = 0;
                                            $pa->save();
                                        }
                                        else{
                                            $panel_log = PanelInfoLogModel::where('ansar_id', $offered_ansar->ansar_id)->select('old_memorandum_id')->first();
                                            $panel_info = new PanelModel;
                                            $panel_info->ansar_id = $offered_ansar->ansar_id;
                                            $panel_info->panel_date = Carbon::now();
                                            $panel_info->re_panel_date = Carbon::now();
                                            $panel_info->come_from = 'Offer';
                                            $panel_info->ansar_merit_list = 1;
                                            $panel_info->memorandum_id = $panel_log->old_memorandum_id;
                                            $panel_info->save();
                                        }
                                        $offered_ansar->status()->update([
                                            'offer_sms_status' => 0,
                                            'pannel_status' => 1,
                                        ]);
                                        break;
                                    case 'rest':
                                        $offered_ansar->status()->update([
                                            'offer_sms_status' => 0,
                                            'rest_status' => 1,
                                        ]);
                                        break;

                                }
                            }
                            $offered_ansar->saveLog('No');
                            $offered_ansar->delete();
                            DB::commit();
                            return "Your offer is cancelled";
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return "No Ansar found with this id in offer list";
                    }
                    return "No Ansar found with this id in offer list";
                    break;
            }
            if($type=="NO"){
                dispatch(new RearrangePanelPositionLocal());
                dispatch(new RearrangePanelPositionGlobal());
            }
        }
        else {
            return "No Ansar found with this id in offer list";
        }
    }

    function removeFromPanel($ansar_ids)
    {
        $pa = PanelModel::where('ansar_id', $ansar_ids)->first();
        $as = AnsarStatusInfo::where('ansar_id', $ansar_ids)->first();
        $as->pannel_status = 0;
        $as->offer_sms_status = 1;
        $as->save();
        if ($pa) {
            $pl = new PanelInfoLogModel;
            $pl->ansar_id = $pa->ansar_id;
            $pl->panel_date = $pa->panel_date;
            $pl->merit_list = 1;
            $pl->come_from = $pa->come_from;
            $pl->old_memorandum_id = $pa->memorandum_id;
            $pl->move_to = 'Offer';
            $pl->save();
            $pa->delete();
        }
    }

    function removeFromRest($ansar_ids)
    {
        $as = AnsarStatusInfo::where('ansar_id', $ansar_ids)->first();
        $as->rest->saveLog("Offer");
        $as->rest->delete();
    }

    function getAnsarStatus($id,$mobile_no)
    {
        $mobile_part = preg_split('/^\+?(88)?/',$mobile_no);
        $mobile_no = $mobile_part[count($mobile_part)-1];
        $a = PersonalInfo::where('ansar_id', $id)->where('mobile_no_self',$mobile_no)->first();
        if($a){
            $ansar = AnsarStatusInfo::where('ansar_id', $id)->first();
            if (!$ansar) {
                return "No Ansar Found With This mobile no :".$mobile_no." Please send sms from the register mobile no in Ansar";
            }
            switch (1) {
                case $ansar->block_list_status:
                    return "Your Status Is BLOCK";
                case $ansar->black_list_status:
                    return "Your Status Is BLACKED";
                case $ansar->free_status:
                    return "Your Status Is FREE";
                case $ansar->pannel_status:
                    $position = $this->getPanelPosition($id);
//                    return $position;
                    return "Your Status Is PANEL. ".$position;
                case $ansar->offer_sms_status:
                    return "Your Status Is OFFERED";
                case $ansar->embodied_status:
                    return "Your Status Is EMBODIED";
                case $ansar->freezing_status:
                    return "Your Status Is FREEZE";
                case $ansar->early_retierment_status:
                    return "Your Status Is EARLY RETIERMENT";
                case $ansar->rest_status:
                    return "Your Status Is REST";
                case $ansar->retierment_status:
                    return "Your Status Is RETIERMENT";
                case $ansar->offer_block_status:
                    return "Your Status Is OFFER BLOCK";
                default:
                    return "Your Status Is NOT VERIFIED";

            }
        }
        return "No Ansar Found With This mobile no :".$mobile_no." Please send sms from the register mobile no in Ansar";
    }

    function getAnsarDetail($id,$mobile_no)
    {
        $mobile_part = preg_split('/^\+?(88)?/',$mobile_no);
        $mobile_no = $mobile_part[count($mobile_part)-1];
        $ansar = PersonalInfo::where('ansar_id', $id)->where('mobile_no_self',$mobile_no)->first();
//        $ansar = PersonalInfo::where('ansar_id', $id)->first();
        if (!$ansar) {
            return 'No Ansar Exists With This ID or mobile no:' . $mobile_no;
        }
        $info = 'Name : ' . $ansar->ansar_name_eng . ', Father Name : ' . $ansar->father_name_eng . ', Mother Name : ' . $ansar->mother_name_eng . ', NID : ' . $ansar->national_id_no . ', DOB : ' . date('d-M-y', strtotime($ansar->data_of_birth)) . ', Mobile : ' . $ansar->mobile_no_self;
        Log::info($info);
        return $info;
    }

    function checkAnsarStatus($ansar_id)
    {
        $ansar_status = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
        if ($ansar_status->pannel_status) {
            $this->removeFromPanel($ansar_id);
        } else if ($ansar_status->rest_status) {

        }
    }
    function getPanelPosition($id)
    {
        $ansar = \App\modules\HRM\Models\PersonalInfo::with(['designation','status','panel'])
            ->where('ansar_id', $id)->first();
        if($ansar&&$ansar->status->pannel_status==1&&$ansar->status->block_list_status==0&&preg_match("/^[0-9]{11}$/",$ansar->mobile_no_self)){
            $go_offer_count = +GlobalParameterFacades::getValue('ge_offer_count');
            $re_offer_count = +GlobalParameterFacades::getValue('re_offer_count');
            $offerStatus = OfferSMSStatus::where('ansar_id',$id)->first();
            $re_panel_pos = "NIL";
            $go_panel_pos = "NIL";
            if($offerStatus&&substr_count($offerStatus->offer_type,'RE')<$re_offer_count){
                $re_panel_pos = $ansar->panel->re_panel_position;
            }
            if($offerStatus&&
                (substr_count($offerStatus->offer_type,'GB')+substr_count($offerStatus->offer_type,'DG')+substr_count($offerStatus->offer_type,'CG'))<$go_offer_count){
                $go_panel_pos = $ansar->panel->go_panel_position;
            }
            return 'Global position : ' . $go_panel_pos . " Regional position : " . $re_panel_pos;
        }else{
            return 'Your panel position not found. May be tou are over aged or invalid mobile no';
        }
        /*if ($ansar) {
            $status = true;
            $age = Carbon::parse($ansar->data_of_birth)->diffInYears(Carbon::now());
            if (strtolower($ansar->designation->code) == 'pc' || strtolower($ansar->designation->code) == 'apc') {
                $query = DB::table('tbl_ansar_status_info')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->where('tbl_ansar_parsonal_info.designation_id',$ansar->designation->id)
                    ->whereRaw('TIMESTAMPDIFF(YEAR,tbl_ansar_parsonal_info.data_of_birth,NOW())<' . $pc_apc_retirement_age)
                    ->where('pannel_status', 1)->where('block_list_status', 0)->select('tbl_panel_info.ansar_id', 'tbl_panel_info.panel_date','tbl_panel_info.id');
                $lquery = DB::table('tbl_ansar_status_info')
                                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                                    ->where('tbl_ansar_parsonal_info.designation_id',$ansar->designation->id)
                                    ->whereRaw('TIMESTAMPDIFF(YEAR,tbl_ansar_parsonal_info.data_of_birth,NOW())<' . $pc_apc_retirement_age)
                                    ->where('pannel_status', 1)->where('block_list_status', 0)->select('tbl_panel_info.ansar_id', 'tbl_panel_info.re_panel_date','tbl_panel_info.id');

            } else {
                $query = DB::table('tbl_ansar_status_info')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->whereRaw('TIMESTAMPDIFF(YEAR,tbl_ansar_parsonal_info.data_of_birth,NOW())<' . $ansar_retirement_age)
                    ->where('tbl_ansar_parsonal_info.designation_id',$ansar->designation->id)
                    ->where('pannel_status', 1)->where('block_list_status', 0)->select('tbl_panel_info.ansar_id', 'tbl_panel_info.panel_date','tbl_panel_info.id');
                $lquery = DB::table('tbl_ansar_status_info')
                    ->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->where('tbl_ansar_parsonal_info.designation_id',$ansar->designation->id)
                    ->whereRaw('TIMESTAMPDIFF(YEAR,tbl_ansar_parsonal_info.data_of_birth,NOW())<' . $ansar_retirement_age)
                    ->where('pannel_status', 1)->where('block_list_status', 0)->select('tbl_panel_info.ansar_id', 'tbl_panel_info.re_panel_date','tbl_panel_info.id');
            }
            $query->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^[0-9]{11}$"');
            $lquery->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^[0-9]{11}$"');
            $g = DB::table(DB::raw("(" . $query->toSql() . ") x,(select @a:=0) a"))->mergeBindings($query)->select(DB::raw('x.*,@a:=@a+1 as gp'))->orderBy('x.panel_date')->orderBy('x.id');
//            return $g->get();
            $gg = DB::table(DB::raw("(" . $g->toSql() . ") x"))->mergeBindings($g)->where('ansar_id', $id)->first();
            $division_id = $ansar->division_id;
            $lquery->where('tbl_ansar_parsonal_info.division_id', $division_id);
            $r = DB::table(DB::raw("(" . $lquery->toSql() . ") x,(select @a:=0) a"))->mergeBindings($lquery)->select(DB::raw('x.*,@a:=@a+1 as gp'))->orderBy('x.re_panel_date')->orderBy('x.id');
            $rr = DB::table(DB::raw("(" . $r->toSql() . ") x"))->mergeBindings($r)->where('ansar_id', $id)->first();
//                    return $r->get();
            if($gg&&$rr) return 'Global position : ' . $gg->gp . " Regional position : " . $rr->gp;
            else return 'Your panel position not found. May be tou are over aged or invalid mobile no';
        }*/
    }
}
