<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobCircularConstraint extends Model
{
    //
    protected $table = 'job_circular_constraint';
    protected $connection = 'recruitment';
    protected $guarded = ['id','job_circular_id'];
}
