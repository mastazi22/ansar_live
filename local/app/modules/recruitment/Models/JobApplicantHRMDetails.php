<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicantHRMDetails extends Model
{
    //
    protected $table = 'job_applicant_hrm_details';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function applicant(){
        return $this->belongsTo(JobAppliciant::class,'applicant_id','applicant_id');
    }
}
