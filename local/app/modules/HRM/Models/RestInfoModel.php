<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class RestInfoModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_rest_info";
    protected $guarded = [];
}
