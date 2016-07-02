<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class BlockListInfoLogModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_blocklist_info_log";
}
