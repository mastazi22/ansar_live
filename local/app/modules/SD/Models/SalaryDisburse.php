<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use Illuminate\Database\Eloquent\Model;

class SalaryDisburse extends Model
{
    protected $table = "tbl_salary_disburst";
    protected $connection = "sd";
    protected $guarded = ["id"];

    public function kpi(){
        return $this->belongsTo(KpiGeneralModel::class,'kpi_id');
    }
    public function salarySheet(){
        return $this->belongsTo(SalarySheetHistory::class,'salary_sheet_id');
    }
}
