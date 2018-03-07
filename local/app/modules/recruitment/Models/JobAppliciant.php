<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobAppliciant extends Model
{
    //
    protected $table = 'job_applicant';
    protected $connection = 'recruitment';
    protected $guarded = ['id','job_circular_id'];

    public function circular(){
        return $this->belongsTo(JobCircular::class,'job_circular_id');
    }
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function district(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }

    public function appliciantEducationInfo(){
        return $this->hasMany(JobAppliciantEducationInfo::class,'job_applicant_id');
    }
    public function payment(){
        return $this->hasOne(JobAppliciantPaymentHistory::class,'job_appliciant_id','applicant_id');
    }
    public function quota(){
        return $this->belongsTo(JobApplicantQuota::class,'unit_id','district_id');
    }
    public function getDateOfBirthAttribute($value){
        if($value) {
            return Carbon::parse($value)->format('d-M-Y');
        }
        return null;
    }
    public function getColumns(){
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function editHistory(){
        return $this->hasMany(JobApplicantEditHistory::class,'applicant_id');
    }

    public function selectedApplicant(){
        return $this->hasOne(JobSelectedApplicant::class,'applicant_id','applicant_id');
    }
    public function accepted(){
        return $this->hasOne(JobAcceptedApplicant::class,'applicant_id','applicant_id');
    }

    public function marks(){
        return $this->hasOne(JobApplicantMarks::class,'applicant_id','applicant_id');
    }
    public function hrmDetail(){
        return $this->hasOne(JobApplicantHRMDetails::class,'applicant_id','applicant_id');
    }
    public function education(){
        return $this->belongsToMany(JobEducationInfo::class,'job_appliciant_education_info','job_applicant_id','job_education_id');
    }
    public function physicalPoint(){
        if($this->status!='selected'){
            return $this->status;
        }
        $min_height = 64;
        $max_height = 74;
        $min_point = 15;
        $max_point = 30;
        $total_height = floatval($this->height_feet)*12+floatval($this->height_inch);
        $delta_height = $max_height-$min_height;
        $delta_point = $max_point-$min_point;
        if($total_height>=$max_height) return $max_point;
//        return $total_height;
        return number_format(($delta_point/$delta_height)*(($total_height-$min_height))+$min_point,2);
    }
    public function educationTrainingPoint(){
        $point_table = [
          4=>10,
          7=>8,
          8=>6,
          9=>4,
          10=>2
        ];
        $order = 'desc';
        $education_priority = $this->education()->orderBy('priority',$order)->first()['priority'];
//        $point_table[$education_priority] = 0;
        $training_point = $this->training_info=='VDP training'||$this->training_info=='TDP training'?5:0;
        return (isset($point_table[$education_priority])?$point_table[$education_priority]:0)+$training_point;
    }
}
