<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingInfo extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_training_info';
    protected $guarded = [];
    public function personalinfo(){
        return $this->belongsTo('App\models\PersonalInfo','ansar_id');
    }
    public function rank(){
        return $this->belongsTo(Designation::class,'training_designation','id');
    }
}
