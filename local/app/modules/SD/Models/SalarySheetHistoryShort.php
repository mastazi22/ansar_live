<?php

namespace App\modules\SD\Models;

use App\modules\AVURP\Models\KpiInfo;
use Illuminate\Database\Eloquent\Model;

class SalarySheetHistoryShort extends Model
{

    protected $connection = 'sd';
    protected $table = 'ansar_sd.tbl_salary_sheet_generate_history_short';
    protected $guarded = ['id'];

    public function kpi()
    {
        return $this->belongsTo(KpiInfo::class, 'kpi_id');
    }

    public function salaryHistory()
    {
        return $this->hasMany(SalaryHistoryShort::class, 'salary_sheet_id');
    }
}
