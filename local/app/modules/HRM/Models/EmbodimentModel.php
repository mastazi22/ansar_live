<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class EmbodimentModel extends Model
{
    protected $connection = 'hrm';
    protected $table='tbl_embodiment';
    protected $guarded = [];
    function kpi(){
        return $this->belongsTo(KpiGeneralModel::class,'kpi_id');
    }
    function ansar(){
        return $this->hasOne(PersonalInfo::class,'ansar_id','ansar_id');
    }
//    function transfer(){
//        return $this->hasMany('App\models\TransferAnsar','embodiment_id');
//    }
}
