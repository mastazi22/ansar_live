<?php

namespace App\Jobs;

use App\Helper\Facades\GlobalParameterFacades;
use App\Jobs\Job;
use App\modules\HRM\Models\PanelModel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockForAge extends Job implements ShouldQueue
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
    public function __construct($skip)
    {
        $this->skip = $skip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ansars = PanelModel::whereHas('ansarInfo.status',function ($q){
            $q->where('block_list_status',0);
            $q->where('pannel_status',1);
            $q->where('black_list_status',0);
        })->with(['ansarInfo'=>function($q){
            $q->select('ansar_id','data_of_birth','designation_id');
            $q->with(['designation','status']);
        }])->skip($this->skip)->take(500)->get();
        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansarInfo;
                $dob = $info->data_of_birth;

                $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;
//                ////Log::info("called : Ansar Block For Age-".$ansar->ansar_id."Age:".$age->y."year ".$age->m."month ".$age->d." days");
                if ($info->designation->code == "ANSAR" && (($age->y >= $ansarRe&&($age->m>0||$age->d>0))||$age->y > $ansarRe)) {
                    $data = (array)$ansar;
                    unset($data['id']);
                    unset($data['updated_at']);
                    unset($data['created_at']);
                    unset($data['go_panel_position']);
                    unset($data['re_panel_position']);
                    $data['come_from']='After Retier';
                    $info->status->update([
                        'pannel_status' => 0,
                        'retierment_status' => 1
                    ]);
                    $info->retireHistory()->create([
                        'retire_from'=>'panel',
                        'retire_date'=>$now->format('Y-m-d'),
                        'data'=>json_encode($data)
                    ]);
                    $ansar->saveLog('Retire', null, 'over aged');
                    $ansar->delete();
                } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && (($age->y >= $pcApcRe&&($age->m>0||$age->d>0))||$age->y > $pcApcRe)) {
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
            }
            DB::connection('hrm')->commit();
        }catch(\Exception $e){
            ////Log::info("ansar_block_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }
}
