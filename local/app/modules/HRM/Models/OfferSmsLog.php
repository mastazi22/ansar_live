<?php

namespace App\modules\HRM\Models;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

class OfferSmsLog extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_sms_send_log';
    protected $guarded = [];
    protected $appends = array('offerType');

    public function district()
    {
        return $this->belongsTo(District::class, 'offered_district');
    }

    public function getOfferTypeAttribute()
    {
        $globalOfferDistrict = Config::get("app.offer");
        if (in_array($this->offered_district, $globalOfferDistrict)) {
            return "Global";
        }
        return "Regional";
    }
}
