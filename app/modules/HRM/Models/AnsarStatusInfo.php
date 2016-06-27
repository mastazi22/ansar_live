<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarStatusInfo extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_ansar_status_info";
    function ansar(){
        return $this->belongsTo('App\models\PersonalInfo','ansar_id','ansar_id');
    }
}
