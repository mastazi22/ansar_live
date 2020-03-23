<?php

namespace App\Console\Commands;

use App\Helper\Facades\GlobalParameterFacades;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class RedistributeGlobalPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redistribute:global';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!DB::connection('hrm')->getDatabaseName()) {
            Log::info("SERVER RECONNECTING....");
            DB::reconnect('hrm');
        }
        Log::info("CONNECTION DATABASE : " . DB::connection('hrm')->getDatabaseName());
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
                ->where('tbl_panel_info.locked', 1)
//                ->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^(/+88)?01[0-9]{9}$"')
                ->select('tbl_panel_info.ansar_id', 'panel_date', 'tbl_panel_info.come_from', 'tbl_panel_info.id', 'locked', 'sex', 'division_id', 'tbl_designations.code',
                    DB::raw('REPLACE(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(offer_type,\',\',LENGTH(offer_type)-LENGTH(REPLACE(offer_type,\',\',\'\'))+1),\',\',-1),"DG","GB"),"CG","GB") as last_offer_region'), 'offer_type')
                ->get();

            $values = collect($data)->groupBy(function ($item) {
                return $item->code . "-" . $item->sex;
            })->toArray();


            foreach ($values as $value) {
                $value = collect($value)->sort(function ($a, $b) {
                    $id1 = +isset($a->id) ? $a->id : 0;
                    $id2 = +isset($b->id) ? $b->id : 0;
                    $d1 = isset($a->panel_date) ? Carbon::parse($a->panel_date) : Carbon::now();
                    $d2 = isset($b->panel_date) ? Carbon::parse($b->panel_date) : Carbon::now();
                    if ($d1->gt($d2)) {
                        return 1;
                    } else if ($d1->eq($d2) && $id1 > $id2) {
                        return 1;
                    } else {
                        return -1;
                    }
                })->values()->toArray();
                $locked_ansar_count = 0;
                $query = "UPDATE tbl_panel_info SET panel_date = (CASE ansar_id ";

                foreach ($value as $p) {
                    $p = (array)$p;
                    if ($p['locked']) {
                        if (strcasecmp($p['last_offer_region'], "GB") == 0 || strcasecmp($p['last_offer_region'], "DG") == 0 || strcasecmp($p['last_offer_region'], "CG") == 0 || $p['last_offer_region'] == "") {
                            $panel_date = Carbon::now()->format('Y-m-d H:i:s');
                            $query .= "WHEN " . $p['ansar_id'] . " THEN '$panel_date' ";
                            $current_ansar = DB::table('tbl_panel_info')->where('ansar_id', $p['ansar_id'])->first();
                            $locked_ansar_count++;

                            if ($current_ansar) {
                                Log::info('ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current panel date:' . $current_ansar->panel_date . ' future panel date:' . $panel_date);
                            } else {
                                Log::info('ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current panel date:Not Found future panel date:' . $panel_date);
                            }
                        }
                    }
                }
                $query .= "ELSE panel_date END) WHERE ansar_id IN (" . implode(",", array_column($value, 'ansar_id')) . ")";
                if ($locked_ansar_count > 0) {
                    DB::statement($query);
                }
//                        $i++;
            }


            DB::connection('hrm')->commit();
            echo "done";
        } catch (\Exception $e) {
            echo $e;
            Log::info("global panel rearr:" . $e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }
}
