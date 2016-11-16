<?php

namespace App\modules\HRM\Models;

use App\modules\HRM\Controllers\FreezeController;
use Illuminate\Database\Eloquent\Model;

class FreezingInfoModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_freezing_info";
    protected $guarded = [];
    function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
    function kpi(){
        return $this->belongsTo(KpiGeneralModel::class,'kpi_id','kpi_id');
    }
    function embodiment(){
        return $this->belongsTo(EmbodimentModel::class,'ansar_embodiment_id');
    }
    function log(){
        return $this->hasMany(FreezingInfoLog::class,'ansar_id','ansar_id');
    }
    function saveLog($move_to='',$date=null,$comment=''){
        $this->log()->save(new FreezingInfoLog([
            'old_freez_id'=>$this->id,
            'ansar_id'=>$this->ansar_id,
            'ansar_embodiment_id'=>$this->ansar_embodiment_id,
            'freez_reason'=>$this->freez_reason,
            'freez_date'=>$this->freez_date,
            'move_frm_freez_date'=>!$date ? Carbon::now() : $date,
            'comment_on_freez'=>$this->comment_on_freez,
            'move_to'=>$move_to,
            'comment_on_move'=>$comment,
            'direct_status'=>0,
            'action_user_id'=>$this->action_user_id,
        ]));
    }
}
