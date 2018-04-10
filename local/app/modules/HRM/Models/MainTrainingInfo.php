<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class MainTrainingInfo extends Model
{
    //
    protected $table = 'tbl_main_training_info';
    protected $guarded=['id'];
    protected $connection='hrm';

    public function subTraining(){
        return $this->hasMany(SubTrainingInfo::class,'main_training_info_id');
    }
}
