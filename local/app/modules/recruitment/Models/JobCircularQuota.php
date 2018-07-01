<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use Illuminate\Database\Eloquent\Model;

class JobCircularQuota extends Model
{
    //
    protected $table = 'job_circular_quota';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';

    public function quota(){
        return $this->hasMany(JobApplicantQuota::class,'job_circular_quota_id');
    }
}
