<?php

namespace App\modules\HRM\Models;

use App\models\User;
use Illuminate\Database\Eloquent\Model;

class ActionUserLog extends Model
{
    //
    protected $connection = 'hrm';
    protected $table='tbl_user_action_log';

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'action_by','id');
    }
    public function getFromKpi(){
        $kpi = KpiGeneralModel::find($this->from_state);
        if($kpi){
            return $kpi->kpi_name;
        }
        return "n\a";
    }
    public function getToKpi(){
        $kpi = KpiGeneralModel::find($this->to_state);
        if($kpi){
            return $kpi->kpi_name;
        }
        return "n\a";
    }
}
