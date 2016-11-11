<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_division';
    protected $guarded = [];
    function kpi(){
        return $this->hasMany('App\models\KpiGeneralModel','division_id','division_id');
    }
    
    public function personalinfo(){
        return $this->hasMany('App\models\PersonalInfo','division_id');
    }
    public function district(){
        return $this->hasMany('App\models\District', 'division_id');
    }
    public function thana(){
        return $this->hasMany('App\models\Thana', 'division_id');
    }
}