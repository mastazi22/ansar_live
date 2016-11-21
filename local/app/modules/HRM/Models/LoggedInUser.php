<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class LoggedInUser extends Model
{
    //
    protected $table = 'tbl_logged_in_user';
    protected $connection = 'hrm';
    protected $guarded = [];
}
