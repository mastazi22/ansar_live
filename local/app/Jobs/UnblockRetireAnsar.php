<?php

namespace App\Jobs;

use App\Helper\Facades\GlobalParameterFacades;
use App\Jobs\Job;
use App\modules\HRM\Models\AnsarRetireHistory;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnblockRetireAnsar extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $skip;

    /**
     * BlockForAge constructor.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ansars = AnsarRetireHistory::all();
        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansar;
                $dob = $info->data_of_birth;

                $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;
//                Log::info("called : Ansar Block For Age-".$ansar->ansar_id."Age:".$age->y."year ".$age->m."month ".$age->d." days");
                if ($info->designation->code == "ANSAR" && (($age->y >= $ansarRe&&($age->m>0||$age->d>0))||$age->y > $ansarRe)) {
                    $pl = PanelInfoLogModel::where('ansar_id',$info->ansar_id)->orderBy('panel_date','desc')->first();
                    $info->panel()->create([
                       'ansar_merit_list'=>$pl->merit_list,
                        'panel_date'=>Carbon::now()->format('Y-m-d'),
                        'memorandum_id'=>$pl->old_memorandum_id,
                        'come_from'=>'After Retier'
                    ]);
                    $info->status->update([
                        'pannel_status' => 1,
                        'retierment_status' => 0
                    ]);
                    $ansar->delete();
                } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && (($age->y >= $pcApcRe&&($age->m>0||$age->d>0))||$age->y > $pcApcRe)) {
                    $info->panel()->create([
                        'ansar_merit_list'=>$pl->merit_list,
                        'panel_date'=>Carbon::now()->format('Y-m-d'),
                        'memorandum_id'=>$pl->old_memorandum_id,
                        'come_from'=>'After Retier'
                    ]);
                    $info->status->update([
                        'pannel_status' => 1,
                        'retierment_status' => 0
                    ]);
                    $ansar->delete();
                }
            }

            DB::connection('hrm')->commit();
        }catch(\Exception $e){
            Log::info("ansar_unblock_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
        dispatch(new RearrangePanelPositionGlobal());
        dispatch(new RearrangePanelPositionLocal());
    }
}
