<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobCircularMarkDistribution extends Model
{
    //
    protected $table = 'job_circular_mark_distribution';
    protected $guarded = ['id', 'job_circular_id'];
    protected $connection = 'recruitment';

    public function circular()
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }

    public static function rules($id = '')
    {
        if ($id) {
            return [
                'job_circular_id' => 'required|unique:recruitment.job_circular_mark_distribution,job_circular_id,' . $id,
//                'physical' => 'required|numeric',
//                'edu_training' => 'required|numeric',
//                'written' => 'required|numeric',
//                'viva' => 'required|numeric'
                //
                'is_physical_checkbox' => 'sometimes',
                'physical' => 'required_if:is_physical_checkbox,checked|numeric',
                //
                'is_education_and_training_checkbox' => 'sometimes',
                'edu_training' => 'required_if:is_education_and_training_checkbox,checked|numeric',
                //
                'is_written_checkbox' => 'sometimes',
                'written' => 'required_if:is_written_checkbox,checked|numeric',
                //
                'is_viva_checkbox'=>'sometimes',
                'viva' => 'required_if:is_viva_checkbox,checked|numeric'
            ];
        } else {
            return [
                'job_circular_id' => 'required|unique:recruitment.job_circular_mark_distribution',
                //
                'is_physical_checkbox' => 'sometimes',
                'physical' => 'required_if:is_physical_checkbox,checked|numeric',
                //
                'is_education_and_training_checkbox' => 'sometimes',
                'edu_training' => 'required_if:is_education_and_training_checkbox,checked|numeric',
                //
                'is_written_checkbox' => 'sometimes',
                'written' => 'required_if:is_written_checkbox,checked|numeric',
                //
                'is_viva_checkbox'=>'sometimes',
                'viva' => 'required_if:is_viva_checkbox,checked|numeric'
            ];
        }
    }

    public function setConvertWrittenMarkAttribute($value)
    {
        if (!$value) {
            $this->attributes['convert_written_mark'] = $this->written;
        } else {
            $this->attributes['convert_written_mark'] = $value;
        }
    }
}