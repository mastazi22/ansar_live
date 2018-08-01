<?php

namespace App\modules\AVURP\Models;

use App\models\User;
use App\modules\HRM\Models\Blood;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Unions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VDPAnsarInfo extends Model
{
    //
    protected $table = "avurp_vdp_ansar_info";
    protected $connection = "avurp";
    protected $guarded = ['id'];

    public function account(){
        return $this->hasOne(VDPAnsarBankAccountInfo::class,'vdp_id');
    }
    public function status(){
        return $this->hasOne(VDPAnsarStatusInfo::class,'vdp_ansar_info_id');
    }
    public function offer(){
        return $this->hasOne(OfferInfo::class,'vdp_id');
    }
    public function embodiment(){
        return $this->hasOne(Embodiment::class,'vdp_id');
    }
    public function bankInfo(){
        return $this->hasOne(VDPAnsarBankAccountInfo::class,'vdp_id');
    }
    public function education(){
        return $this->hasMany(VDPAnsarEducationInfo::class,'vdp_ansar_id');
    }
    public function training_info(){
        return $this->hasMany(VDPAnsarTrainingInfo::class,'vdp_ansar_info_id');
    }
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }
    public function union(){
        return $this->belongsTo(Unions::class,'union_id');
    }
    public function bloodGroup(){
        return $this->belongsTo(Blood::class,'blood_group_id');
    }
    public function setDateOfBirthAttribute($value){
        $this->attributes['date_of_birth'] = $value?Carbon::parse($value)->format('Y-m-d'):'';
    }
    public function getDateOfBirthAttribute($value){
        if($value){
            return Carbon::parse($value)->format('d-M-Y');
        }
        return '';
    }
    public function log(){
        return $this->hasMany(UserActionLog::class,'action_id','id');
    }
    public function scopeUserQuery($query,$id){
        if($id){
            $user = User::find($id);
            if($user->usertype->type_name=="Dataentry"){
                return $query->whereHas('log',function ($q) use ($user){
                    $q->where('action_user_id',$user->id);
                    $q->where('action_type','Entry');
                });
            }
        }
        return $query;
    }
    public function scopeSearchQuery($query,$search){
        if($search){
            return $query->where(function ($q) use ($search){
                $q->where('ansar_name_bng','like',"%$search%");
                $q->orWhere('ansar_name_eng','like',"%$search%");
                $q->orWhere('father_name_bng','like',"%$search%");
                $q->orWhere('mother_name_bng','like',"%$search%");
                $q->orWhere('designation','like',"%$search%");
                $q->orWhere('marital_status','like',"%$search%");
                $q->orWhere('marital_status','like',"%$search%");
                $q->orWhere('national_id_no','like',"%$search%");
                $q->orWhere('mobile_no_self','like',"%$search%");
                $q->orWhere('geo_id','=',"$search");
                if(strtotime($search)){
                    $d = Carbon::parse($search)->format('Y-m-d');
                    $q->orWhere('date_of_birth',$d);
                }
            });
        }
        return $query;
    }
    public function scopeSearchQueryForOffer($query,$filters){
        foreach ($filters as $key=>$value){
            if(!is_array($value))$filter = json_decode($value,true);
            else $filter = $value;
            if($key=="height"&&isset($filter["value"])){
                $total_height = 0;
                if(isset($filter["value"]["feet"])&&$filter["value"]["feet"]) $total_height += floatval($filter["value"]["feet"])*12;
                if(isset($filter["value"]["inch"])&&$filter["value"]["inch"]) $total_height += floatval($filter["value"]["inch"]);
                if($total_height>0)$query->where(DB::raw("height_feet*12+height_inch"),$filter['comparator'],$total_height);
            } else if($key=="age"&&isset($filter["value"])){
                $age = -1;
                if($filter["value"]) $age = floatval($filter["value"]);
                if($age>=0)$query->where(DB::raw("TIMESTAMPDIFF(YEAR,date_of_birth,CURDATE())"),$filter['comparator'],$age);
            }
            else if($key=="units"){
                $units = isset($filter['value'])?array_filter(array_values($filter['value'])):[];
                $user = auth()->user();
                if($user->type==22){
                    if(!in_array($user->district_id,$units)){
                        array_push($units,$user->district_id);
                    }
                } else if($user->type==66){
                    $userUnit = District::where('division_id',$user->division_id)->pluck('id');
                    $units = array_merge($units,$userUnit);
                }
                if(count($units)>0)$query->whereIn('unit_id',$units);
            }
        }

        return $query;
    }
    public function age(){

        $now = Carbon::now();
        $age = false;

        if($this->date_of_birth&&$this->date_of_birth!="0000-00-00"){
            try {
                $d = Carbon::parse($this->date_of_birth);
                if(!checkdate($d->month,$d->day,$d->year)) throw new \Exception("invalid date");
                $age = Carbon::parse($this->date_of_birth)->diffInYears($now, true);
            }catch (\Exception $e){
                $age = false;
            }
        }
        return $age===false?"Invalid date of birth":$age;

    }
}
