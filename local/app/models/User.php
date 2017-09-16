<?php

namespace App\models;

use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\ExportDataJob;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\LoggedInUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

    use Authenticatable,
        Authorizable,
        CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $connection = 'hrm';
    protected $table = 'tbl_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    function userProfile() {
        return $this->hasOne('App\models\UserProfile', 'user_id');
    }

    function userLog() {
        return $this->hasOne('App\models\UserLog', 'user_id');
    }

    function userPermission() {
        return $this->hasOne('App\models\UserPermission', 'user_id');
    }

    public function personalinfo() {
        return $this->hasMany('App\models\PersonalInfo', 'user_id');
    }
    
    public function usertype(){
        return $this->belongsTo('App\models\UserType','type','type_code');
    }
    public function hasEditVerifiedAnsarPermission(){
        $permission = $this->userPermission->permission_list;
        if(is_null($permission)){
            return true;
        }
        else{
            $a = json_decode($permission);
            if(in_array("edit_verified_ansar",$a)) return true;
            else return false;
        }
    }
    public function hasKpiVerifyPermission(){
        $permission = $this->userPermission->permission_list;
        if(is_null($permission)){
            return true;
        }
        else{
            $a = json_decode($permission);
            if(in_array("kpi_verify",$a)) return true;
            else return false;
        }
    }
    public function district(){
        return $this->belongsTo(District::class, 'district_id');
    }
    public function division(){
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function actionLog(){
        return $this->hasMany(ActionUserLog::class,'action_by');
    }
    public function logged(){
        return $this->hasOne(LoggedInUser::class,'user_id');
    }
    public function kpi(){
        return $this->hasMany(KpiGeneralModel::class,'unit_id','district_id');
    }
    public function embodiment(){
        $kpis = $this->kpi();
        $e = [];
        foreach($kpis as $kpi){
            array_push($kpi->embodiment->pluck('ansar_id'));
        }
        return json_encode($e);
    }
    public function exportJob(){
        return $this->hasOne(ExportDataJob::class,'user_id');
    }
}
