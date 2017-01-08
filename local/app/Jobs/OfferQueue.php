<?php

namespace App\Jobs;

use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PersonalInfo;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfferQueue extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $data;
    private $user;
    private $district_id;
    private $userOffer;

    public function __construct($data, $district_id, $user,$userOffer)
    {
        //$this->data = $request;
        $this->data = $data;
        $this->user = $user;
        $this->district_id = $district_id;
        $this->userOffer = $userOffer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Log::info($this->data);
//        die;
        $district_id = $this->district_id;
        $ansar_ids = $this->data;
        $user = $this->user;
        for ($i = 0; $i < count($ansar_ids); $i++) {
            if(!DB::connection()->getDatabaseName()){
                Log::info("SERVER RECONNECTING....");
                DB::reconnect('hrm');
            }
            DB::beginTransaction();
            try {

                $mos = PersonalInfo::where('ansar_id', $ansar_ids[$i])->first();
                $mos = PersonalInfo::where('ansar_id', $ansar_ids[$i])->first();
                $pa = $mos->panel;
                if(!$pa) throw new \Exception("No Panel Available");
                if (!$mos && !preg_match('/^(\+88)?0[0-9]{10}/', $mos->mobile_no_self)) throw new \Exception("Invalid mobile number :" . $mos->mobile_no_self);
                $offer = new OfferSMS([
                    'sms_send_datetime' => Carbon::now(),
                    'sms_end_datetime' => Carbon::now()->addHours(48),
                    'district_id' => $district_id,
                    'come_from' => 'Panel',
                    'action_user_id' => $user->id
                ]);
                $mos->offer_sms_info()->save($offer);
//                //if (!$s) throw new Exception("An Error Occur While Send Offer. Please Try Again Later");
////                $this->removeFromPanel();
                $pa = $mos->panel;
                $mos->status()->update([
                    'pannel_status' => 0,
                    'offer_sms_status' => 1,
                ]);
                $pa->panelLog()->save(new PanelInfoLogModel([
                    'ansar_id' => $pa->ansar_id,
                    'merit_list' => $pa->ansar_merit_list,
                    'panel_date' => $pa->panel_date,
                    'old_memorandum_id' => !$pa->memorandum_id ? "N\A" : $pa->memorandum_id,
                    'movement_date' => Carbon::today(),
                    'come_from' => $pa->come_from,
                    'move_to' => 'Offer',
                ]));
                $mos->panel()->delete();
                $user->actionLog()->save(new ActionUserLog([
                    'ansar_id' => $ansar_ids[$i],
                    'action_type' => 'SEND OFFER',
                    'from_state' => 'PANEL',
                    'to_state' => 'OFFER'
                ]));
                //array_push($user, ['ansar_id' => $ansar_ids[$i], 'action_type' => 'SEND OFFER', 'from_state' => 'PANEL', 'to_state' => 'OFFER']);
                Log::info("processed :" . $ansar_ids[$i]);
                DB::commit();
            }
            catch (\Exception $e) {
                DB::rollback();
                if($pa){
                    $pa->update([
                        'locked'=>0
                    ]);
                }
                Log::info($e->getMessage());
                //return response(collect(['status' => 'error', 'message' => $e->getMessage()])->toJson(), 400, ['Content-Type' => 'application/json']);
            }
        }

        $this->delete();
        $this->userOffer->delete();

    }
}
