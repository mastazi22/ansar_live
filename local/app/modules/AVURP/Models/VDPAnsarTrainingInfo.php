<?php

namespace App\modules\AVURP\Models;

use App\modules\HRM\Models\MainTrainingInfo;
use App\modules\HRM\Models\SubTrainingInfo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class VDPAnsarTrainingInfo extends Model
{
    protected $table = "avurp_vdp_ansar_training_info";
    protected $connection = "avurp";
    protected $guarded = ['id'];
    public function vdp_info(){
        return $this->belongsTo(VDPAnsarInfo::class,'vdp_ansar_info_id');
    }
    public function main_training(){
        return $this->belongsTo(MainTrainingInfo::class,'training_id');
    }
    public function sub_training(){
        return $this->belongsTo(SubTrainingInfo::class,'sub_training_id');
    }
    public function setTrainingStartDateAttribute($value){
        if($value) {
            $this->attributes['training_start_date'] = Carbon::parse($value)->format('Y-m-d');
        }
        else{
            $this->attributes['training_start_date'] = null;
        }
    }
    public function setTrainingEndDateAttribute($value){
        if($value) {
            $this->attributes['training_end_date'] = Carbon::parse($value)->format('Y-m-d');
        }
        else{
            $this->attributes['training_end_date'] = null;
        }
    }
}
