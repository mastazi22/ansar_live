<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarBankAccountInfoDetails extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_bank_account_info';
    protected $guarded = ['id'];
    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
}
