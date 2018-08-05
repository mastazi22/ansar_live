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
    public function deposit(){
        return $this->hasOne(CashDeposite::class,'demand_or_salary_sheet_id')->where('payment_against','demand_sheet');
    }
}
