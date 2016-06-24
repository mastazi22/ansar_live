<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSmsLog extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_sms_send_log';
}
