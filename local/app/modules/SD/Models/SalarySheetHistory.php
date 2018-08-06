<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SalarySheetHistory extends Model
{

    protected $connection = 'sd';
    protected $table = 'ansar_sd.tbl_salary_sheet_generate_history';
    protected $guarded = ['id'];
//    protected $appends = ["sheet_summery"];
    protected $hidden = ["data"];

    public function kpi(){
        return $this->belongsTo(KpiGeneralModel::class,'kpi_id');
    }
    public function salaryHistory(){
        return $this->hasMany(SalaryHistory::class,'salary_sheet_id');
    }
    public function deposit(){
        return $this->hasOne(CashDeposite::class,'demand_or_salary_sheet_id')->where('payment_against','salary_sheet');
    }

    public function getSummeryAttribute($value){
        try {
            $summery = unserialize(gzdecode($value));
            return $summery;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
    public function scopeQuerySearch($q,$request)
    {
        if ($request->range) {
            $q->whereHas('kpi', function ($q) use ($request) {
                $q->where('division_id', $request->range);
            });
        }
        if ($request->unit) {
            $q->whereHas('kpi', function ($q) use ($request) {
                $q->where('unit_id', $request->unit);
            });
        }
        if ($request->thana) {
            $q->whereHas('kpi', function ($q) use ($request) {
                $q->where('thana_id', $request->thana);
            });
        }
        if ($request->kpi) {
            $q->whereHas('kpi', function ($q) use ($request) {
                $q->where('id', $request->kpi);
            });
        }
        return $q;
    }
}
