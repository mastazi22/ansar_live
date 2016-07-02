<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingInfo extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_training_info';
    
    public function personalinfo(){
        return $this->belongsTo('App\models\PersonalInfo','ansar_id');
    }
}
