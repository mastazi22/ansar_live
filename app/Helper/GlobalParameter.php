<?php

namespace App\Helper;
class GlobalParameter
{
    const RETIREMENT_AGE = 'retirement_age';
    const EMBODIMENT_PERIOD = 'embodiment_period';
    const REST_PERIOD = 'rest_period';
    const ALLOCATED_LEAVE = 'allocated_leave';
    const LAST_ANSAR_ID = "last_ansar_id";
    private $globalParameter;

    /**
     * GlobalParameter constructor.
     */
    public function __construct()
    {
        $this->globalParameter = \App\modules\HRM\Models\GlobalParameter::all();
    }

    public function getValue($type)
    {
        switch($type){
            case Self::RETIREMENT_AGE:
                return $this->globalParameter->where('param_name','retirement_age')->first()->param_value;
            case Self::EMBODIMENT_PERIOD:
                return $this->globalParameter->where('param_name','embodiment_period')->first()->param_value;
            case Self::REST_PERIOD:
                return $this->globalParameter->where('param_name','rest_period')->first()->param_value;
            case Self::ALLOCATED_LEAVE:
                return $this->globalParameter->where('param_name','allocated_leave')->first()->param_value;
            case Self::LAST_ANSAR_ID:
                return $this->globalParameter->where('param_name','last_ansar_id')->first()->param_value;

        }
    }
    public function getUnit($type)
    {
        switch($type){
            case Self::RETIREMENT_AGE:
                return $this->globalParameter->where('param_name','retirement_age')->first()->param_unit;
            case Self::EMBODIMENT_PERIOD:
                return $this->globalParameter->where('param_name','embodiment_period')->first()->param_unit;
            case Self::REST_PERIOD:
                return $this->globalParameter->where('param_name','rest_period')->first()->param_unit;
            case Self::ALLOCATED_LEAVE:
                return $this->globalParameter->where('param_name','allocated_leave')->first()->param_unit;

        }
    }

}