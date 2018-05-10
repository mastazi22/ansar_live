<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    protected $connection = 'sd';
    protected $table = 'tbl_attendance';

    public function kpi(){
        return $this->belongsTo(KpiGeneralModel::class,'kpi_id');
    }
    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id');
    }
}
