<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class MemorandumModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_memorandum_id";
//    function transfer(){
//        return $this->hasMany('App\models\TransferAnsar','transfer_memorandum_id','memorandum_id');
//    }
//    function embodiment(){
//        return $this->hasMany('App\models\EmbodimentModel','memorandum_id','memorandum_id');
//    }
}
