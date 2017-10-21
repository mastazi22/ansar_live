<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobAppliciantPaymentHistory extends Model
{
    protected $table = 'job_appliciant_payment_history';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];
}
