<?php

namespace App\modules\AVURP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Embodiment extends Model
{
    use SoftDeletes;
    protected $connection = "avurp";
    protected $table = 'avurp_embodiment_info';
    protected $guarded = ['id'];
    protected $dates = ["deleted_at"];
    public function vdp(){
        return $this->belongsTo(VDPAnsarInfo::class,'vdp_id');
    }
    public function kpi(){
        return $this->belongsTo(KpiInfo::class,'kpi_id');
    }
}
