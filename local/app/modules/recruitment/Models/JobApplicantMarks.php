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
    public function setWrittenAttribute($value)
    {
        $applicant = $this->applicant?$this->applicant:$this->applicant()->where('applicant_id', $this->applicant_id)->first();
        $mark_distribution = $applicant->circular->markDistribution;
        if($mark_distribution){
            $written = $mark_distribution->written;
            $written_convert = $mark_distribution->convert_written_mark;
        } else{
            $written_convert = floatval($value);
            $written = floatval($value);
        }
        $this->attributes['written'] = (floatval($value)*$written_convert)/$written;
    }
    public function getWrittenAttribute($value)
    {
        $applicant = $this->applicant?$this->applicant:$this->applicant()->where('applicant_id', $this->applicant_id)->first();
        $mark_distribution = $applicant->circular->markDistribution;
        if($mark_distribution){
            $written = $mark_distribution->written;
            $written_convert = $mark_distribution->convert_written_mark;
        } else{
            $written_convert = floatval($value);
            $written = floatval($value);
        }
        return round((floatval($value)*$written)/$written_convert,2);
    }
}
