<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class SmsQueue extends Model
{
    //
    protected $table = 'job_sms_queue';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';
}
