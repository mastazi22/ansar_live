<?php

namespace App\modules\HRM\Models;

use Carbon\Carbon;
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
    function saveLog($move_to="Offer",$date=null){
        $this->panelLog()->save(new PanelInfoLogModel([
            'ansar_id'=>$this->ansar_id,
            'merit_list'=>$this->ansar_merit_list,
            'panel_date'=>$this->panel_date,
            'old_memorandum_id'=>!$this->memorandum_id ? "N\A" : $this->memorandum_id,
            'movement_date'=>!$date?Carbon::now():$date,
            'come_from'=>$this->come_from,
            'move_to'=>$move_to,
        ]));
    }
}
