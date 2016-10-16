<?php

namespace App\Console;

use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->call(function () {
//            Log::info("called");
//            //return;
//            $user = env('SSL_USER_ID');
//            $pass = env('SSL_PASSWORD');
//            $sid = env('SSL_SID');
//            $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
//            $offered_ansar = OfferSMS::where('sms_try', 0)->where('sms_status', 'Queue')->take(10)->get();
//            foreach ($offered_ansar as $offer) {
//                $a = $offer->ansar->first();
//                $dis = $offer->district->unit_name_eng;
//                $body = 'You (ID:' . $offer->ansar_id . ') are offered for ' . $dis . ' as Rank ' . $a->designation->name_eng . ' Please type (anst YES/anst NO) and send to 6969 within 48 hours. Otherwise your offer will be cancelled - DC ' . strtoupper($dis);
//                $phone = '88' . trim($a->mobile_no_self);
//                $param = "user=$user&pass=$pass&sms[0][0]=$phone&sms[0][1]=" . urlencode($body) . "&sid=$sid";
//                $crl = curl_init();
//                curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
//                curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
//                curl_setopt($crl, CURLOPT_URL, $url);
//                curl_setopt($crl, CURLOPT_HEADER, 0);
//                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
//                curl_setopt($crl, CURLOPT_POST, 1);
//                curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
//                $response = curl_exec($crl);
//                curl_close($crl);
//                $r = Parser::xml($response);
//                Log::info(json_encode($r));
//                $offer->sms_try += 1;
//                if (strcasecmp($r['PARAMETER'], 'OK') == 0) {
//                    $offer->sms_status = 'Send';
//                    $offer->save();
//                } else {
//                    $offer->sms_status = 'Failed';
//                    $offer->save();
//                }
//            }
//
//        })->everyMinute()->withoutOverlapping();
//        $schedule->call(function () {
//            $user = env('SSL_USER_ID');
//            $pass = env('SSL_PASSWORD');
//            $sid = env('SSL_SID');
//            $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
//            $offered_cancel = OfferCancel::where('sms_status', 0)->take(10)->get();
//            foreach ($offered_cancel as $offer) {
//                $a = $offer->ansar->first();
//                $body = 'Your offer is cancelled';
//                $phone = '88' . trim($a->mobile_no_self);
//                $param = "user=$user&pass=$pass&sms[0][0]=$phone&sms[0][1]=" . urlencode($body) . "&sid=$sid";
//                $crl = curl_init();
//                curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
//                curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
//                curl_setopt($crl, CURLOPT_URL, $url);
//                curl_setopt($crl, CURLOPT_HEADER, 0);
//                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
//                curl_setopt($crl, CURLOPT_POST, 1);
//                curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
//                $response = curl_exec($crl);
//                curl_close($crl);
//                $r = simplexml_load_string($response);
//                Log::info(json_encode($r));
//                $offer->sms_status = 1;
//                $offer->save();
//            }
//
//        })->everyMinute()->withoutOverlapping();
        $schedule->call(function () {

            $offeredAnsars = OfferSMS::where('sms_end_datetime', '<=', Carbon::now())->get();
            foreach ($offeredAnsars as $ansar) {
                Log::info("CALLED START: OFFER NO REPLY".$ansar->ansar_id);
                $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->select('old_memorandum_id')->first();
                $ansar->log()->save(new OfferSmsLog([
                    'offered_date'=>$ansar->sms_send_datetime,
                    'offered_district'=>$ansar->district_id,
                    'action_date'=>Carbon::now(),
                    'reply_type'=>'No Reply',
                ]));
                $ansar->status()->update([
                    'offer_sms_status'=>0,
                    'pannel_status'=>1,
                ]);
                $ansar->panel()->save(new PanelModel([
                    'memorandum_id'=>$panel_log->old_memorandum_id,
                    'panel_date'=>Carbon::now(),
                    'come_from'=>'Offer',
                    'ansar_merit_list'=>1,
                ]));
                $ansar->delete();
            }
        })->everyFiveMinutes()->name("revert_offer")->withoutOverlapping();
//        $schedule->call(function () {
//            $withdraw_kpi_ids = KpiDetailsModel::where('kpi_withdraw_date', '<=', Carbon::now())->get();
//            foreach ($withdraw_kpi_ids as $withdraw_kpi_id) {
//                if (!is_null($withdraw_kpi_id->kpi_withdraw_date)) {
//                    $kpi_info = KpiGeneralModel::find($withdraw_kpi_id->kpi_id);
//                    $kpi_info->withdraw_status = 1;
//                    $kpi_info->save();
//                    $withdraw_kpi_id->kpi_withdraw_date = NULL;
//                    $withdraw_kpi_id->save();
//                    $embodiment_info_exist = EmbodimentModel::where('kpi_id', $withdraw_kpi_id->kpi_id)->first();
//                    if (is_null($embodiment_info_exist->kpi_id)) {
//                        $embodiment_infos = EmbodimentModel::where('kpi_id', $withdraw_kpi_id->kpi_id)->get();
//                        foreach ($embodiment_infos as $embodiment_info) {
//                            $freeze_info_update = new FreezingInfoModel();
//                            $freeze_info_update->ansar_id = $embodiment_info->ansar_id;
//                            $freeze_info_update->freez_reason = "Guard Withdraw";
//                            $freeze_info_update->freez_date = Carbon::now();
//                            $freeze_info_update->kpi_id = $withdraw_kpi_id->kpi_id;
//                            $freeze_info_update->ansar_embodiment_id = $embodiment_info->id;
//                            $freeze_info_update->save();
//                            $embodiment_info->emboded_status = "Freeze";
//                            $embodiment_info->save();
//                            AnsarStatusInfo::where('ansar_id', $embodiment_info->ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 1]);
//                        }
//                    }
//                }
//            }
//        })->dailyAt("00:00")->withoutOverlapping();
    }
}
