<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicantPoints extends Model
{
    //
    protected $table = 'job_applicant_points';
    protected $guarded = ['id', 'job_circular_id'];
    protected $connection = 'recruitment';

    public function circular()
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }

    public static function rulesName()
    {
        return [
            '' => '--Select a rule--',
            'height' => 'Height',
            'age' => 'Age',
            'education' => 'Education',
            'training' => 'Training'
        ];
    }

    public static function rulesFor()
    {
        return [
            '' => '--Select a option--',
            'physical' => 'Physical',
            'edu_training' => 'Education & Training'
        ];
    }
}
