<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class BlockListModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_blocklist_info";
    protected $guarded = [];
}
