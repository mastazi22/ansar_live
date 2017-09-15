<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class DataExportStatus extends Model
{
    //
    protected $table = 'tbl_data_export_status';
    protected $fillable = ['status','file_name','user_id','total_part','counter'];
    public $connection = 'hrm';
}
