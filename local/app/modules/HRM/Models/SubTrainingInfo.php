<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class SubTrainingInfo extends Model
{
    //
    protected $table = 'tbl_sub_training_info';
    protected $guarded=['id'];
    protected $connection='hrm';

    public function mainTraining(){
        return $this->belongsTo(MainTrainingInfo::class,'main_training_info_id');
    }
}
