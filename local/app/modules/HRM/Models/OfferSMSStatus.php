<?php

namespace App\modules\HRM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OfferSMSStatus extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_offer_status';
    protected $guarded = ['id'];
    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
    public function district(){
        return $this->belongsTo(District::class,'last_offer_unit');
    }
    public function status(){
        return $this->hasOne(AnsarStatusInfo::class,'ansar_id','ansar_id');
    }
    public function panel(){
        return $this->hasOne(PanelModel::class,'ansar_id','ansar_id');
    }
}
