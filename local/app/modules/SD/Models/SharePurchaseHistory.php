<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Model;

class SharePurchaseHistory extends Model
{

    protected $connection = 'sd';
    protected $table = 'tbl_ansar_share_purchase';
    protected $guarded = ['id'];

    public function salarySheet(){
        return $this->belongsTo(SalarySheetHistory::class,'salary_sheet_id');
    }
    public function salaryDisburse(){
        return $this->belongsTo(SalaryDisburse::class,'salary_disburse_id');
    }
    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }

}
