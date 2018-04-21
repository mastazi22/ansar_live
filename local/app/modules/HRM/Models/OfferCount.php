<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class OfferCount extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_offer_count';
    
    public function personalinfo(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id');
    }
}
