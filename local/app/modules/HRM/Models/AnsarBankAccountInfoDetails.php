<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarBankAccountInfoDetails extends Model
{
    protected $connection = 'hrm';
    protected $table = 'db_amis.tbl_ansar_bank_account_info';
    protected $guarded = ['id'];
    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
    public function getAccountNo(){
        $choice = $this->prefer_choice;
        if($choice=='general'){
            return $this->account_no;
        } else{
            return $this->mobile_bank_account_no;
        }
    }
    public function getBankName(){
        $choice = $this->prefer_choice;
        if($choice=='general'){
            return "DBBL";
        } else{
            return $this->mobile_bank_type;
        }
    }
}
