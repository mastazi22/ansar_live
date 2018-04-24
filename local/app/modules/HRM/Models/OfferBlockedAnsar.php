<?php

namespace App\modules\HRM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferBlockedAnsar extends Model
{
    use SoftDeletes;
    protected $connection = 'hrm';
    protected $table = 'tbl_offer_blocked_ansar';
    protected $dates=['deleted_at'];
    public function personalinfo()
    {
        return $this->belongsTo(PersonalInfo::class, 'ansar_id','ansar_id');
    }
    public function unit()
    {
        return $this->belongsTo(District::class, 'last_offer_unit');
    }
    public function getBlockedDateAttribute($value){
        if($value) return Carbon::parse($value)->format('d-M-Y');
        else return Carbon::parse($this->created_at)->format('d-M-Y');
    }
}
