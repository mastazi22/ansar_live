<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AllSkill extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_particular_skill';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'skill_id');
    }
}
