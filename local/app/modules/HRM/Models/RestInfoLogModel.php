<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class RestInfoLogModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_rest_info_log";
    protected $guarded = [];
}
