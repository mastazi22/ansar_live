<?php

namespace App\modules\recruitment\subModule\training\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    protected $table = "training_session";
    protected $guarded = ["id"];
    protected $connection = "recruitment.training";

    public function course(){
        return $this->belongsTo(TrainingCourses::class,'course_category_id');
    }
}
