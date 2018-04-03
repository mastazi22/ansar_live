<?php

namespace App\modules\AVURP\Models;

use App\modules\HRM\Models\Blood;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Unions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class VDPAnsarInfo extends Model
{
    //
    protected $table = "avurp_vdp_ansar_info";
    protected $connection = "avurp";
    protected $guarded = ['id'];

    public function education(){
        return $this->hasMany(VDPAnsarEducationInfo::class,'vdp_ansar_id');
    }
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }
    public function union(){
        return $this->belongsTo(Unions::class,'union_id');
    }
    public function bloodGroup(){
        return $this->belongsTo(Blood::class,'blood_group_id');
    }
    public function setDateOfBirthAttribute($value){
        $this->attributes['date_of_birth'] = Carbon::parse($value)->format('Y-m-d');
    }
    public function getDateOfBirthAttribute($value){
        return Carbon::parse($value)->format('d-M-Y');
    }
}
