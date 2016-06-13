<?php

namespace App\modules\SD\Models;

use Illuminate\Database\Eloquent\Model;

class DemandLog extends Model
{

    protected $connection = 'sd';
    protected $table = 'tbl_demand_log';
}
