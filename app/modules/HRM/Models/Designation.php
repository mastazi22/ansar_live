<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $connection = 'hrm';
    protected $table= 'tbl_designations';
    function ansar(){
        return $this->hasMany('App\models\PersonalInfo','designation_id');
    }
}
