<?php

namespace App\modules\AVURP\Models;

use App\models\User;
use App\modules\HRM\Models\Blood;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Unions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserActionLog extends Model
{
    //
    protected $table = "avurp_user_action_log";
    protected $connection = "avurp";
    protected $guarded = ['id'];

}
