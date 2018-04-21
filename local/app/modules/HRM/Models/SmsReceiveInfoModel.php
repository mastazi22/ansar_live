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
    public function getOfferCount(){
        $count = OfferCount::where('ansar_id',$this->ansar_id)->first();
        if(!$count) return 0;
        return intval($count->count);
    }
    public function saveCount(){
        $count = OfferCount::where('ansar_id',$this->ansar_id)->first();
        if($count) {
            $count->increment('count');
        }
        else  {
            $count = new OfferCount;
            $count->count = 1;
            $count->save();
        }
    }
    public function blockAnsarOffer(){
        $oba = new OfferBlockedAnsar;
        $oba->ansar_id = $this->ansar_id;
        $oba->last_offer_unit = $this->offered_district;
        $oba->save();
    }
    public function deleteCount(){
        $count = OfferCount::where('ansar_id',$this->ansar_id)->first();
        if($count) {
            $count->delete();
        }
    }
}

