<?php

namespace App\modules\AVURP\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use Illuminate\Database\Eloquent\Model;

class OfferInfo extends Model
{
    protected $table = "avurp_offer_info";
    protected $connection = "avurp";
    protected $guarded = ['id'];


    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function vdp(){
        return $this->belongsTo(VDPAnsarInfo::class,'vdp_id');
    }
}
