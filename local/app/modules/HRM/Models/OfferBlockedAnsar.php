<?php

namespace App\modules\HRM\Models;

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
        return $this->belongsTo(PersonalInfo::class, 'ansar_id');
    }
}
