<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class TransferAnsar extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_transfer_ansar';
    function presentKpi(){
        return $this->belongsTo(KpiGeneralModel::class,'present_kpi_id');
    }
    function transferKpi(){
        return $this->belongsTo(KpiGeneralModel::class,'transfered_kpi_id');
    }
    function embodiment(){
        return $this->belongsTo(EmbodimentModel::class,'embodiment_id');
    }
    function memorandum(){
        return $this->belongsTo(MemorandumModel::class,'memorandum_id');
    }
}
