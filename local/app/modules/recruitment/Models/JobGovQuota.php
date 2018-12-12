<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use Illuminate\Database\Eloquent\Model;

class JobGovQuota extends Model
{
    //
    protected $table = 'job_quota';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';

    public function applicant(){
        return $this->belongsTo(JobAppliciant::class,'job_applicant_id');
    }
}
