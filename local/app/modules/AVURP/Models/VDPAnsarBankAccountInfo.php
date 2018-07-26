<?php

namespace App\modules\AVURP\Models;

use Illuminate\Database\Eloquent\Model;

class VDPAnsarBankAccountInfo extends Model
{

    protected $table = 'avurp_vdp_ansar_bank_account_info';
    protected $connection = 'avurp';
    protected $guarded = ['id'];

    public function vdp(){
        return $this->belongsTo(VDPAnsarInfo::class,'vdp_id');
    }
}
