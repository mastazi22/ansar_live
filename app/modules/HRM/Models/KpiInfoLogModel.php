<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class KpiInfoLogModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_kpi_log";
}
