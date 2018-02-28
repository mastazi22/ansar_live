<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicantMarks extends Model
{
    //
    protected $table = 'job_applicant_marks';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function applicant()
    {
        return $this->belongsTo(JobAppliciant::class, 'applicant_id', 'applicant_id');
    }

    public function setPhysicalAttribute($value)
    {
        $applicant = $this->applicant?$this->applicant:$this->applicant()->where('applicant_id', $this->applicant_id)->first();
        $this->attributes['physical'] = $applicant->physicalPoint();
    }

    public function setEduTrainingAttribute($value)
    {
        $applicant = $this->applicant?$this->applicant:$this->applicant()->where('applicant_id', $this->applicant_id)->first();
        $this->attributes['edu_training'] = $applicant->educationTrainingPoint();
    }
}
