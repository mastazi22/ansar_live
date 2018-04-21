<?php

namespace App\modules\HRM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OfferSMS extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_sms_offer_info';
    protected $guarded = [];
    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
    public function district(){
        return $this->belongsTo(District::class,'district_id');
    }
    public function log(){
        return $this->hasMany(OfferSmsLog::class,'ansar_id','ansar_id');
    }
    public function status(){
        return $this->hasOne(AnsarStatusInfo::class,'ansar_id','ansar_id');
    }
    public function panel(){
        return $this->hasOne(PanelModel::class,'ansar_id','ansar_id');
    }
    public function saveLog($reply = 'Yes'){
        $this->log()->save(new OfferSmsLog([
            'offered_district'=>$this->district_id,
            'sms_offer_id'=>$this->id,
            'action_user_id'=>$this->action_user_id,
            'offered_date'=>$this->sms_send_datetime,
            'action_date' => Carbon::now(),
            'reply_type'=>$reply
        ]));
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
            $count->ansar_id = $this->ansar_id;
            $count->save();
        }
    }
    public function deleteCount(){
        $count = OfferCount::where('ansar_id',$this->ansar_id)->first();
        if($count) {
            $count->delete();
        }
    }
    public function blockAnsarOffer(){
        $oba = new OfferBlockedAnsar;
        $oba->ansar_id = $this->ansar_id;
        $oba->last_offer_unit = $this->district_id;
        $oba->save();
    }
}
