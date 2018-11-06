<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Unions extends Model
{
    //
    protected $table = 'tbl_unions';
    protected $connection = "hrm";
    protected $guarded = [];
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }
}
