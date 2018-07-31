<?php

namespace App\modules\recruitment\subModule\training\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use Illuminate\Database\Eloquent\Model;

class TrainingCenter extends Model
{
    protected $table = "training_center";
    protected $guarded = ["id"];
    protected $connection = "recruitment.training";

    public function course(){
        return $this->belongsToMany(TrainingCourses::class,'training_course_center','course_id','center_id');
    }
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }
    public function quota(){
        return $this->hasMany(TrainingCenterQuota::class,'center_id');
    }
}
