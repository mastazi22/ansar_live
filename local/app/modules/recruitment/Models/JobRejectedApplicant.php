<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobRejectedApplicant extends Model
{
    //
    protected $table = 'job_rejected_applicant';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function applicant(){
        return $this->belongsTo(JobAppliciant::class,'applicant_id','applicant_id');
    }
}
