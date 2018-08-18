<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\SD\Helper\Facades\DemandConstantFacdes;
use Illuminate\Database\Eloquent\Model;

class SalaryDisburse extends Model
{
    protected $table = "tbl_salary_disburst";
    protected $connection = "sd";
    protected $guarded = ["id"];

    public function kpi()
    {
        return $this->belongsTo(KpiGeneralModel::class, 'kpi_id');
    }

    public function salarySheet()
    {
        return $this->belongsTo(SalarySheetHistory::class, 'salary_sheet_id');
    }

    public function stampAmount()
    {
        if ($this->extra_amount_include) {
            $percent = sprintf("%.2f",($this->extra_amount * DemandConstantFacdes::getValue('DCEP')->cons_value) / 100);
        } else{
            $percent = 0;
        }
        return sprintf('%d',$this->dc_account_amount-$percent);
    }
}
