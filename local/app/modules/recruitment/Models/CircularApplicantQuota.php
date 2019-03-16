<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CircularApplicantQuota extends Model
{
    use SoftDeletes;
    protected $table = 'job_circular_applicant_quota';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];
}
