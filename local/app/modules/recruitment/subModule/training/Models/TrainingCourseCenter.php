<?php

namespace App\modules\recruitment\subModule\training\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCourseCenter extends Model
{
    protected $table = "training_course_center";
    protected $guarded = ["id"];
    protected $connection = "recruitment.training";
    public $timestamps = false;
}
