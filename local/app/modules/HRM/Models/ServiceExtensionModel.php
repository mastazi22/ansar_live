<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceExtensionModel extends Model
{
   protected $connection = 'hrm';
   protected $table="tbl_service_extension";
}
