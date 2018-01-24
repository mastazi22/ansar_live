<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\AllDisease;
use App\modules\HRM\Models\AllSkill;
use App\modules\HRM\Models\Blood;
use App\modules\HRM\Models\Designation;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobApplicantHRMDetails extends Model
{
    //
    protected $table = 'job_applicant_hrm_details';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function applicant(){
        return $this->belongsTo(JobAppliciant::class,'applicant_id','applicant_id');
    }
    public function getApplicantNomineeInfoAttribute($value){
        return json_decode($value);

    }
    public function getApplicantTrainingInfoAttribute($value){
        return json_decode($value);

    }
    public function getAppliciantEducationInfoAttribute($value){
        return json_decode($value);

    }
    public function setDataOfBirthAttribute($value){
        $this->attributes['data_of_birth'] =  Carbon::parse($value)->format('Y-m-d');

    }
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function district(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }
    public function skill(){
        return $this->belongsTo(AllSkill::class,'skill_id');
    }
    public function designation(){
        return $this->belongsTo(Designation::class,'designation_id');
    }
    public function disease(){
        return $this->belongsTo(AllDisease::class,'disease_id');
    }
    public function bloodGroup(){
        return $this->belongsTo(Blood::class,'blood_group_id');
    }
}
