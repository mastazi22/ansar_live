<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobAppliciant extends Model
{
    //
    protected $table = 'job_appliciant';
    protected $connection = 'recruitment';
    protected $guarded = ['id','job_circular_id'];

    public function circular(){
        return $this->belongsTo(JobCircular::class,'job_circular_id');
    }

    public function appliciantEducationInfo(){
        return $this->hasMany(JobAppliciantEducationInfo::class,'job_appliciant_id');
    }
}
