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
}
