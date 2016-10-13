<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class PanelModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_panel_info";
    protected $guarded = [];
    function ansarInfo(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id');
    }
    function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }
    function panelLog(){
        return $this->hasMany(PanelInfoLogModel::class,'panel_id_old');
    }
}
