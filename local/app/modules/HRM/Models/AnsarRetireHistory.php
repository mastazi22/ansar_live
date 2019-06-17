<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnsarRetireHistory extends Model
{
    use SoftDeletes;
    protected $connection = 'hrm';
    protected $table="tbl_ansar_retirement_history";
    protected $guarded = ['id'];
    protected $dates =['deleted_at'];
    function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
}
