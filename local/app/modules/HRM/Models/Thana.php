<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Thana extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_thana';
    protected $guarded = [];
    function kpi(){
        return $this->hasMany('App\models\KpiGeneralModel','thana_id','thana_id');
    }
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'thana_id');
    }
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function district(){
        return $this->belongsTo(District::class,'unit_id');
    }
}
