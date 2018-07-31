<?php

namespace App\modules\AVURP\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferInfo extends Model
{
    use SoftDeletes;
    protected $table = "avurp_offer_info";
    protected $connection = "avurp";
    protected $guarded = ['id'];
    protected $dates = ["deleted_at"];


    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function vdp(){
        return $this->belongsTo(VDPAnsarInfo::class,'vdp_id');
    }
    public function getSmsSendDateTimeAttribute($value){
        return Carbon::parse($value)->format('d-M-Y');
    }
}
