<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Edication extends Model
{
    protected $connection = 'hrm';
    protected  $table= "tbl_ansar_education_info";
    protected $guarded = ['id'];
    public function personalinfo(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
    public function educationName(){
        return $this->hasOne(AllEducationName::class,'id','education_id');
    }
}