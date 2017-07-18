<?php

namespace App\Helper;

use Carbon\Carbon;

class GlobalParameter
{
    const RETIREMENT_AGE_ANSAR = 'retirement_age_ansar';
    const RETIREMENT_AGE_PC_APC = 'retirement_age_pc_apc';
    const EMBODIMENT_PERIOD = 'embodiment_period';
    const REST_PERIOD = 'rest_period';
    const ALLOCATED_LEAVE = 'allocated_leave';
    const LAST_ANSAR_ID = "last_ansar_id";
    const OFFER_QUOTA_DAY = "offer_quota_day";
    private $globalParameter;

    /**
     * GlobalParameter constructor.
     */
    public function __construct()
    {
        $this->globalParameter = \App\modules\HRM\Models\GlobalParameter::all();
    }

    public function getServiceEndedDate($joining_date)
    {
        $unit = $this->getUnit($this::EMBODIMENT_PERIOD);
        $value = $this->getValue($this::EMBODIMENT_PERIOD);
        if (strcasecmp($unit, "Year") == 0) {
            $service_ending_period = $value;
            $service_ended_date = Carbon::parse($joining_date)->addYear($service_ending_period)->subDay(1);
        } elseif (strcasecmp($unit, "Month") == 0) {
            $service_ending_period = $value;
            $service_ended_date = Carbon::parse($joining_date)->addMonth($service_ending_period)->subDay(1);
        } elseif (strcasecmp($unit, "Day") == 0) {
            $service_ending_period = $value;
            $service_ended_date = Carbon::parse($joining_date)->addDay($service_ending_period)->subDay(1);
        }
        return $service_ended_date;
    }

    public function getUnit($type)
    {
        switch ($type) {
            case Self::RETIREMENT_AGE_ANSAR:
                return $this->globalParameter->where('param_name', 'retirement_age_ansar')->first()->param_unit;
            case Self::RETIREMENT_AGE_PC_APC:
                return $this->globalParameter->where('param_name', 'retirement_age_pc_apc')->first()->param_unit;
            case Self::EMBODIMENT_PERIOD:
                return $this->globalParameter->where('param_name', 'embodiment_period')->first()->param_unit;
            case Self::REST_PERIOD:
                return $this->globalParameter->where('param_name', 'rest_period')->first()->param_unit;
            case Self::ALLOCATED_LEAVE:
                return $this->globalParameter->where('param_name', 'allocated_leave')->first()->param_unit;
            case Self::OFFER_QUOTA_DAY:
                return $this->globalParameter->where('param_name', 'offer_quota_day')->first()->param_unit;

        }
    }

    public function getValue($type)
    {
        switch ($type) {
            case Self::RETIREMENT_AGE_ANSAR:
                return $this->globalParameter->where('param_name', 'retirement_age_ansar')->first()->param_value;
            case Self::RETIREMENT_AGE_PC_APC:
                return $this->globalParameter->where('param_name', 'retirement_age_pc_apc')->first()->param_value;
            case Self::EMBODIMENT_PERIOD:
                return $this->globalParameter->where('param_name', 'embodiment_period')->first()->param_value;
            case Self::REST_PERIOD:
                return $this->globalParameter->where('param_name', 'rest_period')->first()->param_value;
            case Self::ALLOCATED_LEAVE:
                return $this->globalParameter->where('param_name', 'allocated_leave')->first()->param_value;
            case Self::LAST_ANSAR_ID:
                return $this->globalParameter->where('param_name', 'last_ansar_id')->first()->param_value;
            case Self::OFFER_QUOTA_DAY:
                return $this->globalParameter->where('param_name', 'offer_quota_day')->first()->param_value;

        }
    }

    public function getActiveDate($rest_date)
    {
        $unit = $this->getUnit($this::REST_PERIOD);
        $value = $this->getValue($this::REST_PERIOD);
        if (strcasecmp($unit, "Year") == 0) {
            $active_date = Carbon::parse($rest_date)->addYear($value);
        } elseif (strcasecmp($unit, "Month") == 0) {
            ;
            $active_date = Carbon::parse($rest_date)->addMonth($value);
        } elseif (strcasecmp($unit, "Day") == 0) {
            $active_date = Carbon::parse($rest_date)->addDay($value);
        }
        return $active_date;
    }

    public function generateSmartCard($uid, $aid)
    {
        $unit_code = str_pad($uid . '', 3, '0', STR_PAD_LEFT);
        $ansar_id = str_pad($aid . '', 6, '0', STR_PAD_LEFT);
        return $unit_code . $ansar_id;
    }

}