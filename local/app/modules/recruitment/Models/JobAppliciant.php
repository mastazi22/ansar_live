<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
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
        return $this->hasMany(JobAppliciantEducationInfo::class,'job_appliciant_id');
    }
    public function payment(){
        return $this->hasOne(JobAppliciantPaymentHistory::class,'job_appliciant_id','applicant_id');
    }
}
