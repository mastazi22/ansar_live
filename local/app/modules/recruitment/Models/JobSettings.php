<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobSettings extends Model
{
    //
    protected $table = 'job_settings';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];
}
