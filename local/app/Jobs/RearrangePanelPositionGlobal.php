<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\modules\HRM\Models\PanelModel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RearrangePanelPositionGlobal extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!DB::connection('hrm')->getDatabaseName()){
            Log::info("SERVER RECONNECTING....");
            DB::reconnect('hrm');
        }
        Log::info("CONNECTION DATABASE : ".DB::connection('hrm')->getDatabaseName());
        DB::connection('hrm')->beginTransaction();
        try {
            $data = $data = \App\modules\HRM\Models\PanelModel::with(['ansarInfo'=>function($q){
                $q->select('ansar_id','sex','designation_id','division_id');
                $q->with('designation');
            }])->whereHas('ansarInfo',function($q){
                $q->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^(/+88)?01[0-9]{9}$"');
                $q->whereHas('status',function($q){
                    $q->where('block_list_status',0);
                    $q->where('black_list_status',0);
                });
            })->select('ansar_id','panel_date','id','locked')->orderBy('panel_date','asc')->orderBy('id','asc')->get();
//                return $ansars;
            $ansars =  collect($data)->groupBy('ansarInfo.designation.code',true)->toArray();
            $globalPosition = [];
            foreach ($ansars as $k=>$ansar){
                $values = collect(array_values($ansar))->groupBy('ansar_info.sex',true)->toArray();
                if(!isset($globalPosition[$k])){
                    $globalPosition[$k] = [];
                }
                foreach ($values as $key=>$v){
                    if(!isset($globalPosition[$k][$key])){
                        $globalPosition[$k][$key] = [];
                    }
                    $value = array_values($v);
                    $i=1;
                    foreach ($value as $p){
                        $offerStatus = OfferSMSStatus::where('ansar_id',$p['ansar_id'])
                            ->select(DB::raw('SUBSTRING_INDEX(SUBSTRING_INDEX(offer_type,\',\',LENGTH(offer_type)-LENGTH(REPLACE(offer_type,\',\',\'\'))+1),\',\',-1) as last_offer_region'))
                            ->first();
                        if((!$offerStatus||strcasecmp($offerStatus->last_offer_region,'GB')||strcasecmp($offerStatus->last_offer_region,'DG')||strcasecmp($offerStatus->last_offer_region,'CG'))&&!$p['locked']){
                            $globalPosition[$k][$key][$p['ansar_id']] = $i;
                        }
                        $i++;
                    }

                }
            }
            foreach ($globalPosition as $k=>$v){
                foreach ($v as $k1=>$v1){
                    foreach ($v1 as $key=>$value){
                        $p = PanelModel::where('ansar_id',$key)->first();
                        if($p){
                            $p->go_panel_position = $value;
                            $p->save();
                        }
                    }
                }
            }

//                return $globalPosition;
            DB::connection('hrm')->commit();
            echo "done";
        }catch(\Exception $e){
            echo $e;
            Log::info("global panel rearr:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }
}
