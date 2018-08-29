<?php

namespace App\models;

use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\ExportDataJob;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\LoggedInUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class TempUser extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

    use Authenticatable,
        Authorizable,
        CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $connection = 'hrm';
    protected $table = 'tbl_temp_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_name', 'password','return_verification_code'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'return_verification_code','verification_code'];


}
