<?php

namespace App\Console\Commands;

use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;
use App\modules\HRM\Models\PanelModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveUnverified extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:unverified';

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
//        get all unverified ansar and delete them.
        $ansar_panels = PanelModel::join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_ansar_parsonal_info.verified', 0)
            ->orWhere('tbl_ansar_status_info.block_list_status', 1)
            ->orWhere('tbl_ansar_status_info.black_list_status', 1)
            ->orWhere('tbl_ansar_status_info.free_status', 1)
            ->orWhere('tbl_ansar_status_info.embodied_status', 1)
            ->orWhere('tbl_ansar_status_info.offer_block_status', 1)
            ->orWhere('tbl_ansar_status_info.freezing_status', 1)
            ->orWhere('tbl_ansar_status_info.early_retierment_status', 1)
            ->orWhere('tbl_ansar_status_info.rest_status', 1)
            ->orWhere('tbl_ansar_status_info.expired_status', 1)
            ->select('tbl_panel_info.go_panel_position','tbl_panel_info.re_panel_position', 'tbl_ansar_status_info.*')
            ->get();
        if ($ansar_panels->count() > 0) {

            foreach ($ansar_panels as $panel) {
                Log::info('DELETE_PANEL ansar id:' . $panel->ansar_id . ' verified-' . $panel->verified
                    . ' black_status-'.$panel->black_list_status.' block_status-'.$panel->block_list_status
                    . ' free_status-'.$panel->free_status.' embodied_status-'.$panel->embodied_status
                    . ' offer_block_status-'.$panel->offer_block_status.' freezing_status-'.$panel->freezing_status
                    . ' early_retirement_status-'.$panel->early_retierment_status.' rest_status-'.$panel->rest_status
                    . ' expired_status-'.$panel->expired_status
                    .', current g panel position:' . $panel->go_panel_position . ' current re panel position:' . $panel->re_panel_position);
                PanelModel::where('ansar_id', $panel->ansar_id)->delete();
            }
        }
        echo $ansar_panels->count();
        dispatch(new RearrangePanelPositionGlobal());
        dispatch(new RearrangePanelPositionLocal());
    }
}
