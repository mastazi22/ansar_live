<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobCircular extends Model
{
    //
    protected $table = 'job_circular';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function appliciant()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id');
    }

    public function appliciantMale()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('gender', 'Male');
    }

    public function appliciantFemale()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('gender', 'Female');
    }

    public function appliciantPaid()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')
            ->join('job_appliciant_payment_history', 'job_appliciant_payment_history.job_appliciant_id', '=', 'job_applicant.applicant_id')
            ->where('job_applicant.status', 'applied')
            ->where('job_appliciant_payment_history.bankTxStatus', 'SUCCESS');
    }

    public function appliciantNotPaid()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where(function ($q) {
            $q->whereHas('payment', function ($q) {
                $q->whereNotNull('txID');
                $q->where('bankTxStatus', 'FAIL');
            });
            $q->orWhere(function ($q) {
                $q->where('status', 'pending');
            });
        });
    }

    public function appliciantInitial()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where(function ($q) {
            $q->whereHas('payment', function ($q) {
                $q->whereNotNull('txID');
            });
            $q->where('status', 'initial');

        });
    }

    public function appliciantPaidNotApply()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where(function ($q) {
            $q->whereHas('payment', function ($q) {
                $q->whereNotNull('txID');
                $q->where('bankTxStatus', 'SUCCESS');
            });
            $q->where('status', 'paid');

        });
    }

    public function constraint()
    {
        return $this->hasOne(JobCircularConstraint::class, 'job_circular_id');
    }
}
