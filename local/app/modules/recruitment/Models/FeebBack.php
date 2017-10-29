<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class FeebBack extends Model
{
    //
    protected $table = 'job_feedback';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';

    public function applicant(){
        return $this->belongsTo(JobAppliciant::class,'mobile_no_self','mobile_no_self');
    }
}
