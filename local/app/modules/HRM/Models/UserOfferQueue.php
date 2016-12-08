<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class UserOfferQueue extends Model
{
    //
    protected $table = 'tbl_offer_queue';
    protected $guarded =[];
    protected $connection = 'hrm';
}
