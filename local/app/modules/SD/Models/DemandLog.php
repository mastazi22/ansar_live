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

    public function scopeQuerySearch($q,$request){
        if($request->range){
            $q->whereHas('kpi',function($q) use($request){
                $q->where('division_id',$request->range);
            });
        }
        if($request->unit){
            $q->whereHas('kpi',function($q) use($request){
                $q->where('unit_id',$request->unit);
            });
        }
        if($request->thana){
            $q->whereHas('kpi',function($q) use($request){
                $q->where('thana_id',$request->thana);
            });
        }
        if($request->kpi){
            $q->whereHas('kpi',function($q) use($request){
                $q->where('id',$request->kpi);
            });
        }
        return $q;
    }
}
