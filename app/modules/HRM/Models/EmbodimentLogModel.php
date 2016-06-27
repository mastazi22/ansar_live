<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class EmbodimentLogModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_embodiment_log";

    function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
    function kpi(){
        return $this->belongsTo(KpiGeneralModel::class, 'kpi_id');
    }
}
