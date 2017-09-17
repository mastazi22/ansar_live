<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class ExportDataJob extends Model
{
    //
    protected $table = 'tbl_data_export_job';
    protected $fillable = ['total_file','file_completed'];
    public function exportStatus(){
        return $this->hasMany(DataExportStatus::class,'data_export_job_id');
    }
}
