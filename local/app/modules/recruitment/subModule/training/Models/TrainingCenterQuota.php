<?php

namespace App\modules\recruitment\subModule\training\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use Illuminate\Database\Eloquent\Model;

class TrainingCenterQuota extends Model
{
    protected $table = "training_center_quota";
    protected $guarded = ["id"];
    protected $connection = "recruitment.training";

    public function center(){
        return $this->belongsTo(TrainingCenter::class,'center_id');
    }
}
