<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AllDisease extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_long_term_disease';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'disease_id');
    }
}
