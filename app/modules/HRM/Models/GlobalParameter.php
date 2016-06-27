<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalParameter extends Model
{
    public $timestamps = false;
    protected $connection = 'hrm';
    protected $table = 'tbl_global_parameter';
}
