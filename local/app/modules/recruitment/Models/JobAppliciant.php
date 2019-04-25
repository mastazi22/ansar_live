<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\Thana;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
class JobAppliciant extends Model
{
    //
    protected $table = 'job_applicant';
    protected $connection = 'recruitment';
    protected $guarded = ['id', 'job_circular_id'];

    public function circular()
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }
    public function ansar()
    {
        return $this->belongsTo(PersonalInfo::class, 'ansar_id','ansar_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
    public function govQuota()
    {
        return $this->hasOne(JobGovQuota::class, 'job_applicant_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'unit_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
    }

    public function appliciantEducationInfo()
    {
        return $this->hasMany(JobAppliciantEducationInfo::class, 'job_applicant_id');
    }

    public function payment()
    {
        return $this->hasOne(JobAppliciantPaymentHistory::class, 'job_appliciant_id', 'applicant_id');
    }

    public function quota()
    {
        return $this->belongsTo(JobApplicantQuota::class, 'unit_id', 'district_id');
    }

    public function getDateOfBirthAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('d-M-Y');
        }
        return null;
    }
	public function getProfilePicAttribute($value){
		try{
			$path = storage_path("receruitment");
			if(!file_exists($path)){
				if(mkdir($path)){
					$file_parts = explode("/",$value);
					$file_name = explode(".",$file_parts[count($file_parts)-1])[0].".jpg";
					Image::make($value)->resize(250,250)->encode("jpg")->save($path."/".$file_name);
					return $path."/".$file_name;
				}
				return $value;

			}else{
				$file_parts = explode("/",$value);
				$file_name = explode(".",$file_parts[count($file_parts)-1])[0].".jpg";
				if(file_exists($path."/".$file_name)) return $path."/".$file_name;
				Image::make($value)->resize(250,250)->encode("jpg")->save($path."/".$file_name);
				return $path."/".$file_name;
			}
			return $value;
		}catch(\Exception $e){
            return false;
        }
    }
    public function getColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function editHistory()
    {
        return $this->hasMany(JobApplicantEditHistory::class, 'applicant_id');
    }

    public function selectedApplicant()
    {
        return $this->hasOne(JobSelectedApplicant::class, 'applicant_id', 'applicant_id');
    }

    public function accepted()
    {
        return $this->hasOne(JobAcceptedApplicant::class, 'applicant_id', 'applicant_id');
    }
    public function rejected()
    {
        return $this->hasOne(JobRejectedApplicant::class, 'applicant_id', 'applicant_id');
    }

    public function marks()
    {
        return $this->hasOne(JobApplicantMarks::class, 'applicant_id', 'applicant_id');
    }

    public function hrmDetail()
    {
        return $this->hasOne(JobApplicantHRMDetails::class, 'applicant_id', 'applicant_id');
    }
    public function hrmDetailTrashed()
    {
        return $this->hrmDetail()->onlyTrashed();
    }

    public function education()
    {
        return $this->belongsToMany(JobEducationInfo::class, 'job_appliciant_education_info', 'job_applicant_id', 'job_education_id');
    }

    public function physicalPoint()
    {
        if ($this->status != 'selected') {
            return -1;
        }
        if (!$this->circular->point()->exists() || !$this->circular->point()->where('point_for', 'physical')->exists()) {
            return "--";
        }
        $rules = $this->circular->point()->where('point_for', 'physical')->where('rule_name', 'height')->first()->rules;
        return $this->heightPoint($rules);
    }

    public function educationTrainingPoint()
    {
        if ($this->status != 'selected') {
            return -1;
        }
        if (!$this->circular->point()->exists() || !$this->circular->point()->where('point_for', 'edu_training')->exists()) {
            return "--";
        }
        $rules = $this->circular->point()->where('point_for', 'edu_training')->get();
        $edu_point = 0;
        foreach ($rules as $rule) {
            if ($rule->rule_name === 'education') {
                $edu_point += $this->eduPoint(json_decode(json_encode($rule->rules), true));
            } else if ($rule->rule_name === 'training'&&($this->training_info=="VDP training"||$this->training_info=="TDP training")) {
                $edu_point += floatval($rule->rules->training_point);
            }
        }
        /*$point_table = [
          4=>10,
          7=>8,
          8=>6,
          9=>4,
          10=>2
        ];
        $order = 'desc';
        $education_priority = $this->education()->orderBy('priority',$order)->first()['priority'];
//        $point_table[$education_priority] = 0;
        $training_point = $this->training_info=='VDP training'||$this->training_info=='TDP training'?5:0;*/
        return $edu_point;
    }

    public function educationExperiencePoint()
    {
        if ($this->status != 'selected') {
            return -1;
        }
        if (!$this->circular->point()->exists() || !$this->circular->point()->where('point_for', 'edu_experience')->exists()) {
            return "--";
        }
        $rules = $this->circular->point()->where('point_for', 'edu_experience')->get();
        $expRule = collect($rules)->where('rule_name', 'experience')->first();
        $eduRule = collect($rules)->where('rule_name', 'education')->first();
        $eduPoint = $point = 0;
        if ($eduRule) {
            $eduPoint = $this->eduPoint(json_decode(json_encode($eduRule->rules), true));
        }
        if ($expRule) {
            $exp = $this->expCalculation();
            $min_exp_years = floatval($expRule->rules->min_experience_years);
            $max_exp_years = floatval($expRule->rules->max_experience_years);
            $min_point = floatval($expRule->rules->min_exp_point);
            $max_point = floatval($expRule->rules->max_exp_point);
            $delta_height = $max_exp_years - $min_exp_years;
            $delta_point = $max_point - $min_point;
            if ($exp >= $max_exp_years) $point = $max_point;
            else $point = number_format(($delta_point / $delta_height) * (($exp - $min_exp_years)) + $min_point, 2);
        }
        return $point + $eduPoint < 0 ? 0 : $point + $eduPoint;
    }
    public function physicalAgePoint()
    {
        if ($this->status != 'selected') {
            return -1;
        }
        if (!$this->circular->point()->exists() || !$this->circular->point()->where('point_for', 'physical_age')->exists()) {
            return "--";
        }
        $rules = $this->circular->point()->where('point_for', 'physical_age')->get();
        $ageRule = collect($rules)->where('rule_name', 'age')->first();
        $physicalRule = collect($rules)->where('rule_name', 'height')->first();
        $physical = $point = 0;
        if ($physicalRule) {
            $physical = $this->heightPoint($physicalRule->rules);
        }
        if ($ageRule) {
            $age = $this->yearDiff($this->date_of_birth,Carbon::now()->format('Y-m-d'));
            $min_age_years = floatval($ageRule->rules->min_age_years);
            $max_age_years = floatval($ageRule->rules->max_age_years);
            $min_point = floatval($ageRule->rules->min_age_point);
            $max_point = floatval($ageRule->rules->max_age_point);
            $delta_age = $max_age_years - $min_age_years;
            $delta_point = $max_point - $min_point;
            if ($age >= $max_age_years) $point = $max_point;
            else $point = number_format(($delta_point / $delta_age) * (($age - $min_age_years)) + $min_point, 2);
        }
        return $point + $physical < 0 ? 0 : $point + $physical;
    }

    private function eduPoint($rules)
    {
        $point_table = array_values($rules['edu_point']);
        $epp = intval($rules['edu_p_count']);
        Log::info($this->education);
        if ($epp === 1) {
            $education_priority = $this->education()->orderBy('priority', 'asc')->first()['priority'];
            Log::info("education_priority".$education_priority);
            $key = array_search($education_priority, array_column($point_table, 'priority'));
            $point = intval($point_table[$key]['point']);
        } else if ($epp === 2) {

            $education_priority = $this->education()->orderBy('priority', 'desc')->first()['priority'];
            Log::info("education_priority".$education_priority);
            $key = array_search($education_priority, array_column($point_table, 'priority'));
            $point = intval($point_table[$key]['point']);
        } else {
            $p = collect($point_table)->pluck('priority');
            $education_priority = $this->education()->whereIn('priority', $p)->pluck('priority');
            $point = collect($point_table)->whereIn('priority', $education_priority)->sum('point');
        }
        Log::info("edu_point".$point);
        Log::info($point_table);
        return $point;

    }
    private function heightPoint($rules){
        $min_height = floatval($rules->min_height_feet) * 12 + floatval($rules->min_height_inch);
        $max_height = floatval($rules->max_height_feet) * 12 + floatval($rules->max_height_inch);
        $min_point = floatval($rules->min_point);
        $max_point = floatval($rules->max_point);
        $total_height = floatval($this->height_feet) * 12 + floatval($this->height_inch);
        $delta_height = $max_height - $min_height;
        $delta_point = $max_point - $min_point;
        if ($total_height >= $max_height) return $max_point;
//        return number_format(($delta_point / $delta_height) * (($total_height - $min_height)) + $min_point,2);
        return ($delta_point / $delta_height) * (($total_height - $min_height)) + $min_point;

    }
    public function expCalculation()
    {
        if (!$this->ansar_id) return 0;
        $currentExp = $prevExp = 0;
        $currentEmbodiment = EmbodimentModel::where("ansar_id", $this->ansar_id)->first();
        if ($currentEmbodiment) {
            $currentExp = $this->yearDiff(date('Y-m-d'), $currentEmbodiment->joining_date);
        }
        $embodimentHistory = EmbodimentLogModel::where("ansar_id", $this->ansar_id)->get();
        if ($embodimentHistory->count() > 0) {
            foreach ($embodimentHistory as $history) {
                $prevExp += $this->yearDiff($history->joining_date, $history->release_date);
            }
        }
        return $currentExp + $prevExp;
    }

    private function yearDiff($d1, $d2)
    {
        $d1 = new \DateTime($d1);
        $d2 = new \DateTime($d2);
        return $d1->diff($d2)->y;
    }
}