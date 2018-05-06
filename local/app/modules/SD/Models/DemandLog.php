<?php

namespace App\modules\SD\Models;

use Illuminate\Database\Eloquent\Model;

class DemandLog extends Model
{

    protected $connection = 'sd';
    protected $table = 'ansar_sd.tbl_demand_log';
}
