<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Thana extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_thana';
    function kpi(){
        return $this->hasMany('App\models\KpiGeneralModel','thana_id','thana_id');
    }
    
    public function personalinfo(){
        return $this->hasOne('App\models\PersonalInfo','thana_id');
    }
    public function division(){
        return $this->belongsTo('App\models\Division','division_id');
    }
    public function district(){
        return $this->belongsTo('App\models\District','unit_id');
    }
}
