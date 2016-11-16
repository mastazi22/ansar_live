<?php

namespace App\modules\HRM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EmbodimentModel extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_embodiment';
    protected $guarded = [];

    function kpi()
    {
        return $this->belongsTo(KpiGeneralModel::class, 'kpi_id');
    }

    function ansar()
    {
        return $this->hasOne(PersonalInfo::class, 'ansar_id', 'ansar_id');
    }

    function saveLog($move_to = '', $date = null, $comment = '')
    {
        $this->log()->save(new EmbodimentLogModel([
            'old_embodiment_id' => $this->id,
            'old_memorandum_id' => $this->memorandum_id,
            'kpi_id' => $this->kpi_id,
            'reporting_date' => $this->reporting_date,
            'joining_date' => $this->joining_date,
            'transfered_date' => $this->transfered_date,
            'service_extension_status' => $this->service_extension_status,
            'release_date' => !$date ? Carbon::now() : $date,
            'move_to' => $move_to,
            'comment' => $comment,
            'action_user_id' => Auth::user()->id,
        ]));
    }

    function log()
    {
        return $this->hasMany(EmbodimentLogModel::class, 'ansar_id', 'ansar_id');
    }
//    function transfer(){
//        return $this->hasMany('App\models\TransferAnsar','embodiment_id');
//    }
}
