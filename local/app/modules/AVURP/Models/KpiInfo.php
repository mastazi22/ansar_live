<?php

namespace App\modules\AVURP\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use Illuminate\Database\Eloquent\Model;

class KpiInfo extends Model
{
    protected $table = "avurp_kpi_info";
    protected $connection = "avurp";
    protected $guarded = ['id'];


    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }
    public function embodiment(){
        return $this->hasMany(Embodiment::class,'kpi_id');
    }
    public function vdp(){
        return $this->belongsToMany(VDPAnsarInfo::class,'avurp_embodiment_info','kpi_id','vdp_id');
    }

}
