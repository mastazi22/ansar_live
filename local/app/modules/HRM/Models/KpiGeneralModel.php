<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class KpiGeneralModel extends Model
{
    protected $connection = 'hrm';
    protected $table ="tbl_kpi_info";
    protected $guarded = [];
//    function division(){
//       return $this->belongsTo('App\models\Division','division_id');
//    }
    function unit(){
        return $this->belongsTo('App\modules\HRM\Models\District','unit_id');
    }
//    function thana(){
//        return $this->belongsTo('App\models\Thana','thana_id');
//    }
    function embodiment(){
        return $this->hasMany('App\modules\HRM\Models\EmbodimentModel','kpi_id');
    }
//    function freezing_info(){
//        return $this->hasOne('App\models\FreezingInfoModel','kpi_id');
//   }
    function demand(){
        $this->hasMany('App\modules\SD\Models\DemandLog','kpi_id');
    }
    function details(){
        return $this->hasOne(KpiDetailsModel::class, 'kpi_id');
    }
}
