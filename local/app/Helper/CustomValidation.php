<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 8/2/2016
 * Time: 5:34 AM
 */

namespace App\Helper;


use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use Symfony\Component\Translation\TranslatorInterface;

class CustomValidation extends Validator
{
    private $custom_messages = [
        'is_eligible' => ':attribute not eligible for this action',
        'is_array' => ':attribute not an array',
        'array_type' => 'Array type does not match of this :attribute',
        'array_length_max' => ':attribute length is overflow',
        'array_length_min' => ':attribute length is underflow',
        'array_length_same' => ':attribute length does not match with :other',
    ];

    public function __construct(TranslatorInterface $translator, array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        parent::__construct($translator, $data, $rules, $messages, $customAttributes);
        $this->setCustomMessage();
    }

    public function setCustomMessage()
    {
        $this->setCustomMessages($this->custom_messages);

    }

    public function validateIsEligible($attribute, $value, $parameters)
    {
        $ansar_id = array_get($this->getData(), $parameters[0]);
        $kpi_id = array_get($this->getData(), $parameters[1]);
        Log::info($ansar_id . " " . $kpi_id);
        if (!is_int($ansar_id) || !is_int($kpi_id)) {
            return false;
        }
        $ansar_rank = PersonalInfo::where('tbl_ansar_parsonal_info.ansar_id', $ansar_id)->select('designation_id')->first();
        if ($ansar_rank->designation_id == 1) {
            $kpi_ansar_given = KpiDetailsModel::where('kpi_id', $kpi_id)->select('no_of_ansar')->first();
            $kpi_ansar_appointed = EmbodimentModel::join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_embodiment.kpi_id', '=', $kpi_id)->where('tbl_ansar_parsonal_info.designation_id', '=', 1)->count('tbl_ansar_parsonal_info.ansar_id');
            if ($kpi_ansar_given->no_of_ansar > $kpi_ansar_appointed) {
                return true;
            } else {
                return false;
            }
        } elseif ($ansar_rank->designation_id == 2) {
            $kpi_ansar_given = KpiDetailsModel::where('kpi_id', $kpi_id)->select('no_of_apc')->first();
            $kpi_ansar_appointed = EmbodimentModel::join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_embodiment.kpi_id', '=', $kpi_id)->where('tbl_ansar_parsonal_info.designation_id', '=', 2)->count('tbl_ansar_parsonal_info.ansar_id');
            if ($kpi_ansar_given->no_of_apc > $kpi_ansar_appointed) {
                return true;
            } else {
                return false;
            }
        } elseif ($ansar_rank->designation_id == 3) {
            $kpi_ansar_given = KpiDetailsModel::where('kpi_id', $kpi_id)->select('no_of_pc')->first();
            $kpi_ansar_appointed = EmbodimentModel::join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_embodiment.kpi_id', '=', $kpi_id)->where('tbl_ansar_parsonal_info.designation_id', '=', 3)->count('tbl_ansar_parsonal_info.ansar_id');
            if ($kpi_ansar_given->no_of_pc > $kpi_ansar_appointed) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function validateIsArray($attribute, $value, $parameters)
    {
        return is_array($value);
    }

    public function validateArrayType($attribute, $value, $parameters)
    {
        $type = $parameters[0];
        Log::info($type);
        //return true;
        switch ($type) {
            case 'int':
                foreach ($value as $v) {
                    if (!preg_match('/^[0-9]+$/',$v)) return false;
                }
                break;
        }

        return true;
    }

    public function validateArrayLengthMax($attribute, $value, $parameters)
    {
        $length = count($value);
        $max = array_get($this->getData(), $parameters[0]);
        Log::info($max);
        return $length <= $max;
    }

    public function validateArrayLengthMin($attribute, $value, $parameters)
    {
        $length = count($value);
        $min = intval($parameters[0]);
        Log::info($min);
        return $length >= $min;
    }

    public function validateArrayLengthSame($attribute, $value, $parameters)
    {
        $length = count($value);
        $same = count(array_get($this->getData(), $parameters[0]));
        Log::info($same);
        return $length == $same;
    }

}