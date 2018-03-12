<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicantPoints extends Model
{
    //
    protected $table = 'job_applicant_points';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';

    public function circular()
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }

    public static function rulesName()
    {
        return [
            '' => '--Select a rule--',
            'height' => 'Height',
            'age' => 'Age',
            'education' => 'Education',
            'training' => 'Training'
        ];
    }

    public static function rulesFor()
    {
        return [
            '' => '--Select a option--',
            'physical' => 'Physical',
            'edu_training' => 'Education & Training'
        ];
    }
    public function getRulesAttribute($value){
        return json_decode($value);
    }
    public function getEducationRules(){
        $er = json_decode(json_encode($this->rules),true);
        $values = array_values($er);
        $p = collect($values)->pluck('priority');
        $educations = JobEducationInfo::select(DB::raw('GROUP_CONCAT(education_deg_bng) as education_name'),'priority','id')
            ->groupBy('priority')->whereIn('priority',$p)->get();
        $data = [];
        $i=0;
        foreach ($educations as $e){
            array_push($data,['education_name'=>$e->education_name,'point'=>$values[$i++]['point']]);
        }
    }
}
