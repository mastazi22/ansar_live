<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class PanelInfoLogModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_panel_info_log";
    protected $guarded = [];

    public function panel(){
        return $this->hasOne(PanelModel::class,'ansar_id','ansar_id');
    }
}
