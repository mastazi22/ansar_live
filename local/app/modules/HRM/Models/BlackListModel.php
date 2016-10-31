<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class BlackListModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_blacklist_info";
    protected $guarded = [];
}
