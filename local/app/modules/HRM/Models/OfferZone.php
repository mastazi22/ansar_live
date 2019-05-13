<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class OfferZone extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_offer_zone';
    protected $guarded = ['id'];
    
    public function range(){
        return $this->belongsTo(Division::class,'range_id');
    }
    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function offerZoneRange(){
        return $this->belongsTo(Division::class,'offer_zone_range_id');
    }
    public function offerZoneUnit(){
        return $this->belongsTo(District::class,'offer_zone_unit_id');
    }
}
