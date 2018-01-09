<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class FreezedAnsarEmbodimentDetail extends Model
{
    //
    protected $table = 'tbl_freezed_ansar_embodiment_details';
    protected $guarded = ['id'];
    function kpi()
    {
        return $this->belongsTo(KpiGeneralModel::class, 'freezed_kpi_id');
    }
}
