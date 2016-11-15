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
}
