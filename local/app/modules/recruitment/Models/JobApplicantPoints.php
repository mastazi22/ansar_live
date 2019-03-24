<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
            'training' => 'Training',
            'experience' => 'Experience'
        ];
    }

    public static function rulesFor()
    {
        return [
            '' => '--Select a option--',
            'physical' => 'Physical',
            'edu_training' => 'Education & Training',
            'edu_experience' => 'Education & Experience',
            'physical_age' => 'Physical & Age'
        ];
    }

    public function getRulesAttribute($value)
    {
        return json_decode($value);
    }

    public function getEducationRules()
    {
        $er = json_decode(json_encode($this->rules), true);
        if (!$this->rules || !$er[0]['edu_point']) return "--";
        $quota = $this->circular->applicantQuotaRelation;
        if($quota) $quota = $quota->pluck('quota_name_eng','id');
        $view = '<div>';
        foreach ($er as $key=>$rule){
            if (!$rule['edu_point']) {
                if($key==0)$view .= "<h5 style='border-bottom: 1px solid #cccccc'>General</h5>";
                else $view .= "<h5 style='border-bottom: 1px solid #cccccc'>{$quota[$key]}</h5>";
                $view.= '--';
                continue;
            };
            $values = array_values($rule['edu_point']);
//        return json_encode($values);
            $p = collect($values)->pluck('priority');
//        return $p;
            $educations = JobEducationInfo::select(DB::raw('GROUP_CONCAT(education_deg_bng) as education_name'), 'priority', 'id')
                ->groupBy('priority')->whereIn('priority', $p)->get();
            $data = [];
            $i = 0;
            $view = "<h5 style='border-bottom: 1px solid #cccccc'>General</h5>";
            $view .= "<table width='100%'>
        <tr>
            <th style='padding: 0 10px'>Eduction deg.</th>
            <th style='padding: 0 10px'>points</th>
        </tr>";
            foreach ($educations as $e) {
                $column = "<tr><td style='padding: 0 10px'>{$e->education_name}</td><td style='padding: 0 10px'>{$values[$i++]['point']}</td></tr>";
                $view .= $column;
            }
            $view .= "</table>";
            if ($rule['edu_p_count'] === 2) {
                $view .= "<p>Point count only descending priority</p>";
            } else if ($rule['edu_p_count'] === 1) {
                $view .= "<p>Point count only ascending priority</p>";
            } else if ($rule['edu_p_count'] === 3) {
                $view .= "<p>Sum up all point</p>";
            }
        }

        $view .= "</view>";
        return $view;

    }

    public function getHeightRules()
    {
        $er = $this->rules;
        if (!$er) return "--";
        $quota = $this->circular->applicantQuotaRelation;
        $view = "<h5 style='border-bottom: 1px solid #cccccc'>General</h5>";
        foreach (\get_object_vars($er->{'0'}) as $key=>$value){
            $view .= "<div style='padding: 5px;'>";
            $view .= \ucfirst($key).':';
            $view .= "<div style='padding: 5px;'>";
            $view .= "<div><strong>Minimum Height : </strong>{$er->{'0'}->{$key}->min_height_feet}'{$er->{'0'}->{$key}->min_height_inch}''</div>";
            $view .= "<div><strong>Minimum Point : </strong>{$er->{'0'}->{$key}->min_point}</div>";
            $view .= "<div><strong>Maximum Height : </strong>{$er->{'0'}->{$key}->max_height_feet}'{$er->{'0'}->{$key}->max_height_inch}''</div>";
            $view .= "<div><strong>Maximum Point : </strong>{$er->{'0'}->{$key}->max_point}</div>";
            $view .= "</div></div>";
        }
        if($quota){
            foreach ($quota as $q){
                if(!isset($er->{$q->id})) continue;
                foreach (\get_object_vars($er->{$q->id}) as $key=>$value) {
                    $view = "<h5 style='border-bottom: 1px solid #cccccc'>{$q->quota_name_eng}</h5>";
                    $view .= "<div style='padding: 5px;'>";
                    $view .= \ucfirst($key).':';
                    $view .= "<div style='padding: 5px;'>";
                    $view .= "<div><strong>Minimum Height : </strong>{$er->{$q->id}->{$key}->min_height_feet}'{$er->{$q->id}->{$key}->min_height_inch}''</div>";
                    $view .= "<div><strong>Minimum Point : </strong>{$er->{$q->id}->{$key}->min_point}</div>";
                    $view .= "<div><strong>Maximum Height : </strong>{$er->{$q->id}->{$key}->max_height_feet}'{$er->{$q->id}->{$key}->max_height_inch}''</div>";
                    $view .= "<div><strong>Maximum Point : </strong>{$er->{$q->id}->{$key}->max_point}</div>";
                    $view .= "</div></div>";
                }
            }
        }
        return $view;
    }
    public function getAgeRules()
    {
        $er = $this->rules;
//        return var_dump($er);
        if (!$er) return "--";
        $quota = $this->circular->applicantQuotaRelation;
        $view = "<h5 style='border-bottom: 1px solid #cccccc'>General</h5>";
        $view .= "<div><strong>Minimum Age : </strong>{$er->{'0'}->min_age_years} years</div>";
        $view .= "<div><strong>Minimum Point : </strong>{$er->{'0'}->min_age_point}</div>";
        $view .= "<div><strong>Maximum Age : </strong>{$er->{'0'}->max_age_years} years</div>";
        $view .= "<div><strong>Maximum Point : </strong>{$er->{'0'}->max_age_point}</div>";
        if($quota){
            foreach ($quota as $q){
                if(!isset($er->{$q->id})) continue;
                $view = "<h5 style='border-bottom: 1px solid #cccccc'>{$q->quota_name_eng}</h5>";
                $view .= "<div><strong>Minimum Age : </strong>{$er->{$q->id}->min_age_years} years</div>";
                $view .= "<div><strong>Minimum Point : </strong>{$er->{$q->id}->min_age_point}</div>";
                $view .= "<div><strong>Maximum Age : </strong>{$er->{$q->id}->max_age_years} years</div>";
                $view .= "<div><strong>Maximum Point : </strong>{$er->{$q->id}->max_age_point}</div>";
            }
        }
        return $view;
    }

    public function getTrainingRules()
    {
        $er = $this->rules;
        if (!$er) return "--";
        $quota = $this->circular->applicantQuotaRelation;
        $view = "<h5 style='border-bottom: 1px solid #cccccc'>General</h5>";
        $view .= "<div><strong>For VDP or TDP Training point : </strong>{$er->{'0'}->training_point}</div>";
        if($quota){
            foreach ($quota as $q){
                if(!isset($er->{$q->id})) continue;
                $view = "<h5 style='border-bottom: 1px solid #cccccc'>{$q->quota_name_eng}</h5>";
                $view .= "<div><strong>For VDP or TDP Training point : </strong>{$er->{$q->id}->training_point}</div>";
            }
        }
        return $view;
    }

    public function getExperienceRules()
    {
        $er = $this->rules;
//        return var_dump($er);
        if (!$er) return "--";
        $quota = $this->circular->applicantQuotaRelation;
        $view = "<h5 style='border-bottom: 1px solid #cccccc'>General</h5>";
        $view .= "<div><strong>Minimum Experience : </strong>{$er->{'0'}->min_experience_years} Year(s)</div>";
        $view .= "<div><strong>Minimum Point : </strong>{$er->{'0'}->min_exp_point}</div>";
        $view .= "<div><strong>Maximum Experience : </strong>{$er->{'0'}->max_experience_years} Year(s)</div>";
        $view .= "<div><strong>Maximum Point : </strong>{$er->{'0'}->max_exp_point}</div>";
        if($quota){
            foreach ($quota as $q){
                if(!isset($er->{$q->id})) continue;
                $view = "<h5 style='border-bottom: 1px solid #cccccc'>{$q->quota_name_eng}</h5>";
                $view .= "<div><strong>Minimum Experience : </strong>{$er->{$q->id}->min_experience_years} Year(s)</div>";
                $view .= "<div><strong>Minimum Point : </strong>{$er->{$q->id}->min_exp_point}</div>";
                $view .= "<div><strong>Maximum Experience : </strong>{$er->{$q->id}->max_experience_years} Year(s)</div>";
                $view .= "<div><strong>Maximum Point : </strong>{$er->{$q->id}->max_exp_point}</div>";
            }
        }
        return $view;
    }
}
