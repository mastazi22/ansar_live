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
    public function getValue($type){
        switch($type){
            case 'R':
                return $this->constants->where('cons_name','ration_fee')->first();
            case 'DPA':
                return $this->constants->where('cons_name','per_day_salary_pc_and_apc')->first();
            case 'DA':
                return $this->constants->where('cons_name','per_day_salary_ansar')->first();
            case 'CB':
                return $this->constants->where('cons_name','barber_and_cleaner_fee')->first();
            case 'CV':
                return $this->constants->where('cons_name','transportation')->first();
            case 'DV':
                return $this->constants->where('cons_name','medical_fee')->first();
            case 'MV':
                return $this->constants->where('cons_name','margha_fee')->first();
            default:
                return 0;

        }
    }
}