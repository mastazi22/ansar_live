<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_user_details';
    function user(){
        return $this->belongsTo('App\models\User');
    }
    function getFullName(){
        $name = $this->first_name.' '.$this->last_name;
        if(trim($name)){
            return $name;
        }
        return $this->user->user_name;
    }
}
