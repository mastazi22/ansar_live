<?php

namespace App\modules\HRM\Models;

use App\Helper\Facades\UserPermissionFacades;
use App\models\User;
use App\modules\SD\Models\Leave;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class PersonalInfo extends Model
{
    protected $connection = 'hrm';
    protected $table = 'db_amis.tbl_ansar_parsonal_info';
    protected $guarded = ['id'];

    public function status()
    {
      return $this->hasOne(AnsarStatusInfo::class,'ansar_id','ansar_id');
    }

    public function blood()
    {
        return $this->belongsTo(Blood::class, 'blood_group_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'unit_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
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

    function panel()
    {
        return $this->hasOne(PanelModel::class, 'ansar_id','ansar_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    function embodiment()
    {
        return $this->belongsTo(EmbodimentModel::class, 'ansar_id', 'ansar_id');
    }

    function embodiment_log()
    {
        return $this->hasOne(EmbodimentLogModel::class, 'ansar_id', 'ansar_id');
    }

    function freezing_info()
    {
        return $this->hasOne(FreezingInfoModel::class, 'ansar_id', 'ansar_id');
    }
    function freezing_info_log()
    {
        return $this->hasOne(FreezingInfoLog::class, 'ansar_id', 'ansar_id');
    }

    function offer_sms_info()
    {
        return $this->hasOne(OfferSMS::class, 'ansar_id', 'ansar_id');
    }

    function alldisease()
    {
        return $this->belongsTo(AllDisease::class, 'disease_id');
    }

    function allskill()
    {
        return $this->belongsTo(AllSkill::class, 'skill_id');
    }
    function receiveSMS(){
        return $this->hasOne(ReceiveSMSModel::class,'ansar_id','ansar_id');
    }
    function panelLog(){
        return $this->hasMany(PanelInfoLogModel::class,'ansar_id','ansar_id');
    }
    function offerCancel(){

        return $this->hasMany(OfferCancel::class,'ansar_id','ansar_id');

    }
    function offerLog(){
        return $this->hasMany(OfferSmsLog::class,'ansar_id','ansar_id');
    }
    function rest(){
        return $this->hasOne(RestInfoModel::class,'ansar_id','ansar_id');
    }

    function getMobileNoSelfAttribute($value){

        return UserPermissionFacades::userPermissionExists('view_mobile_no')||UserPermissionFacades::isAnsarEmbodied($this->ansar_id)?$value:"";
    }
    function getMobileNoRequestAttribute($value){

        return UserPermissionFacades::userPermissionExists('view_mobile_no')||UserPermissionFacades::isAnsarEmbodied($this->ansar_id)?$value:"";
    }
    function getMobileNo(){

        return $this->mobile_no_self;
    }
    function account(){
        return $this->hasOne(AnsarBankAccountInfoDetails::class,'ansar_id','ansar_id');
    }
    public function getExperience()
    {
        if (!$this->ansar_id) return 0;
        $currentExp = $prevExp = 0;
        $currentEmbodiment = EmbodimentModel::where("ansar_id", $this->ansar_id)->first();
        if ($currentEmbodiment) {
            $currentExp = Carbon::parse($currentEmbodiment->joining_date)->diffInYears(Carbon::now(),true);
        }
        $embodimentHistory = EmbodimentLogModel::where("ansar_id", $this->ansar_id)->get();
        if ($embodimentHistory->count() > 0) {
            foreach ($embodimentHistory as $history) {
                $prevExp += Carbon::parse($history->joining_date)->diffInYears(Carbon::parse($history->release_date),true);;
            }
        }
        return $currentExp + $prevExp;
    }
    public function calculateAge(){
        $last_date_of_month = Carbon::now()->daysInMonth;
        $current_date = Carbon::now()->day($last_date_of_month);
        return Carbon::parse($this->data_of_birth)->diff($current_date)->format("%yy %mm %dd");
    }
    public function leave(){
        return $this->hasMany(Leave::class,'ansar_id','ansar_id');
    }
}
