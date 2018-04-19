<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserCreationRequest extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_user_creation_request';
    
    public function user(){
        return $this->hasOne(User::class,'user_parent_id');
    }
}
