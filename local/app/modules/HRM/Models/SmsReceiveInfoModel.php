<?php

namespace App\modules\HRM\models;

use Illuminate\Database\Eloquent\Model;

class SmsReceiveInfoModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_sms_receive_info";
}
