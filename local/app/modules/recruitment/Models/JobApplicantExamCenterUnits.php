<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class JobApplicantExamCenterUnits extends Model
{
    //
    protected $table='job_applicant_exam_center_units';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];
}
