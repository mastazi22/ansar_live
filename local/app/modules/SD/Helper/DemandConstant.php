<?php

namespace App\modules\SD\Helper;
/**
 * Created by PhpStorm.
 * User: Arafat
 * Date: 6/13/2016
 * Time: 10:31 AM
 */
class DemandConstant
{

    /**
     * DemandConstant constructor.
     */
    private $constants;

    public function __construct()
    {
        $this->constants = \App\modules\SD\Models\DemandConstant::all();
    }

    public function getValue($type)
    {
        switch ($type) {
            case 'R':
                return $this->constants->where('cons_name', 'ration_fee')->first();
            case 'DPA':
                return $this->constants->where('cons_name', 'per_day_salary_pc_and_apc')->first();
            case 'DA':
                return $this->constants->where('cons_name', 'per_day_salary_ansar')->first();
            case 'CB':
                return $this->constants->where('cons_name', 'barber_and_cleaner_fee')->first();
            case 'CV':
                return $this->constants->where('cons_name', 'transportation')->first();
            case 'DV':
                return $this->constants->where('cons_name', 'medical_fee')->first();
            case 'MV':
                return $this->constants->where('cons_name', 'margha_fee')->first();
            case 'WF':
                return $this->constants->where('cons_name', 'welfare_fee')->first();
            case 'EBPA':
                return $this->constants->where('cons_name', 'eid_bonus_for_pc_and_apc')->first();
            case 'EBA':
                return $this->constants->where('cons_name', 'eid_bonus_for_ansar')->first();
            case 'SA':
                return $this->constants->where('cons_name', 'share_amount')->first();
            case 'DAS':
                return $this->constants->where('cons_name', 'deduct_amount')->first();
            case 'OAS':
                return $this->constants->where('cons_name', 'other_amount')->first();
            case 'DPAS':
                return $this->constants->where('cons_name', 'pc_apc_per_day_salary_for_short_term_kpi')->first();
            case 'DVAS':
                return $this->constants->where('cons_name', 'ansar_vdp_per_day_salary_for_short_term_kpi')->first();
            case 'REGF':
                return $this->constants->where('cons_name', 'regimental_fee')->first();
            case 'REVS':
                return $this->constants->where('cons_name', 'revenue_stamp')->first();
            case 'DGEP':
                return $this->constants->where('cons_name', 'part_of_dg_account_of_extra_amount')->first();
            case 'RCEP':
                return $this->constants->where('cons_name', 'part_of_rc_account_of_extra_amount')->first();
            case 'DCEP':
                return $this->constants->where('cons_name', 'part_of_dc_account_of_extra_amount')->first();

            default:
                return 0;

        }
    }
}