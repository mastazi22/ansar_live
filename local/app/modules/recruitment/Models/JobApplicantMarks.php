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
    public function convertedWrittenMark(){
        $applicant = $this->applicant?$this->applicant:$this->applicant()->where('applicant_id', $this->applicant_id)->first();
        $mark_distribution = $applicant->circular->markDistribution;
        if($mark_distribution){
            $written = $mark_distribution->written;
            $written_convert = $mark_distribution->convert_written_mark;
        } else{
            $written_convert = floatval($this->written);
            $written = floatval($this->written);
        }
        return round((floatval($this->written)*$written_convert)/$written,2);
    }
    public function fail(){
        $applicant = $this->applicant?$this->applicant:$this->applicant()->where('applicant_id', $this->applicant_id)->first();
        $mark_distribution = $applicant->circular->markDistribution;
        $written_pass_mark = 0;
        $viva_pass_mark = 0;
        if($mark_distribution){
            $written_pass_mark = (floatval($mark_distribution->convert_written_mark)*30)/100;
            $viva_pass_mark = (floatval($mark_distribution->viva)*30)/100;
        }
        return $this->convertedWrittenMark()<$written_pass_mark||$this->viva<$viva_pass_mark;
    }
}
