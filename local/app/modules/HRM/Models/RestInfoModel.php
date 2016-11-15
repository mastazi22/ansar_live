<?php

namespace App\modules\HRM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RestInfoModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_rest_info";
    protected $guarded = [];
    public function log(){
        return $this->hasMany(RestInfoLogModel::class,'ansar_id','ansar_id');
    }
    public function saveLog($move_to = "Embodiment",$date = null){
        $this->log()->save(new RestInfoLogModel([
            'old_rest_id'=>$this->id,
            'old_embodiment_id'=>$this->old_embodiment_id,
            'old_memorandum_id'=>$this->memorandum_id,
            'rest_date'=>$this->rest_date,
            'total_service_days'=>$this->total_service_days,
            'rest_type'=>$this->rest_form,
            'disembodiment_reason_id'=>$this->disembodiment_reason_id,
            'comment'=>$this->comment,
            'move_to'=>$move_to,
            'move_date'=>!$date?Carbon::now():$date,
            'action_user_id'=>$this->action_user_id,
        ]));
    }
}
