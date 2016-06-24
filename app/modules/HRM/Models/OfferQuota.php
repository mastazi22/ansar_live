<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class OfferQuota extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_offer_quota';
    protected $fillable = ['quota'];
}
