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
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('job_applicant.status', 'applied');
    }

    public function appliciantNotPaid()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('status', 'pending');
    }

    public function appliciantInitial()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('status', 'initial');
    }

    public function appliciantPaidNotApply()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('status', 'paid');
    }

    public function constraint()
    {
        return $this->hasOne(JobCircularConstraint::class, 'job_circular_id');
    }
}
