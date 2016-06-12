<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    protected $connection = 'hrm';
    protected  $table = 'tbl_ansar_parsonal_info';
  
//    public function blood(){
//        return $this->belongsTo('App\models\Blood','blood_group_id');
//    }
//    public function division(){
//        return $this->belongsTo('App\models\Division','division_id');
//    }
//    public function district(){
//        return $this->belongsTo('App\models\District','unit_id');
//    }
//    public function thana(){
//        return $this->belongsTo('App\models\Thana','thana_id');
//    }
//    public function education(){
//        return $this->hasMany('App\models\Edication','ansar_id','ansar_id');
//    }
//    public function nominee(){
//        return $this->hasMany('App\models\Nominee','annsar_id','ansar_id');
//    }
//    public function training(){
//        return $this->hasMany('App\models\TrainingInfo','ansar_id','ansar_id');
//    }
//    function panel(){
//        return $this->hasMany('App\models\PanelModel','ansar_id');
//    }
//    public function user(){
//        return $this->belongsTo('App\models\User','user_id');
//    }
//    public function designation(){
//        return $this->belongsTo('App\models\Designation','designation_id');
//    }
//    function embodiment(){
//        return $this->belongsTo('App\models\EmbodimentModel','ansar_id','ansar_id');
//    }
//    function embodiment_log(){
//        return $this->hasOne('App\models\EmbodimentLogModel','ansar_id', 'ansar_id');
//    }
//    function freezing_info(){
//        return $this->hasOne('App\models\EmbodimentLogModel','ansar_id', 'ansar_id');
//    }
//    function offer_sms_info(){
//        return $this->hasOne('App\models\OfferSMS','ansar_id', 'ansar_id');
//    }
//    function alldisease(){
//        return $this->hasOne('App\models\AllDisease','disease_id');
//    }
//    function allskill(){
//        return $this->hasOne('App\models\AllSkill','skill_id');
//    }
}
