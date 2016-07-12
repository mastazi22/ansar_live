<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Blood extends Model
{
    protected $connection = 'hrm';
    protected $table= 'tbl_blood_group';
    
    public function personalinfo(){
        return $this->hasOne('App\models\PersonalInfo','id');
    }

}
