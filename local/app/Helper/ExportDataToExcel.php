<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 9/17/2017
 * Time: 12:17 PM
 */

namespace App\Helper;


use App\Jobs\ExportData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

trait ExportDataToExcel
{
    public function exportData($data,$view,$type=''){
        $export_job = Auth::user()->exportJob()->create([
            'total_file' => count($data),
            'file_completed' => 0,
            'download_url'=>'',
            'notification_url'=>""
        ]);
        $export_job->download_url =url()->route('download_file',$export_job);
        $export_job->delete_url =url()->route('delete_file',$export_job);
        $export_job->save();
        $status = [];
        for($i=0;$i<count($data);$i++){
            array_push($status,[
                'data_export_job_id'=>$export_job->id,
                'file_name'=>$i+1,
                'payload'=>gzcompress(serialize(['view'=>$view,'data'=>['type'=>$type,'ansars'=>$data[$i],'index'=>($i*2000+1)]])),
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]);
        }
        $export_job->exportStatus()->insert($status);
        return Response::json($export_job);
    }
}