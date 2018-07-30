<?php

namespace App\modules\AVURP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memorandum extends Model
{
    protected $connection = "avurp";
    protected $table = 'avurp_mem_id';
    protected $guarded = ['id'];
    public function embodiment(){
        return $this->hasOne(Embodiment::class,'mem_id');
    }
    public function embodimentLog(){
        return $this->hasOne(Embodiment::class,'mem_id')->onlyTrashed();
    }
}
