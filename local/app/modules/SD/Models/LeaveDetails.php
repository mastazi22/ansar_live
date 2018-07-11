<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Model;

class LeaveDetails extends Model
{

    protected $connection = 'sd';
    protected $table = 'tbl_leave_details';
    protected $guarded = ['id'];

    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
    public function leave(){
        return $this->belongsTo(Leave::class,'leave_id');
    }
}
