<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    public function getAdditionalMarksAttribute($value){
        if($value)return unserialize($value);
        else return null;
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
    public function showOriginalWrittenMark()
    {
        $applicant = $this->applicant?$this->applicant:$this->applicant()->where('applicant_id', $this->applicant_id)->first();
        $mark_distribution = $applicant->circular->markDistribution;
        return round($this->written,2)." out of ".$mark_distribution->written;
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
    public function failedApplicants(){
        $q = $this->applicant()->whereHas('circular.markDistribution',function ($q){
            $q->where(DB::raw('(convert_written_mark*written_pass_mark)/100'),'>',DB::raw('(job_applicant_marks.written*convert_written_mark)/job_circular_mark_distribution.written'));
            $q->where(DB::raw('(viva*viva_pass_mark)/100'),'>',DB::raw('job_applicant_marks.viva'));
        });
        return $q;
    }
    public function passedApplicants(){
        $q = $this->applicant()->whereHas('circular.markDistribution',function ($q){
            $q->where(DB::raw('(convert_written_mark*written_pass_mark)/100'),'<=',DB::raw('(job_applicant_marks.written*convert_written_mark)/job_circular_mark_distribution.written'));
            $q->where(DB::raw('(viva*viva_pass_mark)/100'),'<=',DB::raw('job_applicant_marks.viva'));
        });
        return $q;
    }
    public function fail(){
        $applicant = $this->applicant?$this->applicant:$this->applicant()->where('applicant_id', $this->applicant_id)->first();
        $mark_distribution = $applicant->circular->markDistribution;
        $written_pass_mark = 0;
        $viva_pass_mark = 0;
        if($mark_distribution){
            $written_pass_mark = (floatval($mark_distribution->convert_written_mark)*floatval($mark_distribution->written_pass_mark))/100;
            $viva_pass_mark = (floatval($mark_distribution->viva)*floatval($mark_distribution->viva_pass_mark))/100;
        }
        return $this->convertedWrittenMark()<$written_pass_mark||$this->viva<$viva_pass_mark;
    }

    public function totalMarks(){
        $t = 0;
        if($this->written) $t+=$this->convertedWrittenMark();
        if($this->edu_training) $t+=$this->edu_training;
        if($this->edu_experience) $t+=$this->edu_experience;
        if($this->applicant->physicalAgePoint()) $t+=$this->applicant->physicalAgePoint();
        if($this->physical) $t+=$this->physical;
        if($this->viva) $t+=$this->viva;
        if(is_array($this->additional_marks)){
            foreach ($this->additional_marks as $k=>$adm){
                $t+=floatval(array_values($adm)[0]);
            }
        }
        return $t;
    }
}
