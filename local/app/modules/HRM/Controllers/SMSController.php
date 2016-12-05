<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
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
        $body = Input::get('body');
        $sender_no = Input::get('sender');
        $body_part = explode(' ', $body);
        Log::info("SMS BODY" . $body);
        SMSLog::create(Input::all());
        if (strcasecmp($body_part[0], 'ans') == 0) {
            Log::info("SMS NO" . $sender_no);
            if (count($body_part) > 1) {
                switch ($body_part[1]) {
                    case 'YES':
                    case 'yes':
                        return $this->changeAnsarOfferStatus($sender_no, 'YES');
                    case 'NO':
                    case 'no':
                        return $this->changeAnsarOfferStatus($sender_no, 'NO');
                    default:
                        return "Invalid SMS Format";
                }
            }
            return "Invalid SMS Format";
        } else {
            if (count($body_part) > 1) {
                switch ($body_part[0]) {
                    case 'S':
                    case 's':
                        return $this->getAnsarStatus((int)$body_part[1]);
                    case 'E':
                    case 'e':
                        return $this->getAnsarDetail((int)$body_part[1]);
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
        if (strcasecmp(substr($phone, 0, 2), '88') == 0) {
            $phone = substr($phone, 2);
        } else if (strcasecmp(substr($phone, 0, 3), '+88') == 0) {
            $phone = substr($phone, 3);
        }
        //return $phone;
        $ansar = PersonalInfo::where('mobile_no_self', $phone)->first();
        $action_date = Carbon::now();
        if ($ansar) {
            Log::info("SMS RECEIVE : ANSAR FOUND" );
            switch ($type) {
                case 'YES':
                    DB::beginTransaction();
                    try {
                        $offered_ansar = OfferSMS::where('ansar_id', $ansar->ansar_id)->first();
                        if ($offered_ansar) {
                            $yes = new SmsReceiveInfoModel;
                            $yes->ansar_id = $ansar->ansar_id;
                            $yes->sms_received_datetime = $action_date;
                            $yes->sms_status = 'ACCEPTED';
                            $yes->offered_district = $offered_ansar->district_id;
                            $yes->sms_send_datetime = $offered_ansar->sms_send_datetime;
                            $yes->sms_end_datetime = $offered_ansar->sms_end_datetime;
                            $yes->save();
                            switch ($offered_ansar->come_from) {
                                case 'panel':
                                    $this->removeFromPanel($ansar->ansar_id);
                                    break;
                                case 'rest':
                                    $this->removeFromRest($ansar->ansar_id);
                                    break;
                            }
                            $offered_ansar->delete();
                            $dis = District::find($offered_ansar->district_id)->unit_name_eng;
                            DB::commit();
                            return "Please Join in " . $dis . " by " . Carbon::now()->addDay(7)->format('d-m-Y') . ' with Smart Card. Otherwise your offer will be cancelled -DC ' . strtoupper($dis);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return "An error occur while accepting offer. Please try again some time.";
                    }
                    return "No Ansar found with this id in offer list";
                case 'NO':
                    DB::beginTransaction();
                    try {
                        $offered_ansar = OfferSMS::where('ansar_id', $ansar->ansar_id)->first();
                        if ($offered_ansar) {
                            $status_info = AnsarStatusInfo::where('ansar_id', $ansar->ansar_id)->first();
                            $offer_log = new OfferSmsLog;
                            switch ($offered_ansar->come_from) {
                                case 'panel':
                                    $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->select('old_memorandum_id')->first();
                                    $panel_info = new PanelModel;
                                    $panel_info->ansar_id = $ansar->ansar_id;
                                    $panel_info->panel_date = Carbon::now();
                                    $panel_info->come_from = 'Offer';
                                    $panel_info->ansar_merit_list = 1;
                                    $panel_info->memorandum_id = $panel_log->old_memorandum_id;
                                    $panel_info->save();
                                    $status_info->offer_sms_status = 0;
                                    $status_info->pannel_status = 1;
                                    $status_info->save();
                                    break;
                                case 'rest':
                                    $status_info->offer_sms_status = 0;
                                    $status_info->rest_status = 1;
                                    $status_info->save();
                                    break;

                            }
                            $offer_log->offered_date = $offered_ansar->sms_send_datetime;
                            $offer_log->ansar_id = $ansar->ansar_id;
                            $offer_log->reply_type = 'No';
                            $offer_log->offered_district = $offered_ansar->district_id;
                            $offer_log->action_user_id = $offered_ansar->action_user_id;
                            $offer_log->action_date = $action_date;
                            $offer_log->save();
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

    function getAnsarStatus($id)
    {
        $ansar = AnsarStatusInfo::where('ansar_id', $id)->first();
        if (!$ansar) {
            return "No Ansar Found With This ID: " . $id;
        }
        switch (1) {
            case $ansar->block_list_status:
                return "Your Status Is BLOCK";
            case $ansar->black_list_status:
                return "Your Status Is BLACKED";
            case $ansar->free_status:
                return "Your Status Is FREE";
            case $ansar->pannel_status:
                return "Your Status Is PANEL";
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

        }
    }

    function getAnsarDetail($id)
    {
        $ansar = PersonalInfo::where('ansar_id', $id)->first();
        if (!$ansar) {
            return 'No Ansar Exists With This ID :' . $id;
        }
        $info = 'Name : ' . $ansar->ansar_name_eng . ', Father Name : ' . $ansar->father_name_eng . ', Mother Name : ' . $ansar->mother_name_eng . ', NID : ' . $ansar->national_id_no . ', DOB : ' . date('d-M-y', strtotime($ansar->data_of_birth)) . ', Mobile : ' . $ansar->mobile_no_self;
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
//    function getSMSStatus()
//    {
//
//        $offer = OfferSMS::where('message_id', Input::get('SmsSid'))->first();
//        Log::info(Input::get('SmsSid'));
//        switch (Input::get('MessageStatus')) {
//            case 'delivered':
//            case 'Delivered':
//                $offer->sms_status = 'Delivered';
//                break;
//            case 'failed':
//            case 'Failed':
//                $offer->sms_status = 'Failed';
//                break;
//            case 'sent':
//            case 'Sent':
//                $offer->sms_status = 'Send';
//                break;
//            default:
//                $offer->sms_status = 'Queue';
//        }
//        $offer->save();
//    }
}
