<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\modules\HRM\Models\PanelModel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RearrangePanelPositionLocal extends Job implements ShouldQueue
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
            $data = \App\modules\HRM\Models\PanelModel::with(['ansarInfo'=>function($q){
                $q->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^[0-9]{11}$"');
                $q->select('ansar_id','sex','designation_id','division_id');
                $q->with('designation');
            }])->whereHas('ansarInfo.status',function($q){
                $q->where('pannel_status',1);
                $q->where('block_list_status',0);
                $q->where('black_list_status',0);
            })->select('ansar_id','re_panel_date','id')->orderBy('re_panel_date','asc')->orderBy('id','asc')->get();
//                return $ansars;
            $ansars =  collect($data)->groupBy('ansarInfo.division_id',true)->toArray();
            $globalPosition = [];
            foreach ($ansars as $k=>$ansar){
                $values = collect(array_values($ansar))->groupBy('ansar_info.designation.code',true)->toArray();
                if(!isset($globalPosition[$k])){
                    $globalPosition[$k] = [];
                }
                foreach ($values as $key=>$v){
                    if(!isset($globalPosition[$k][$key])){
                        $globalPosition[$k][$key] = [];
                    }
                    $vvalues = collect(array_values($v))->groupBy("ansar_info.sex",true)->toArray();
                    foreach ($vvalues as $kk=>$vv){
                        if(!isset($globalPosition[$k][$key][$kk])){
                            $globalPosition[$k][$key][$kk] = [];
                        }
                        $value = array_values($vv);
                        $i=1;
                        foreach ($value as $p){
                            $globalPosition[$k][$key][$kk][$p['ansar_id']] = $i++;
//                                $globalPosition[$k][$key][$kk][$i++] =$p['ansar_id'];
                        }
                    }

                }
            }
//                return $globalPosition;
            foreach ($globalPosition as $k=>$v){
                foreach ($v as $k1=>$v1){
                    foreach ($v1 as $key=>$value){
                        foreach ($value as $key1=>$value1){
                            $p = PanelModel::where('ansar_id',$key1)->first();
                            if($p){
                                $p->re_panel_position = $value1;
                                $p->save();
                            }
                        }
                    }
                }
            }

//                return $globalPosition;
            DB::connection('hrm')->commit();
            echo "done";
        }catch(\Exception $e){
            echo $e;
            Log::info("ansar_block_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }
}
