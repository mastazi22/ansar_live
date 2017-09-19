<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\ExportDataJob;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class DownloadController extends Controller
{
    //
    public function downloadFile(ExportDataJob $dataJob){

        $path = storage_path('export_file');
        if($dataJob->total_file==1){

            $status = $dataJob->exportStatus()->first();
            if($status){
                $file_name = $path.'/'.$status->file_name.'.xls';
                if(File::exists($file_name)){
                    return response()->download($file_name);
                }
                return redirect()->back()->with('error','File does not exists or deleted');
            }
            return redirect()->back()->with('error','File does not exists or deleted');

        }
        else{

            $status = $dataJob->exportStatus;
            $files = [];
            foreach ($status as $s){

                $file_name = $path.'/'.$s->file_name.'.xls';
                if(File::exists($file_name)){
                    array_push($files,$file_name);
                }

            }
            $des = $path.'/export.zip';
            $zip = new \ZipArchive();
            if($zip->open($des,\ZipArchive::CREATE)){

                for ($i=0;$i<count($files);$i++){

                    $zip->addFile($files[$i],($i+1).'');

                }
                $zip->close();
                return response()->download($des)->deleteFileAfterSend(true);

            }
            else{
                return redirect()->back()->with('error','File does not exists or deleted');
            }

        }

    }

    public function deleteFiles(ExportDataJob $dataJob){

        $path = storage_path('export_file');
        $status = $dataJob->exportStatus;
        $files = [];
        foreach ($status as $s){

            $file_name = $path.'/'.$s->file_name.'.xls';
            if(File::exists($file_name)){
                File::delete($file_name);
            }
            $s->delete();

        }
        $dataJob->delete();
        return redirect()->back()->with('success','File delete complete');

    }
}
