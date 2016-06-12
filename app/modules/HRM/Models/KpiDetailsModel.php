<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class KpiDetailsModel extends Model
{
    protected $connection = 'hrm';
    protected $table ="tbl_kpi_detail_info";
}
