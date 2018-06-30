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
        if (!$this->rules || !$er['edu_point']) return "--";
        $values = array_values($er['edu_point']);
//        return json_encode($values);
        $p = collect($values)->pluck('priority');
//        return $p;
        $educations = JobEducationInfo::select(DB::raw('GROUP_CONCAT(education_deg_bng) as education_name'), 'priority', 'id')
            ->groupBy('priority')->whereIn('priority', $p)->get();
        $data = [];
        $i = 0;
        $view = "<table width='100%'>
        <tr>
            <th style='padding: 0 10px'>Eduction deg.</th>
            <th style='padding: 0 10px'>points</th>
        </tr>";
        foreach ($educations as $e) {
            $column = "<tr><td style='padding: 0 10px'>{$e->education_name}</td><td style='padding: 0 10px'>{$values[$i++]['point']}</td></tr>";
            $view .= $column;
        }
        $view .= "</table>";
        if ($er['edu_p_count'] === 2) {
            $view .= "<p>Point count only descending priority</p>";
        } else if ($er['edu_p_count'] === 1) {
            $view .= "<p>Point count only ascending priority</p>";
        } else if ($er['edu_p_count'] === 3) {
            $view .= "<p>Sum up all point</p>";
        }
        return $view;

    }

    public function getHeightRules()
    {
        $er = $this->rules;
        if (!$this->rules || !$er) return "--";

        $view = "<div><strong>Minimum Height : </strong>{$er->min_height_feet}'{$er->min_height_inch}''</div>";
        $view .= "<div><strong>Minimum Point : </strong>{$er->min_point}</div>";
        $view .= "<div><strong>Maximum Height : </strong>{$er->max_height_feet}'{$er->max_height_inch}''</div>";
        $view .= "<div><strong>Maximum Point : </strong>{$er->max_point}</div>";
        return $view;
    }
    public function getAgeRules()
    {
        $er = $this->rules;
//        return var_dump($er);
        if (!$this->rules || !$er) return "--";

        $view = "<div><strong>Minimum Age : </strong>{$er->min_age_years} years</div>";
        $view .= "<div><strong>Minimum Point : </strong>{$er->min_age_point}</div>";
        $view .= "<div><strong>Maximum Age : </strong>{$er->max_age_years} years</div>";
        $view .= "<div><strong>Maximum Point : </strong>{$er->max_age_point}</div>";
        return $view;
    }

    public function getTrainingRules()
    {
        if (!$this->rules || !$this->rules->training_point) return "--";
        $view = "<div><strong>For VDP or TDP Training point : </strong>{$this->rules->training_point}</div>";
        return $view;
    }

    public function getExperienceRules()
    {
        $er = $this->rules;
//        return var_dump($er);
        if (!$this->rules || !$er) return "--";
        $view = "<div><strong>Minimum Experience : </strong>{$er->min_experience_years} Year(s)</div>";
        $view .= "<div><strong>Minimum Point : </strong>{$er->min_exp_point}</div>";
        $view .= "<div><strong>Maximum Experience : </strong>{$er->max_experience_years} Year(s)</div>";
        $view .= "<div><strong>Maximum Point : </strong>{$er->max_exp_point}</div>";
        return $view;
    }
}
