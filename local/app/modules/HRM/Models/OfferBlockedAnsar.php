<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class OfferBlockedAnsar extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_offer_blocked_ansar';
    
    public function personalinfo(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id');
    }
}
