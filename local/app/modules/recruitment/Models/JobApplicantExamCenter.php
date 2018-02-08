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
    public function units(){
        return $this->belongsToMany(District::class,'ansar_recruitment.job_applicant_exam_center_units','exam_center_id','unit_id');
    }
    public function examUnits(){
        return $this->hasMany(JobApplicantExamCenterUnits::class,'exam_center_id');
    }
    public function setSelectionDateAttribute($value){
//        return $units;
        $this->attributes['selection_date'] = Carbon::parse($value)->format('Y-m-d');
    }
    public function getSelectionDateAttribute($value){
        return Carbon::parse($value)->format('d-M-Y');
    }
    /*public function getUnitsAttribute($value){
        $ids = explode(',',$value);
        $units = District::whereIn('id',$ids)->pluck('unit_name_bng');
//        return $units;
        return implode(',',collect($units)->toArray());
    }
    public function setUnitsAttribute($value){
        $this->attributes['units'] = implode(',',$value);
    }*/
    public function setWrittenVivaDateAttribute($value){
        $this->attributes['written_viva_date'] = Carbon::parse($value)->format('Y-m-d');
    }
    public function getWrittenVivaDateAttribute($value){
        return Carbon::parse($value)->format('d-M-Y');
    }

    public static function rules(){
        $rules = [
            'job_circular_id' => 'required|numeric|exists:job_circular,id',
            'selection_date' => 'required',
            'selection_time' => ['required','regex:/^(0[0-9]|1[0-2]):([0-5][0-9])\s?(AM|PM)$/'],
            'selection_place' => 'required',
            'written_viva_date' => 'required',
            'written_viva_time' => ['required','regex:/^(0[0-9]|1[0-2]):([0-5][0-9])\s?(AM|PM)$/'],
            'written_viva_place' => 'required',
            'units' => 'required',
        ];
        return $rules;
    }
}
