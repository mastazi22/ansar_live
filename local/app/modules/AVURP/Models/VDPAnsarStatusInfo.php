<?php

namespace App\modules\AVURP\Models;

use Illuminate\Database\Eloquent\Model;

class VDPAnsarStatusInfo extends Model
{

    protected $table = 'avurp_vdp_ansar_status';
    protected $connection = 'avurp';
    protected $guarded = ['id'];
}
