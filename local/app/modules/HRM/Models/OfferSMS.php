<?php

namespace App\modules\HRM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class OfferSMS extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_sms_offer_info';
    protected $guarded = [];
    protected $appends = array('offerType');
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
    public function getOfferCount($c=true){
        $count = OfferCount::where('ansar_id',$this->ansar_id)->first();
        if($c){
            if(!$count) return 0;
            return intval($count->count);
        }
        return $count;
    }
    public function saveCount($data = null){
        $count = OfferCount::where('ansar_id',$this->ansar_id)->first();
        if(!$data){
            if($count) {
                $count->increment('count');
            }
            else  {
                $count = new OfferCount;
                $count->count = 1;
                $count->ansar_id = $this->ansar_id;
                $count->save();
            }
        } else{
            if($count) {
                $count->update($data);
            }
            else  {
                OfferCount::create($data);
            }
        }
    }
    public function deleteCount(){
        $count = OfferCount::where('ansar_id',$this->ansar_id)->first();
        if($count) {
            $count->delete();
        }
    }
    public function deleteOfferStatus(){
        $offer_sms_status = OfferSMSStatus::where('ansar_id',$this->ansar_id)->first();
        if($offer_sms_status) {
            $offer_sms_status->delete();
        }
    }
    public function blockAnsarOffer(){
        $oba = new OfferBlockedAnsar;
        $oba->ansar_id = $this->ansar_id;
        $oba->last_offer_unit = $this->district_id;
        $oba->blocked_date = Carbon::now()->format('Y-m-d');
        $oba->save();
    }

    public function getOfferTypeAttribute()
    {
        $globalOfferDistrict = Config::get("app.offer");
        if (in_array($this->district_id, $globalOfferDistrict)) {
            return "Global";
        }
        return "Regional";
    }
}
