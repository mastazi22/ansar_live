<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicantPoints extends Model
{
    //
    protected $table = 'job_applicant_points';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';
}
