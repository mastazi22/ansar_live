<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarStatusInfo extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_ansar_status_info";
    protected $guarded = [];
    function ansar(){
        return $this->belongsTo('App\models\PersonalInfo','ansar_id','ansar_id');
    }
    function getStatus(){
        $status = [];
        switch(1){
            case $this->free_status:
                array_push($status,'free');
                break;
            case $this->pannel_status:
                array_push($status,'panel');
                break;
            case $this->embodied_status:
                array_push($status,'embodied');
                break;
            case $this->offer_sms_status:
                array_push($status,'offer');
                break;
            case $this->freezing_status:
                array_push($status,'freeze');
                break;
            case $this->block_list_status:
                array_push($status,'block');
                break;
            case $this->black_list_status:
                array_push($status,'black');
                break;
            case $this->rest_status:
                array_push($status,'rest');
                break;
            case $this->retierment_status:
                array_push($status,'disembodied');
                break;
            case $this->early_retierment_status:
                array_push($status,'early_retierment_status');
                break;
            default:
                array_push($status,'unverified');
                break;

        }
        return $status;
    }
}
