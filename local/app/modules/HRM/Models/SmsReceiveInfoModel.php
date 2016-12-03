<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class SmsReceiveInfoModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_sms_receive_info";
    protected $guarded = [];

    public function log(){
        return $this->hasMany(OfferSmsLog::class,'ansar_id','ansar_id');
    }
    public function saveLog(){
        $this->log()->save(new OfferSmsLog([
            'offered_district'=>$this->offered_district,
            'sms_offer_id'=>$this->id,
            'action_user_id'=>0,
            'offered_date'=>$this->sms_send_datetime,
            'action_date'=>$this->sms_received_datetime,
            'reply_type'=>'Yes'
        ]));
    }
    public function status(){
        return $this->belongsTo(AnsarStatusInfo::class,'ansar_id','ansar_id');
    }
    public function panel(){
        return $this->hasOne(PanelModel::class,'ansar_id','ansar_id');
    }
}
