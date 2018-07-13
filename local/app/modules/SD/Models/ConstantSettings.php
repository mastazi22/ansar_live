<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Model;

class ConstantSettings extends Model
{

    protected $connection = 'sd';
    protected $table = 'tbl_constant_settings';
    protected $guarded = ['id'];
}
