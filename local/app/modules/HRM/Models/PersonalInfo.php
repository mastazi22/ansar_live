<?php

namespace App\modules\HRM\Models;

use App\models\User;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_parsonal_info';

    public function blood(){
        return $this->belongsTo(Blood::class,'blood_group_id');
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
    public function education()
    {
        return $this->hasMany(Edication::class, 'ansar_id', 'ansar_id');
    }

    public function nominee()
    {
        return $this->hasMany(Nominee::class, 'annsar_id', 'ansar_id');
    }

    public function training()
    {
        return $this->hasMany(TrainingInfo::class, 'ansar_id', 'ansar_id');
    }
    function panel(){
        return $this->hasMany(PanelModel::class,'ansar_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
    function embodiment(){
        return $this->belongsTo(EmbodimentModel::class,'ansar_id','ansar_id');
    }
    function embodiment_log(){
        return $this->hasOne(EmbodimentLogModel::class,'ansar_id', 'ansar_id');
    }
    function freezing_info(){
        return $this->hasOne(EmbodimentLogModel::class,'ansar_id', 'ansar_id');
    }
    function offer_sms_info(){
        return $this->hasOne(OfferSMS::class,'ansar_id', 'ansar_id');
    }
    function alldisease(){
        return $this->hasOne(AllDisease::class,'disease_id');
    }
    function allskill(){
        return $this->hasOne(AllSkill::class,'skill_id');
    }
}
