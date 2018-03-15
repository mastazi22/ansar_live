<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FreezedAnsarEmbodimentDetail extends Model
{
    //
    protected $table = 'tbl_freezed_ansar_embodiment_details';
    protected $connection='hrm';
    protected $guarded = ['id'];
    function kpi()
    {
        return $this->belongsTo(KpiGeneralModel::class, 'freezed_kpi_id');
    }
    function saveLog($move_to = '', $date = null, $comment = '',$reason=0)
    {
        $this->log()->save(new EmbodimentLogModel([
            'old_embodiment_id' => $this->embodiment_id,
            'old_memorandum_id' => $this->em_mem_id,
            'kpi_id' => $this->freezed_kpi_id,
            'reporting_date' => $this->reporting_date,
            'joining_date' => $this->embodied_date,
            'transfered_date' => $this->transfer_date,
            'service_extension_status' => '',
            'release_date' => !$date ? Carbon::now() : $date,
            'move_to' => $move_to,
            'comment' => $comment,
            'disembodiment_reason_id' => $reason,
            'action_user_id' => Auth::user()->id,
        ]));
    }

    function log()
    {
        return $this->hasMany(EmbodimentLogModel::class, 'ansar_id', 'ansar_id');
    }
}
