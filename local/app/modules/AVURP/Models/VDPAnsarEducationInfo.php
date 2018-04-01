<?php

namespace App\modules\AVURP\Models;

use App\modules\HRM\Models\AllEducationName;
use Illuminate\Database\Eloquent\Model;

class VDPAnsarEducationInfo extends Model
{
    //
    protected $table = "avurp_ansar_vdp_education_info";
    protected $connection = "avurp";
    protected $guarded = ['id'];

    public function education()
    {
        return $this->belongsTo(AllEducationName::class, 'education_id');
    }
}
