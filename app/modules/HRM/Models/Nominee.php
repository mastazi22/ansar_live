<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    protected $table= 'tbl_amsar_nominee_info';
    
    public function personalinfo(){
        return $this->belongsTo('App\models\PersonalInfo','ansar_id');
    }
}
