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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use Symfony\Component\Translation\TranslatorInterface;

class CustomValidation extends Validator
{
    private $custom_messages = [
        'is_eligible' => ':attribute not eligible for this action',
        'is_offered_for_embodiment' => ':attribute not eligible for this action',
        'is_array' => ':attribute not an array',
        'array_type' => 'Array type does not match of this :attribute',
        'array_length_max' => ':attribute length is overflow',
        'array_length_min' => ':attribute length is underflow',
        'array_length_same' => ':attribute length does not match with :other',
        'date_validity' => ':attribute date is not valid',
        'offer_date_validate' => ':attribute date is not valid',
        'joining_date_validate' => 'joining date must be within 1 month after reporting date',
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
    public function validateIsOfferedForEmbodiment($attribute, $value, $parameters)
    {
        $ansar_id = array_get($this->getData(), $parameters[0]);
        $kpi_id = array_get($this->getData(), $parameters[1]);
        Log::info($ansar_id);
        if (!is_int($ansar_id) || !is_int($kpi_id)) {
            return false;
        }
        $ansar_from_sms_receive = DB::table('tbl_sms_receive_info')->where('ansar_id', $ansar_id)->select('tbl_sms_receive_info.ansar_id')->first();
        if(!is_null($ansar_from_sms_receive)){
            return true;
        }else{
            return false;
        }
    }
    public function validateIsArray($attribute, $value, $parameters)
    {
        return is_array($value);
    }

    public function validateArrayType($attribute, $value, $parameters)
    {
        $key_length = count($parameters);
        Log::info($parameters);
        if(!is_array($value)) return false;
        if($key_length==1) {
            $type = $parameters[0];
            switch ($type) {
                case 'int':
                    foreach ($value as $v) {
                        if (!preg_match('/^[0-9]+$/', $v)) return false;
                    }
                    break;
            }
        }
        else if($key_length>1){
            $map = [];
            if($key_length%2==1){
                return false;
            }
            for($i = 0;$i<$key_length;$i+=2){
                array_push($map,['key'=>$parameters[$i],'type'=>$parameters[$i+1]]);
            }
            Log::info($map);
            foreach($map as $m){
                switch ($m['type']) {
                    case 'int':
                        foreach ($value as $v) {
                            if (!preg_match('/^[0-9]+$/', $v[$m['key']])) return false;
                        }
                        break;
                }
            }
        }
        else return false;
        return true;
    }

    public function validateArrayLengthMax($attribute, $value, $parameters)
    {
        $length = count($value);
        $max = array_get($this->getData(), $parameters[0]);
        $max = $max?$max:intval($parameters[0]);
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
        $same = array_get($this->getData(), $parameters[0]);
        $same = $same?count($same):intval($parameters[0]);
        Log::info($same);
        return $length == $same;
    }
    public function validateDateValidity($attribute, $value, $parameters)
    {
        if(preg_match('/^[0-9]{2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(dec))\-[0-9]{4}$/',$value)){
            return true;
        }
        return false;
    }
    public function validateOfferDateValidate($attribute, $value, $parameters)
    {
        try {
            if (Carbon::parse($value)->gte(Carbon::now()->subHours(48)->setTime(0, 0, 0)) && !Carbon::parse($value)->gt(Carbon::now())) {
                return true;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }
    public function validateJoiningDateValidate($attribute, $value, $parameters)
    {
        $reporting_date = array_get($this->getData(),$parameters[0]);
        try {
            $diff = Carbon::parse($reporting_date)->diffInMonths(Carbon::parse($value),false);
            Log::info("Different in month :".$diff);
            if ($diff>=0&&$diff<=1&&Carbon::parse($value)->gte(Carbon::parse($reporting_date))) {
                return true;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }
}