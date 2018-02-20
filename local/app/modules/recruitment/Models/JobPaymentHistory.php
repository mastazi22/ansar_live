<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobPaymentHistory extends Model
{
    protected $table = 'job_payment_history';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];
    public function paymentHistory(){
        return $this->belongsTo(JobAppliciantPaymentHistory::class,'job_applicant_payment_id');
    }
}
