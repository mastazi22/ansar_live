<?php

namespace App\modules\SD\Models;

use App\models\User;
use App\modules\HRM\Models\KpiGeneralModel;
use Illuminate\Database\Eloquent\Model;

class UserActionLog extends Model
{

    protected $connection = 'sd';
    protected $table = 'tbl_action_log';
    protected $guarded = ['id'];
    public function user(){
        return $this->belongsTo(User::class,'action_user_Id');
    }
}
