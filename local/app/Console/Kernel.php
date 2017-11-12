<?php

namespace App\Console;

use App\Console\Commands\NotificationServer;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nathanmac\Utilities\Parser\Facades\Parser;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        NotificationServer::class,
    ];

    /**
     * Kernel constructor.
     *

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {
            Log::info("called : send_offer");
//            //return;
            $user = env('SSL_USER_ID','ansarapi');
            $pass = env('SSL_PASSWORD','x83A7Z96');
            $sid = env('SSL_SID','ANSARVDP');
            $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
            $offered_ansar = OfferSMS::with(['ansar','district'])->where('sms_try', 0)->where('sms_status', 'Queue')->take(10)->get();
//            Log::info($offered_ansar);
            foreach ($offered_ansar as $offer) {
                DB::connection('hrm')->beginTransaction();
                try {

                    $a = $offer->ansar;
                    //Log::info($a);
//                    break;
                    $dis = $offer->district->unit_name_eng;
                    $body = 'You (ID:' . $offer->ansar_id . ') are offered for ' . $dis . ' as Rank ' . $a->designation->name_eng . ' Please type (ans YES/ans NO) and send to 6969 within 48 hours. Otherwise your offer will be cancelled - DC ' . strtoupper($dis);
                    $phone = '88' . trim($a->mobile_no_self);
                    $param = "user=$user&pass=$pass&sms[0][0]=$phone&sms[0][1]=" . urlencode($body) . "&sid=$sid";
                    $crl = curl_init();
                    curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($crl, CURLOPT_URL, $url);
                    curl_setopt($crl, CURLOPT_HEADER, 0);
                    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($crl, CURLOPT_POST, 1);
                    curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
                    $response = curl_exec($crl);
                    curl_close($crl);
                    $r = Parser::xml($response);
                    Log::info("SERVER RESPONSE : ".json_encode($r));
                    $offer->sms_try += 1;
                    if (isset($r['PARAMETER']) && strcasecmp($r['PARAMETER'], 'OK') == 0&&isset($r['SMSINFO']['MSISDN'])&&strcasecmp($r['SMSINFO']['MSISDN'],'88' . trim($a->mobile_no_self))==0) {
                        $offer->sms_status = 'Send';
                        $offer->save();
                    } else {
                        $offer->sms_status = 'Failed';
                        $offer->save();
                    }
                    DB::connection('hrm')->commit();
                } catch (\Exception $e) {
                    Log::info('OFFER SEND ERROR: ' . $e->getMessage());
                    DB::connection('hrm')->rollback();
                }
            }

        })->everyMinute()->name("send_offer_sms")->withoutOverlapping();
        $schedule->call(function () {
            Log::info("called : send_failed_offer");
//            //return;
            $user = env('SSL_USER_ID','ansarapi');
            $pass = env('SSL_PASSWORD','x83A7Z96');
            $sid = env('SSL_SID','ANSARVDP');
            $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
            $offered_ansar = OfferSMS::with(['ansar','district'])->where('sms_status', 'Failed')->take(10)->get();
//            Log::info($offered_ansar);
            foreach ($offered_ansar as $offer) {
                DB::connection('hrm')->beginTransaction();
                try {

                    $a = $offer->ansar;
                    //Log::info($a);
//                    break;
                    $dis = $offer->district->unit_name_eng;
                    $body = 'You (ID:' . $offer->ansar_id . ') are offered for ' . $dis . ' as Rank ' . $a->designation->name_eng . ' Please type (ans YES/ans NO) and send to 6969 within 48 hours. Otherwise your offer will be cancelled - DC ' . strtoupper($dis);
                    $phone = '88' . trim($a->mobile_no_self);
                    $param = "user=$user&pass=$pass&sms[0][0]=$phone&sms[0][1]=" . urlencode($body) . "&sid=$sid";
                    $crl = curl_init();
                    curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($crl, CURLOPT_URL, $url);
                    curl_setopt($crl, CURLOPT_HEADER, 0);
                    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($crl, CURLOPT_POST, 1);
                    curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
                    $response = curl_exec($crl);
                    curl_close($crl);
                    $r = Parser::xml($response);
                    Log::info("SERVER RESPONSE : ".json_encode($r));
                    $offer->sms_try += 1;
                    if (isset($r['PARAMETER']) && strcasecmp($r['PARAMETER'], 'OK') == 0&&isset($r['SMSINFO']['MSISDN'])&&strcasecmp($r['SMSINFO']['MSISDN'],'88' . trim($a->mobile_no_self))==0) {
                        $offer->sms_status = 'Send';
                        $offer->save();
                    } else {
                        $offer->sms_status = 'Failed';
                        $offer->save();
                    }
                    DB::connection('hrm')->commit();
                } catch (\Exception $e) {
                    Log::info('OFFER SEND ERROR: ' . $e->getMessage());
                    DB::connection('hrm')->rollback();
                }
            }

        })->everyMinute()->name("send_failed_offer")->withoutOverlapping();
        $schedule->call(function () {
            Log::info("called : offer_cancel");
            $user = env('SSL_USER_ID','ansarapi');
            $pass = env('SSL_PASSWORD','x83A7Z96');
            $sid = env('SSL_SID','ANSARVDP');
            $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
            $offered_cancel = OfferCancel::where('sms_status', 0)->take(10)->get();
            foreach ($offered_cancel as $offer) {
                $a = $offer->ansar;
                $body = 'Your offer is cancelled';
                $phone = '88' . trim($a->mobile_no_self);
                $param = "user=$user&pass=$pass&sms[0][0]=$phone&sms[0][1]=" . urlencode($body) . "&sid=$sid";
                $crl = curl_init();
                curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($crl, CURLOPT_URL, $url);
                curl_setopt($crl, CURLOPT_HEADER, 0);
                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($crl, CURLOPT_POST, 1);
                curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
                $response = curl_exec($crl);
                curl_close($crl);
                $r = simplexml_load_string($response);
                Log::info(json_encode($r));
                $offer->sms_status = 1;
                $offer->save();
            }

        })->everyMinute()->name("offer_cancel")->withoutOverlapping();
        $schedule->call(function () {
            Log::info("called : send_sms_to_selected_applicant");
            $messID        = rand(1000,9999);
            $messageID     = $messID;
            $apiUser       = 'join_ans_vdp';
            $apiPass       = 'shurjoSM123';

            $applicants = JobSelectedApplicant::with('applicant')->where('message_status','pending')->where('sms_status','on')->limit(10)->get();
            foreach ($applicants as $a) {


                if($a->applicant){
                    $sms_data = http_build_query(
                        array(
                            'API_USER' => $apiUser,
                            'API_PASSWORD' => $apiPass,
                            'MOBILE' => $a->applicant->mobile_no_self,
                            'MESSAGE' => $a->message,
                            'MESSAGE_ID' => $messageID
                        )
                    );

                    $ch = curl_init();
                    $url = "https://shurjobarta.shurjorajjo.com.bd/barta_api/api.php";
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);                //0 for a get request
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $sms_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    var_dump($response);
                }
            }

        })->everyMinute()->name("send_sms_to_selected_applicant")->withoutOverlapping();
        $schedule->call(function () {
            Log::info("REVERT OFFER");
            $offeredAnsars = OfferSMS::where('sms_end_datetime', '<=', Carbon::now())->get();
            foreach ($offeredAnsars as $ansar) {
                Log::info("CALLED START: OFFER NO REPLY" . $ansar->ansar_id);
                DB::beginTransaction();
                try {
                    switch($ansar->come_from){
                        case 'Panel':
                            $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->select('old_memorandum_id')->first();
                            $ansar->saveLog('No Reply');
                            $ansar->status()->update([
                                'offer_sms_status' => 0,
                                'pannel_status' => 1,
                            ]);
                            $ansar->panel()->save(new PanelModel([
                                'memorandum_id' => isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                                'panel_date' => Carbon::now(),
                                'come_from' => 'Offer',
                                'ansar_merit_list' => 1,
                            ]));
                            $ansar->delete();
                            break;
                        case 'rest':
                            $ansar->saveLog('No Reply');
                            $ansar->status()->update([
                                'rest_status' => 1,
                                'offer_sms_status' => 0,
                            ]);
                            $ansar->delete();
                            break;
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::info("ERROR: " . $e->getMessage());
                }
            }
        })->dailyAt("23:50")->name("revert_offer")->withoutOverlapping();
        $schedule->call(function () {

            $offeredAnsars = SmsReceiveInfoModel::all();
            $now = Carbon::now();
            foreach ($offeredAnsars as $ansar) {
                if($now->diffInDays(Carbon::parse($ansar->sms_received_datetime)) >=7){
                    Log::info("CALLED START: OFFER ACCEPTED" . $ansar->ansar_id);
                    DB::beginTransaction();
                    try {
                        $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->select('old_memorandum_id')->first();
                        $ansar->saveLog();
                        $ansar->status()->update([
                            'offer_sms_status' => 0,
                            'pannel_status' => 1,
                        ]);
                        $ansar->panel()->save(new PanelModel([
                            'memorandum_id' => isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                            'panel_date' => Carbon::now(),
                            'come_from' => 'Offer',
                            'ansar_merit_list' => 1,
                        ]));
                        $ansar->delete();
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollback();
                        Log::info("ERROR: " . $e->getMessage());
                    }
                }

            }
        })->dailyAt("23:50")->name("revert_offer_accepted")->withoutOverlapping();
        $schedule->call(function () {
            $withdraw_kpi_ids = KpiDetailsModel::where('kpi_withdraw_date', '<=', Carbon::now())->whereNotNull('kpi_withdraw_date')->get();
            foreach ($withdraw_kpi_ids as $withdraw_kpi_id) {
                $kpi_info = KpiGeneralModel::find($withdraw_kpi_id->kpi_id);
                $kpi_info->status_of_kpi = 0;
                $kpi_info->withdraw_status = 1;
                $kpi_info->save();
                $withdraw_kpi_id->kpi_withdraw_date = NULL;
                $withdraw_kpi_id->save();
                $embodiment_infos = EmbodimentModel::where('kpi_id', $withdraw_kpi_id->kpi_id)->get();
                foreach ($embodiment_infos as $embodiment_info) {
                    $freeze_info_update = new FreezingInfoModel();
                    $freeze_info_update->ansar_id = $embodiment_info->ansar_id;
                    $freeze_info_update->freez_reason = "Guard Withdraw";
                    $freeze_info_update->freez_date = Carbon::now();
                    $freeze_info_update->kpi_id = $withdraw_kpi_id->kpi_id;
                    $freeze_info_update->ansar_embodiment_id = $embodiment_info->id;
                    $freeze_info_update->save();
                    $embodiment_info->emboded_status = "Freeze";
                    $embodiment_info->save();
                    AnsarStatusInfo::where('ansar_id', $embodiment_info->ansar_id)->update(['embodied_status' => 0, 'freezing_status' => 1]);
                }
            }
        })->dailyAt("00:00")->name('withdraw_kpi')->withoutOverlapping();
        $schedule->call(function () {
            $rest_ansars = RestInfoModel::whereDate('active_date','<=',Carbon::today()->toDateString())->whereIn('disembodiment_reason_id',[1,2,8])->get();
            Log::info("REST to PANEl : CALLED");

            foreach($rest_ansars as $ansar){

                if(!in_array(AnsarStatusInfo::REST_STATUS,$ansar->status->getStatus())||in_array(AnsarStatusInfo::BLOCK_STATUS,$ansar->status->getStatus())||in_array(AnsarStatusInfo::BLACK_STATUS,$ansar->status->getStatus())) continue;
                DB::beginTransaction();
                try{
                    $panel_log = PanelInfoLogModel::where('ansar_id',$ansar->ansar_id)->orderBy('id','desc')->first();
                    PanelModel::create([
                        'ansar_id'=>$ansar->ansar_id,
                        'come_from'=>'Rest',
                        'panel_date'=>Carbon::today(),
                        'memorandum_id'=>isset($panel_log->old_memorandum_id)?$panel_log->old_memorandum_id:'N\A',
                        'ansar_merit_list'=>isset($panel_log->merit_list)?$panel_log->merit_list:'N\A',
                        'action_user_id'=>'0',
                    ]);
                    $ansar->status->update([
                        'pannel_status'=>1,
                        'rest_status'=>0,
                    ]);
                    $ansar->saveLog('Panel');
                    $ansar->delete();
                    DB::commit();
                    Log::info("REST to PANEl :".$ansar->ansar_id);
                }catch(\Exception $e){
                    DB::rollBack();
                    Log::info("REST to PANEl FAILED:".$ansar->ansar_id);
                }
            }
        })->twiceDaily(0,12)->name('rest_to_panel')->withoutOverlapping();
        $schedule->call(function () {
            $rest_ansars = RestInfoModel::whereRaw('FLOOR(DATEDIFF(rest_date,NOW())/365)>=1')->where('disembodiment_reason_id',5)->get();
            Log::info("REST to PANEl DICIPLINARY : CALLED");

            foreach($rest_ansars as $ansar){

                if(!in_array(AnsarStatusInfo::REST_STATUS,$ansar->status->getStatus())||in_array(AnsarStatusInfo::BLOCK_STATUS,$ansar->status->getStatus())||in_array(AnsarStatusInfo::BLACK_STATUS,$ansar->status->getStatus())) continue;
                DB::beginTransaction();
                try{
                    $panel_log = PanelInfoLogModel::where('ansar_id',$ansar->ansar_id)->orderBy('id','desc')->first();
                    PanelModel::create([
                        'ansar_id'=>$ansar->ansar_id,
                        'come_from'=>'Rest',
                        'panel_date'=>Carbon::today(),
                        'memorandum_id'=>isset($panel_log->old_memorandum_id)?$panel_log->old_memorandum_id:'N\A',
                        'ansar_merit_list'=>isset($panel_log->merit_list)?$panel_log->merit_list:'N\A',
                        'action_user_id'=>'0',
                    ]);
                    $ansar->status->update([
                        'pannel_status'=>1,
                        'rest_status'=>0,
                    ]);
                    $ansar->saveLog('Panel');
                    $ansar->delete();
                    DB::commit();
                    Log::info("REST to PANEl :".$ansar->ansar_id);
                }catch(\Exception $e){
                    DB::rollBack();
                    Log::info("REST to PANEl FAILED:".$ansar->ansar_id);
                }
            }
        })->twiceDaily(0,12)->name('rest_to_panel_disciplaney_action')->withoutOverlapping();
        $schedule->call(function(){

        })->twiceDaily(0,12)->name("ansar_retirement")->withoutOverlapping();
    }
}
