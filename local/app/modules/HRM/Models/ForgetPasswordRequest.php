<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class ForgetPasswordRequest extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_forget_password_request';
}
