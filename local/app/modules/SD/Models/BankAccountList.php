<?php

namespace App\modules\SD\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccountList extends Model
{

    protected $connection = 'sd';
    protected $table = 'ansar_sd.tbl_bank_account_info';
    protected $guarded = ['id'];


    public static function getAccount($type){
        $b= BankAccountList::where('account_for',$type)->first();
        if($b){
            return $b->account_no;
        }
        return 'n\a';
    }
}
