<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_user_type';
    
    public function user(){
        return $this->hasMany('App\models\User','type','type_code');
    }
}
