<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class OfferCancel extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_offer_cancel';

    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
}
