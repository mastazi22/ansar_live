<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use Illuminate\Database\Eloquent\Model;

class DemandLog extends Model
{

    protected $connection = 'sd';
    protected $table = 'tbl_demand_log';

    public function kpi(){
        return $this->belongsTo(KpiGeneralModel::class,'kpi_id');
    }
}
