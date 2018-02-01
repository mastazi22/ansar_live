<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicationInstruction extends Model
{
    //
    protected $table = 'job_application_instruction';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';
}
