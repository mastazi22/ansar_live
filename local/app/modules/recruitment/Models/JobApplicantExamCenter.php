<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class JobApplicantExamCenter extends Model
{
    //
    protected $table='job_applicant_exam_center';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];
    public function circular(){
        return $this->belongsTo(JobCircular::class,'job_circular_id');
    }
    public function getSelectionUnitsAttribute($value){
        $ids = explode(',',$value);
        $units = District::whereIn('id',$ids)->pluck('unit_name_bng');
//        return $units;
        return implode(',',collect($units)->toArray());
    }
    public function setSelectionUnitsAttribute($value){
//        return $units;
        $this->attributes['selection_units'] = implode(',',$value);
    }
    public function setSelectionDateAttribute($value){
//        return $units;
        $this->attributes['selection_date'] = Carbon::parse($value)->format('Y-m-d');
    }
    public function getWrittenVivaUnitsAttribute($value){
        $ids = explode(',',$value);
        $units = District::whereIn('id',$ids)->pluck('unit_name_bng');
//        return $units;
        return implode(',',collect($units)->toArray());
    }
    public function setWrittenVivaUnitsAttribute($value){
        $this->attributes['written_viva_units'] = implode(',',$value);
    }
    public function setWrittenVivaDateAttribute($value){
        $this->attributes['written_viva_date'] = Carbon::parse($value)->format('Y-m-d');
    }
    public function getWrittenVivaDateAttribute($value){
        return Carbon::parse($value)->format('d-M-Y');
    }
    public function getSelectionDateAttribute($value){
        return Carbon::parse($value)->format('d-M-Y');
    }
}
