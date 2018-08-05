<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Model;

class SalarySheetHistory extends Model
{

    protected $connection = 'sd';
    protected $table = 'ansar_sd.tbl_salary_sheet_generate_history';
    protected $guarded = ['id'];

    public function kpi(){
        return $this->belongsTo(KpiGeneralModel::class,'kpi_id');
    }
    public function salaryHistory(){
        return $this->hasMany(SalaryHistory::class,'salary_sheet_id');
    }
    public function deposit(){
        return $this->hasOne(CashDeposite::class,'demand_or_salary_sheet_id')->where('payment_against','salary_sheet');
    }
}
