<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AllEducationName extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_education_info';
    public function educationName(){
        return $this->hasOne(Edication::class,'education_id');
    }
}
