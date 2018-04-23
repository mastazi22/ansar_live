<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_user_permisson';
    protected $guarded = ['id'];
    function user(){
        return $this->belongsTo('App\models\User');
    }
}
