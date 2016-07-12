<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class EmbodimentModel extends Model
{
    protected $connection = 'hrm';
    protected $table='tbl_embodiment';
    function kpi(){
        return $this->belongsTo('App\modelsApp\modules\HRM\Models\KpiGeneralModel','kpi_id');
    }
    function ansar(){
        return $this->hasOne('AApp\modules\HRM\Models\PersonalInfo','ansar_id','ansar_id');
    }
//    function transfer(){
//        return $this->hasMany('App\models\TransferAnsar','embodiment_id');
//    }
}
