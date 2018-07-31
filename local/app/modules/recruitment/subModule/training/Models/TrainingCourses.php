<?php

namespace App\modules\recruitment\subModule\training\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCourses extends Model
{
    protected $table = "training_courses";
    protected $guarded = ["id"];
    protected $connection = "recruitment.training";

    public function category(){
        return $this->belongsTo(TrainingCategory::class,'course_category_id');
    }
    public function center(){
        return $this->belongsToMany(TrainingCenter::class,'training_course_center','course_id','center_id');
    }
    public function courseCenter(){
        return $this->hasMany(TrainingCourseCenter::class,'course_id');
    }
}
