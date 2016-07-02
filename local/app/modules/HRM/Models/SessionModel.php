<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class SessionModel extends Model
{
    protected $connection = 'hrm';
    protected $table = "tbl_sesson";

}
