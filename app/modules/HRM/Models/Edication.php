<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Edication extends Model
{
    protected $connection = 'hrm';
    protected  $table= "tbl_ansar_education_info";
    public function personalinfo(){
        return $this->belongsTo('App\models\PersonalInfo','ansar_id');
    }
    public function educationName(){
        return $this->hasOne('App\models\AllEducationName','id','education_id');
//        return $this->belongsTo('App\models\AllEducationName','education_id');
    }
}