<?php

namespace App\modules\recruitment\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobApplicantTrainingDate extends Model
{
    //
    protected $table = 'job_applicant_training_date';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function circular()
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }

    public static function rules($id = '')
    {
        if ($id) {
            return [
                'job_circular_id' => 'required|unique:recruitment.job_applicant_training_date,job_circular_id,' . $id,
                'start_date' => 'required',
                'end_date' => 'required',
            ];
        } else {
            return [
                'job_circular_id' => 'required|unique:recruitment.job_applicant_training_date',
                'start_date' => 'required',
                'end_date' => 'required',
            ];
        }
    }

    public function setStartDateattribute($value)
    {
        $this->attributes['start_date'] = Carbon::parse($value)->format('Y-m-d');
    }
    public function setEndDateattribute($value)
    {
        $this->attributes['end_date'] = Carbon::parse($value)->format('Y-m-d');
    }
    public function getStartDateattribute($value)
    {
        return Carbon::parse($value)->format('d-M-Y');
    }
    public function getEndDateattribute($value)
    {
        return Carbon::parse($value)->format('d-M-Y');
    }
}
