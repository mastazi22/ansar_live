<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarStatusInfo extends Model
{
    const FREE_STATUS = 'Free';
    const PANEL_STATUS = 'Panel';
    const OFFER_STATUS = 'Offer';
    const EMBODIMENT_STATUS = 'Embodied';
    const REST_STATUS = 'rest';
    const FREEZE_STATUS = 'freeze';
    const BLOCK_STATUS = 'Block';
    const BLACK_STATUS = 'Black';
    const NOT_VERIFIED_STATUS = 'Not Verified';
    const RETIREMENT_STATUS = 'disembodied';
    const EARLY_RETIREMENT_STATUS = 'early_retierment_status';
    const OFFER_BLOCK_STATUS = 'offer_block_status';
    protected $connection = 'hrm';
    protected $table="tbl_ansar_status_info";
    protected $guarded = ['id'];
    function ansar(){
        return $this->belongsTo('App\models\PersonalInfo','ansar_id','ansar_id');
    }
    function panel(){
        return $this->hasOne(PanelModel::class,'ansar_id','ansar_id');
    }
    function offer(){
        return $this->hasOne(OfferSMS::class,'ansar_id','ansar_id');
    }
    function rest(){
        return $this->hasOne(RestInfoModel::class,'ansar_id','ansar_id');
    }
    function offerReceived(){
        return $this->hasOne(SmsReceiveInfoModel::class,'ansar_id','ansar_id');
    }
    function getStatus(){
        $status = [];
            if($this->block_list_status) array_push($status,self::BLOCK_STATUS);
            if($this->black_list_status) array_push($status,self::BLACK_STATUS);
            if($this->free_status) array_push($status,self::FREE_STATUS);
            if($this->pannel_status) array_push($status,self::PANEL_STATUS);
            if($this->embodied_status) array_push($status,self::EMBODIMENT_STATUS);
            if($this->offer_sms_status) array_push($status,self::OFFER_STATUS);
            if($this->freezing_status) array_push($status,self::FREEZE_STATUS);
            if($this->rest_status) array_push($status,self::REST_STATUS);
            if($this->retierment_status) array_push($status,self::RETIREMENT_STATUS);
            if($this->early_retierment_status) array_push($status,self::EARLY_RETIREMENT_STATUS);
            if(!$this->block_list_status&&!$this->black_list_status&&!$this->free_status&&!$this->pannel_status&&!$this->embodied_status&&!$this->offer_sms_status&&!$this->freezing_status&&!$this->rest_status&&!$this->retierment_status&&!$this->early_retierment_status) array_push($status,self::NOT_VERIFIED_STATUS);


        return $status;
    }
    public function getAnsarForDirectEmbodiment(){
        if($this->block_list_status==1){
            return false;
        }
        switch(1){
            case $this->pannel_status:
                $this->panel->saveLog('Emboded');
                $this->panel->delete();
                $this->update([
                    'pannel_status'=>0,
                    'embodied_status'=>1,
                ]);
                return  "PANLE";
            case $this->offer_sms_status:
                if($this->offer){
                    $this->offer->saveLog();
                    $this->offer->delete();
                }
                else{
                    $this->offerReceived->saveLog();
                    $this->offerReceived->delete();
                }
                $this->update([
                    'offer_sms_status'=>0,
                    'embodied_status'=>1,
                ]);
                return "OFFER";
            case $this->rest_status:
                $this->rest->saveLog();
                $this->rest->delete();
                $this->update([
                    'rest_status'=>0,
                    'embodied_status'=>1,
                ]);
                return "REST";
            default:
                return false;

        }
    }
}
