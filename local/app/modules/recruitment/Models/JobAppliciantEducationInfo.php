<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobAppliciantEducationInfo extends Model
{
    //
    protected $table = 'job_appliciant_education_info';
    protected $connection = 'recruitment';
    protected $guarded = ['id','job_appliciant_id','job_education_id'];

    public function educationInfo(){
        return $this->belongsTo(JobEducationInfo::class,'job_education_id');
    }
}
