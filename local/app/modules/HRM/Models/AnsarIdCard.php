<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarIdCard extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_id_card';
    protected $fillable = ['ansar_id','issue_date','expire_date','type','status'];
}
