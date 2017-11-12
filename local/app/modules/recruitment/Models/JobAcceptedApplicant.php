<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobAcceptedApplicant extends Model
{
    //
    protected $table = 'job_accepted_applicant';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function applicant(){
        return $this->belongsTo(JobAppliciant::class,'applicant_id','applicant_id');
    }
}
