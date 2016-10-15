<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveSMSModel extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_sms_receive_info';
    protected $guarded = [];
}
