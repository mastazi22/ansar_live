<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 10/26/2016
 * Time: 12:13 PM
 */

namespace App\Helper;


use Illuminate\Support\Facades\DB;

class QueryHelper
{
    const EMBODIED = 1;
    const ALL_ANSARS = 2;
    const UNVERIFIED = 3;
    const PANEL = 4;
    const FREE = 5;
    const OFFER = 6;
    const OFFER_BLOCK = 14;
    const OFFER_RECEIVED = 13;
    const REST = 7;
    const FREEZE = 8;
    const BLOCK = 9;
    const BLACK = 10;
    const OWN_EMBODIED = 11;
    const DIFF_EMBODIED = 12;
    const RETIRE = 15;

    public static function getQuery($type)
    {
        $ansarQuery = '';
        switch ($type) {
            case self::ALL_ANSARS:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id');
                break;
            case self::FREE:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.free_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::PANEL:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->leftJoin('tbl_offer_status', 'tbl_offer_status.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->leftJoin('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->leftJoin('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where(function ($q) {
                        $q->where('tbl_ansar_status_info.pannel_status', 1)
                            ->orWhere('tbl_ansar_status_info.offer_sms_status', 1);
                    })
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->where('tbl_ansar_status_info.black_list_status', 0);
                break;
            case self::OFFER:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_offer_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_offer_info.district_id')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::OFFER_BLOCK:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->leftJoin('tbl_sms_send_log', 'tbl_sms_send_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_offer_blocked_ansar', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_offer_blocked_ansar.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_offer_blocked_ansar.last_offer_unit')
                    ->where('tbl_ansar_status_info.offer_block_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereNull('tbl_offer_blocked_ansar.deleted_at')
                    ->groupBy('tbl_sms_send_log.ansar_id');
                break;
            case self::OFFER_RECEIVED:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_sms_receive_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')
                    ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_receive_info.offered_district')
                    ->where('tbl_ansar_status_info.offer_sms_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::REST:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_rest_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_rest_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.rest_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::FREEZE:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_freezing_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.freezing_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::BLOCK:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_blocklist_info', 'tbl_blocklist_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->where('tbl_ansar_status_info.block_list_status', 1)
                    ->where('tbl_blocklist_info.date_for_unblock', '=', null);
                break;
            case self::BLACK:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_blacklist_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.black_list_status', 1);
                break;
            case self::EMBODIED:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_embodiment', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::OWN_EMBODIED:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::DIFF_EMBODIED:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana as pt', 'tbl_ansar_parsonal_info.thana_id', '=', 'pt.id')
                    ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_units as ku', 'ku.id', '=', 'tbl_kpi_info.unit_id')
                    ->join('tbl_thana as kt', 'tbl_kpi_info.thana_id', '=', 'kt.id')
                    ->where('tbl_ansar_status_info.embodied_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::UNVERIFIED:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])
                    ->where('tbl_ansar_status_info.block_list_status', 0);
                break;
            case self::RETIRE:
                $ansarQuery = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_ansar_retirement_history', 'tbl_ansar_retirement_history.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                    ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_status_info.retierment_status', 1)
                    ->where('tbl_ansar_status_info.block_list_status', 0)
                    ->whereNull('tbl_ansar_retirement_history.deleted_at');
                break;
        }
        return $ansarQuery;
    }

}