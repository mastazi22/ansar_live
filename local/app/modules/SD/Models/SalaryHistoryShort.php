<?php

namespace App\modules\SD\Models;

use App\modules\AVURP\Models\KpiInfo;
use App\modules\AVURP\Models\VDPAnsarInfo;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Model;

class SalaryHistoryShort extends Model
{

    protected $connection = 'sd';
    protected $table = 'ansar_sd.tbl_salary_history_short';
    protected $guarded = ['id'];

    public function kpi(){
        return $this->belongsTo(KpiInfo::class,'kpi_id');
    }
    public function ansar(){
        return $this->belongsTo(VDPAnsarInfo::class,'ansar_id');
    }
    public function salarySheet(){
        return $this->belongsTo(SalarySheetHistoryShortrySheetHistory::class,'salary_sheet_id');
    }
}
