<?php

namespace App\modules\HRM\Models;

use App\models\User;
use App\modules\recruitment\Models\JobApplicantQuota;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $connection = 'hrm';
    protected $table = 'db_amis.tbl_units';
    protected $guarded = [];

    function kpi()
    {
        return $this->hasMany('App\modules\HRM\Models\KpiGeneralModel', 'unit_id', 'unit_id');
    }

    public function personalinfo()
    {
        return $this->hasOne('App\modules\HRM\Models\PersonalInfo', 'unit_id');
    }
//    public function division(){
//        return $this->belongsTo('App\modules\HRM\Models\Division', 'division_id');
//    }
//    public function thana(){
//        return $this->hasMany('App\modules\HRM\Models\Thana', 'unit_id', 'unit_id');
//    }
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
    public function applicantQuota(){
        return $this->hasOne(JobApplicantQuota::class,'district_id');
    }
    public function dc(){
        return $this->hasOne(User::class,'district_id')->where('type',22);
    }
}
