<?php

namespace App\Console;

use App\Console\Commands\NotificationServer;
use App\Helper\Facades\GlobalParameterFacades;
use App\Helper\Helper;
use App\Helper\GlobalParameter;
use App\Helper\SMSTrait;
use App\modules\HRM\Models\AnsarRetireHistory;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use App\modules\recruitment\Models\JobAcceptedApplicant;
use App\modules\recruitment\Models\JobCircular;
use App\modules\SD\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nathanmac\Utilities\Parser\Facades\Parser;

class Kernel extends ConsoleKernel
{
    use SMSTrait;
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
     *
     * /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {
            Log::info("called : send_offer");
            $offered_ansar = OfferSMS::with(['ansar', 'district'])->where('sms_try', 0)->where('sms_status', 'Queue')->take(10)->get();
            foreach ($offered_ansar as $offer) {
                DB::connection('hrm')->beginTransaction();
                try {

                    $a = $offer->ansar;
                    $maximum_offer_limit = (int)GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT);
                    $count = $offer->getOfferCount();
                    $dis = $offer->district->unit_name_eng;
                    $dc = $dis == "DMA HSIA" ? strtoupper("DHAKA AIRPORT") : strtoupper($dis);
                    $sms_end_date = Carbon::parse($offer->sms_end_datetime)->format('d-m-Y h:i:s A');
                    $body = "Apni (ID:{$offer->ansar_id}, {$a->designation->name_eng}) aaj {$dis} theke offer peyesen. Please type (ans YES ) and send korun 26969 number e {$sms_end_date} tarikh er moddhey . Otherwise  offer ti cancel hoie jabe-DC {$dc}";

                    $phone = '88' . trim($a->mobile_no_self);
                    $response = $this->sendSMS($phone, $body);
                    $r = Parser::xml($response);
                    Log::info("SERVER RESPONSE : " . json_encode($r));
                    $offer->sms_try += 1;
                    if (isset($r['PARAMETER']) && strcasecmp($r['PARAMETER'], 'OK') == 0 && isset($r['SMSINFO']['MSISDN']) && strcasecmp($r['SMSINFO']['MSISDN'], '88' . trim($a->mobile_no_self)) == 0) {
                        $offer->sms_status = 'Send';
                        $offer->save();
                    } else {
                        $offer->sms_status = 'Failed';
                        $offer->save();
                    }
                    if ($count == $maximum_offer_limit-1) {
                        $this->sendSMS($phone, "Et apnaer $maximum_offer_limit no offer. Ei offer YES na korle apni er offer paben na. Sotorko houn");
                    }
                    DB::connection('hrm')->commit();
                } catch (\Exception $e) {
                    Log::info('OFFER SEND ERROR: ' . $e->getTraceAsString());
                    DB::connection('hrm')->rollback();
                }
            }

        })->everyMinute()->name("send_offer_sms_2")->withoutOverlapping();
        $schedule->call(function () {
            Log::info("called : send_failed_offer");
            $offered_ansar = OfferSMS::with(['ansar', 'district'])->where('sms_status', 'Failed')->take(10)->get();
            foreach ($offered_ansar as $offer) {
                DB::connection('hrm')->beginTransaction();
                try {

                    $a = $offer->ansar;
                    $dis = $offer->district->unit_name_eng;
                    $dc = strtoupper($dis);
                    $sms_end_date = Carbon::parse($offer->sms_end_datetime)->format('d-m-Y h:i:s A');
                    $body = "Apni (ID:{$offer->ansar_id}, {$a->designation->name_eng}) aaj {$dis} theke offer peyesen. Please type (ans YES ) and send korun 6969 number e {$sms_end_date} tarikh er moddhey . Otherwise  offer ti cancel hoie jabe-DC {$dc}";
                    $phone = '88' . trim($a->mobile_no_self);
                    $response = $this->sendSMS($phone, $body);
                    $r = Parser::xml($response);
                    Log::info("SERVER RESPONSE : " . json_encode($r));
                    $offer->sms_try += 1;
                    if (isset($r['PARAMETER']) && strcasecmp($r['PARAMETER'], 'OK') == 0 && isset($r['SMSINFO']['MSISDN']) && strcasecmp($r['SMSINFO']['MSISDN'], '88' . trim($a->mobile_no_self)) == 0) {
                        $offer->sms_status = 'Send';
                        $offer->save();
                    } else {
                        $offer->sms_status = 'Failed';
                        $offer->save();
                    }
                    $count = $offer->getOfferCount();
                    $offer_limit = +GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT);
                    if ($count == $offer_limit-1) {
                        $this->sendSMS($phone, "Et apnaer $offer_limit no offer. Ei offer YES na korle apni ar offer paben na. Sotorko houn");
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
            $offered_cancel = OfferCancel::where('sms_status', 0)->take(10)->get();
            foreach ($offered_cancel as $offer) {
                $a = $offer->ansar;
                $body = 'Your offer is cancelled';
                $phone = '88' . trim($a->mobile_no_self);
                $response = $this->sendSMS($phone, $body);
                $r = simplexml_load_string($response);
                Log::info(json_encode($r));
                $offer->sms_status = 1;
                $offer->save();
            }

        })->everyMinute()->name("offer_cancel")->withoutOverlapping();
        $schedule->call(function () {
            Log::info("REVERT OFFER");
            $offeredAnsars = OfferSMS::where('sms_end_datetime', '<=', Carbon::now())->get();
            foreach ($offeredAnsars as $ansar) {
                Log::info("CALLED START: OFFER NO REPLY" . $ansar->ansar_id);
                DB::beginTransaction();
                try {
                    $count = $ansar->getOfferCount();
                    $maximum_offer_limit = (int)GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT)-1;
                    if ($count >= $maximum_offer_limit) {
                        $ansar->deleteCount();
                        $ansar->deleteOfferStatus();
                        $ansar->blockAnsarOffer();
                        $ansar->saveLog('No Reply');
                        $ansar->status()->update([
                            'offer_sms_status' => 0,
                            'offer_block_status' => 1,
                        ]);
                        $ansar->delete();
                    } else {
                        $ansar->saveCount();

                        switch ($ansar->come_from) {
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
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::info("ERROR: " . $e->getMessage());
                }
            }
        })->everyMinute()->name("revert_offer_2")->withoutOverlapping();
        $schedule->call(function () {

            $offeredAnsars = SmsReceiveInfoModel::all();
            $now = Carbon::now();
            foreach ($offeredAnsars as $ansar) {
                if ($now->diffInDays(Carbon::parse($ansar->sms_received_datetime)) >= 7) {
                    Log::info("CALLED START: OFFER ACCEPTED" . $ansar->ansar_id);
                    DB::beginTransaction();
                    try {
                        $count = $ansar->getOfferCount();
                        $maximum_offer_limit = (int)GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT)-1;
                        if ($count >= $maximum_offer_limit) {
                            $ansar->deleteCount();
                            $ansar->deleteOfferStatus();
                            $ansar->blockAnsarOffer();
                            $ansar->saveLog();
                            $ansar->status()->update([
                                'offer_sms_status' => 0,
                                'offer_block_status' => 1,
                            ]);
                        } else {
                            $ansar->saveCount();
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
                        }
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
            $rest_ansars = RestInfoModel::whereDate('active_date', '<=', Carbon::today()->toDateString())->whereIn('disembodiment_reason_id', [1, 2, 8])->get();
            Log::info("REST to PANEl : CALLED");

            foreach ($rest_ansars as $ansar) {

                if (!in_array(AnsarStatusInfo::REST_STATUS, $ansar->status->getStatus()) || in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->status->getStatus()) || in_array(AnsarStatusInfo::BLACK_STATUS, $ansar->status->getStatus())) continue;
                DB::beginTransaction();
                try {
                    $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->orderBy('id', 'desc')->first();
                    PanelModel::create([
                        'ansar_id' => $ansar->ansar_id,
                        'come_from' => 'Rest',
                        'panel_date' => Carbon::today(),
                        'memorandum_id' => isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                        'ansar_merit_list' => isset($panel_log->merit_list) ? $panel_log->merit_list : 'N\A',
                        'action_user_id' => '0',
                    ]);
                    $ansar->status->update([
                        'pannel_status' => 1,
                        'rest_status' => 0,
                    ]);
                    $ansar->saveLog('Panel');
                    $ansar->delete();
                    DB::commit();
                    Log::info("REST to PANEl :" . $ansar->ansar_id);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::info("REST to PANEl FAILED:" . $ansar->ansar_id);
                }
            }
        })->twiceDaily(0, 12)->name('rest_to_panel')->withoutOverlapping();
        $schedule->call(function () {
            $rest_ansars = RestInfoModel::whereRaw('FLOOR(DATEDIFF(rest_date,NOW())/365)>=1')->where('disembodiment_reason_id', 5)->get();
            Log::info("REST to PANEl DICIPLINARY : CALLED");

            foreach ($rest_ansars as $ansar) {

                if (!in_array(AnsarStatusInfo::REST_STATUS, $ansar->status->getStatus()) || in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->status->getStatus()) || in_array(AnsarStatusInfo::BLACK_STATUS, $ansar->status->getStatus())) continue;
                DB::beginTransaction();
                try {
                    $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->orderBy('id', 'desc')->first();
                    PanelModel::create([
                        'ansar_id' => $ansar->ansar_id,
                        'come_from' => 'Rest',
                        'panel_date' => Carbon::today(),
                        'memorandum_id' => isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                        'ansar_merit_list' => isset($panel_log->merit_list) ? $panel_log->merit_list : 'N\A',
                        'action_user_id' => '0',
                    ]);
                    $ansar->status->update([
                        'pannel_status' => 1,
                        'rest_status' => 0,
                    ]);
                    $ansar->saveLog('Panel');
                    $ansar->delete();
                    DB::commit();
                    Log::info("REST to PANEl :" . $ansar->ansar_id);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::info("REST to PANEl FAILED:" . $ansar->ansar_id);
                }
            }
        })->twiceDaily(0, 12)->name('rest_to_panel_disciplaney_action')->withoutOverlapping();
        $schedule->call(function () {

        })->twiceDaily(0, 12)->name("ansar_retirement")->withoutOverlapping();
        $schedule->call(function () {
            Log::info("called : disable circular");
            DB::connection('recruitment')->beginTransaction();
            try {
                $circulars = JobCircular::where('status', 'active')->where('end_date', '<=', Carbon::now()->format('Y-m-d'))->get();
                foreach ($circulars as $circular) {
                    $circular->status = 'inactive';
                    $circular->payment_status = 'off';
                    $circular->save();
                    DB::connection('recruitment')->commit();
                }
            } catch (\Exception $e) {
                DB::connection('recruitment')->rollback();
            }

        })->dailyAt("23:50")->name("disable_circular")->withoutOverlapping();


//        $schedule->call(function () {
//            Log::info("called : send_sms_to_accepted_applicant");
//            $messID = uniqid('SB_');
//            $messageID = $messID;
//            $apiUser = 'join_ans_vdp';
//            $apiPass = 'shurjoSM123';
//            $applicants = JobAcceptedApplicant::with('applicant')->whereHas('applicant', function ($q) {
//                $q->where('status', 'accepted');
//            })->where('message_status', 'pending')->where('sms_status', 'on')->limit(10)->get();
//            foreach ($applicants as $a) {
//                if ($a->applicant) {
//                    $sms_data = http_build_query(
//                        array(
//                            'API_USER' => $apiUser,
//                            'API_PASSWORD' => $apiPass,
//                            'MOBILE' => $a->applicant->mobile_no_self,
//                            'MESSAGE' => $a->message,
//                            'MESSAGE_ID' => $messageID
//                        )
//                    );
//
//                    $ch = curl_init();
//                    $url = "https://shurjobarta.shurjorajjo.com.bd/barta_api/api.php";
//                    curl_setopt($ch, CURLOPT_URL, $url);
//                    curl_setopt($ch, CURLOPT_POST, 1);                //0 for a get request
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, $sms_data);
//                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
//                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//                    $response = curl_exec($ch);
//                    curl_close($ch);
//                    var_dump($response);
//                    $a->message_status = 'send';
//                    $a->save();
//                }
//            }
//
//        })->everyMinute()->name("send_sms_to_selected_applicant")->withoutOverlapping();


        $schedule->call(function () {
            Log::info("called : generate attendance");
            //            DB::enableQueryLog();
            $kpis = KpiGeneralModel::with(['embodiment' => function ($q) {
                $q->select('ansar_id', 'kpi_id', 'emboded_status');
                $q->whereHas('ansar.status', function ($q) {
                    $q->where('embodied_status', 1);
                    $q->where('freezing_status', 0);
                    $q->where('block_list_status', 0);
                });
            }])->where('status_of_kpi', 1)
                ->select('id', 'kpi_name');
//            return DB::getQueryLog();
            $now = Carbon::now();
            $day = $now->format('d');
            $month = $now->format('m');
            $year = $now->format('Y');
            $kpis->chunk(1000, function ($datas) use ($day, $month, $year) {

                $inserts = [];
                $bindings = [];
                foreach ($datas as $data) {
                    Log::info('KPI_ID : ' . $data->id);
                    foreach ($data->embodiment as $em) {
                        $qs = [
                            '?',
                            '?',
                            '?',
                            '?',
                            '?',
                        ];
                        $bindings[] = $data->id;
                        $bindings[] = $em->ansar_id;
                        $bindings[] = $day;
                        $bindings[] = $month;
                        $bindings[] = $year;
                        $inserts[] = '(' . implode(",", $qs) . ')';
                        $p[] = $em->emboded_status;
                    }
                }
                $query = "INSERT IGNORE INTO tbl_attendance(kpi_id,ansar_id,day,month,year) VALUES " . implode(",", $inserts);
                DB::connection('sd')->beginTransaction();
                try {
                    DB::connection('sd')->insert($query, $bindings);
                    DB::connection('sd')->commit();
                } catch (\Exception $e) {

                    DB::connection('sd')->rollback();
                    return $e->getMessage();
                }
            });


        })->dailyAt("00:05")->name("generate_attendance")->withoutOverlapping();
//        $schedule->call(function () {
//            Log::info("called : unblock panel locked");
//            PanelModel::where('locked', 1)->update(['locked' => 0]);
//
//
//        })->everyThirtyMinutes()->name("panel_unlock")->withoutOverlapping();
//        $schedule->call(function () {
//            Log::info("called : offer block to panel");
//            DB::connection('hrm')->beginTransaction();
//            try {
//                $currentDate = Carbon::now()->format('Y-m-d');
//                $unit = GlobalParameterFacades::getUnit(GlobalParameter::OFFER_BLOCK_PERIOD);
//                $value = GlobalParameterFacades::getValue(GlobalParameter::OFFER_BLOCK_PERIOD);
//                switch (strtolower($unit)) {
//                    case 'year':
//                        $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(YEAR,blocked_date,'$currentDate')>=$value")->take(1000)->get();
//                        break;
//                    case 'month':
//                        $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(MONTH,blocked_date,'$currentDate')>=$value")->take(1000)->get();
//                        break;
//                    case 'day':
//                        $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(DAY,blocked_date,'$currentDate')>=$value")->take(1000)->get();
//                        break;
//                    default:
//                        dd('Invalid Parameter');
//                }
//
//                foreach ($blocked_ansars as $blocked_ansar) {
//                    $now = Carbon::now();
//                    $panel_log = PanelInfoLogModel::where('ansar_id', $blocked_ansar->ansar_id)->orderBy('panel_date', 'desc')->first();
//                    PanelModel::create([
//                        'memorandum_id' => $panel_log && isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
//                        'panel_date' => $now,
//                        'come_from' => 'Offer Cancel',
//                        'ansar_merit_list' => 1,
//                        'ansar_id' => $blocked_ansar->ansar_id,
//                    ]);
//                    AnsarStatusInfo::where('ansar_id', $blocked_ansar->ansar_id)->update(['offer_block_status' => 0, 'pannel_status' => 1]);
//                    $blocked_ansar->status = "unblocked";
//                    $blocked_ansar->unblocked_date = Carbon::now()->format('Y-m-d');
//                    $blocked_ansar->save();
//                    $blocked_ansar->delete();
//                }
//                DB::commit();
//            } catch (\Exception $exception) {
//                DB::rollback();
//                return ['status' => false, 'message' => $exception->getMessage()];
//            }
//            return ['status' => true, 'message' => 'Sending to panel complete'];
//
//
//        })->everyThirtyMinutes()->name("offer_block_to_panel_6_month")->withoutOverlapping();
        $schedule->call(function () {
            Log::info("called : Ansar Block For Age");
            $ansars = PanelModel::whereHas('ansarInfo.status',function ($q){
                $q->where('block_list_status',0);
                $q->where('pannel_status',1);
                $q->where('black_list_status',0);
            })->with(['ansarInfo'=>function($q){
                $q->select('ansar_id','data_of_birth','designation_id');
                $q->with(['designation','status']);
            }])->take(2000)->get();
//            return $ansars;
            $a = [];
            DB::connection('hrm')->beginTransaction();
            try {
                $now = \Carbon\Carbon::now();
                foreach ($ansars as $ansar) {
                    $info = $ansar->ansarInfo;
                    $dob = $info->data_of_birth;

                    $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                    $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                    $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;
                    if ($info->designation->code == "ANSAR" && ($age->y >= $ansarRe&&($age->m>0||$age->d>0))) {
                        $info->status->update([
                            'pannel_status' => 0,
                            'retierment_status' => 1
                        ]);
                        $info->retireHistory()->create([
                            'retire_from'=>'panel',
                            'retire_date'=>$now->format('Y-m-d')
                        ]);
                        $ansar->saveLog('Retire', null, 'over aged');
                        $ansar->delete();
                    } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && ($age->y >= $pcApcRe&&($age->m>0||$age->d>0))) {
                        $info->status->update([
                            'pannel_status' => 0,
                            'retierment_status' => 1
                        ]);
                        $info->retireHistory()->create([
                            'retire_from'=>'panel',
                            'retire_date'=>$now->format('Y-m-d')
                        ]);
                        $ansar->saveLog('Retire', null, 'over aged');
                        $ansar->delete();
                    }

                    //array_push($a, ['ansar_id' => $ansar->ansar_id, 'age' => $age, 'status' => $info->status->getStatus()]);

                }
                DB::connection('hrm')->commit();
            }catch(\Exception $e){
                Log::info("ansar_block_for_age:".$e->getMessage());
                DB::connection('hrm')->rollback();
            }


        })->everyMinute()->name("ansar_block_for_age")->withoutOverlapping();

    }
}
