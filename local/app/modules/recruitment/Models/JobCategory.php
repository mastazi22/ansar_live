<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobCategory extends Model
{
    //
    protected $table = 'job_category';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function circular(){
        return $this->hasMany(JobCircular::class,'job_category_id');
    }

}
