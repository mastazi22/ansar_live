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
        return Carbon::parse($value)->format('d-M-Y');
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

    public function marks(){
        return $this->hasOne(JobApplicantMarks::class,'applicant_id','applicant_id');
    }
}
