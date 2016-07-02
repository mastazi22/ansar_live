<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class BlackListInfoModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_blacklist_info_log";
}
