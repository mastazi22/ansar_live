<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class FreezingInfoLog extends Model
{
    protected $connection = 'hrm';
    protected $table = "tbl_freezing_info_log";
}
