<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_user_details';
    protected $guarded = ['id'];
    function user(){
        return $this->belongsTo('App\models\User');
    }
    function getFullName(){
        $name = $this->first_name.' '.$this->last_name;
        if(trim($name)){
            if($this->user->type==22){
                return $name."(DCA {$this->user->district->unit_name_eng})";
            }
            return $name;
        }
        if($this->user->type==22){
            return $this->user->user_name."(DCA {$this->user->district->unit_name_eng})";
        }
        return $this->user->user_name;
    }
}
