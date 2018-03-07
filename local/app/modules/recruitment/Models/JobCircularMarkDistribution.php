<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobCircularMarkDistribution extends Model
{
    //
    protected $table = 'job_circular_mark_distribution';
    protected $guarded = ['id','job_circular_id'];
    protected $connection = 'recruitment';
    public function circular(){
        return $this->belongsTo(JobCircular::class,'job_circular_id');
    }
    public static function rules($id=''){
        if($id){
            return [
                'job_circular_id'=>'required|unique:recruitment.job_circular_mark_distribution,job_circular_id,'.$id,
                'physical'=>'required|numeric',
                'edu_training'=>'required|numeric',
                'written'=>'required|numeric',
                'viva'=>'required|numeric'
            ];
        } else{
            return [
                'job_circular_id'=>'required|unique:recruitment.job_circular_mark_distribution',
                'physical'=>'required|numeric',
                'edu_training'=>'required|numeric',
                'written'=>'required|numeric',
                'viva'=>'required|numeric'
            ];
        }
    }
}
