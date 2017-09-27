<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobEducationInfo extends Model
{
    //
    protected $table = 'job_education_info';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];
}
