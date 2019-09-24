<?php

namespace App\Jobs;

use App\Helper\Facades\GlobalParameterFacades;
use App\Jobs\Job;
use App\modules\HRM\Models\OfferSMSStatus;
use App\modules\HRM\Models\PanelModel;
use Carbon\Carbon;
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
            $go_offer_count = +GlobalParameterFacades::getValue('ge_offer_count');
            $data = DB::table('tbl_ansar_parsonal_info')
                ->leftJoin('tbl_offer_status', 'tbl_offer_status.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_ansar_status_info.block_list_status', 0)
                ->where('tbl_ansar_status_info.black_list_status', 0)
                ->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^(/+88)?01[0-9]{9}$"')
                ->select('tbl_panel_info.ansar_id','panel_date','tbl_panel_info.id','locked','sex','division_id','tbl_designations.code',
                    DB::raw('REPLACE(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(offer_type,\',\',LENGTH(offer_type)-LENGTH(REPLACE(offer_type,\',\',\'\'))+1),\',\',-1),"DG","GB"),"CG","GB") as last_offer_region'),'offer_type')
                ->get();
            /*$data = $data = \App\modules\HRM\Models\PanelModel::with(['ansarInfo'=>function($q){
                $q->select('ansar_id','sex','designation_id','division_id');
                $q->with('designation');
            }])->whereHas('ansarInfo',function($q){
                $q->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^(/+88)?01[0-9]{9}$"');
                $q->whereHas('status',function($q){
                    $q->where('block_list_status',0);
                    $q->where('black_list_status',0);
                });
            })->select('ansar_id','panel_date','id','locked')->orderBy('panel_date','asc')->orderBy('id','asc')->get();*/
//                return $ansars;
            $values =  collect($data)->groupBy(function($item){
                return $item->code."-".$item->sex;
            })->toArray();
            $globalPosition = [];

            foreach ($values as $value){
                $value = collect($value)->sort(function($a,$b){
                    $id1 = +isset($a->id)?$a->id:0;
                    $id2 = +isset($b->id)?$b->id:0;
                    $d1 = isset($a->panel_date)?Carbon::parse($a->panel_date):Carbon::now();
                    $d2 = isset($b->panel_date)?Carbon::parse($b->panel_date):Carbon::now();
                    if($d1->gt($d2)){
                        return 1;
                    }else if($d1->eq($d2)&&$id1>$id2){
                        return 1;
                    }else{
                        return -1;
                    }
                })->values()->toArray();
                $i=1;
                $query = "UPDATE tbl_panel_info SET go_panel_position = (CASE ansar_id ";
                foreach ($value as $p){
                    $p = (array)$p;
                    if((!$p['offer_type']||strcasecmp($p['last_offer_region'],'GB')||!$p['locked'])
                        &&(!$p['offer_type']||(substr_count($p['offer_type'],'GB')+substr_count($p['offer_type'],'DG')+substr_count($p['offer_type'],'CG'))<$go_offer_count)){
//                        DB::table('tbl_panel_info')->where('ansar_id',$p['ansar_id'])->update(['go_panel_position'=>$i]);
                        $query .= "WHEN ".$p['ansar_id']." THEN $i ";
                        $i++;
                    }
                }
                $query .= "END) WHERE ansar_id IN (".implode(",",array_column($value,'ansar_id')).")";
                DB::statement($query);

//                        $i++;
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
