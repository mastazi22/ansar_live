<?php

namespace App\modules\HRM\Models;

use App\Helper\Facades\UserPermissionFacades;
use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    protected $table= 'tbl_amsar_nominee_info';
    protected $connection='hrm';
    protected $guarded=['id'];
    
    public function personalinfo(){
        return $this->belongsTo('App\models\PersonalInfo','ansar_id');
    }

    function getNomineeContactNoAttribute($value){
        return UserPermissionFacades::userPermissionExists('view_mobile_no')?$value:"";
    }
    function getNomineeContactNoEngAttribute($value){
        return UserPermissionFacades::userPermissionExists('view_mobile_no')?$value:"";
    }
}
