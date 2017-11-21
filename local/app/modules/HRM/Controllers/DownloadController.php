<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\ExportDataJob;
use function foo\func;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class DownloadController extends Controller
{
    //
    public function downloadFile(ExportDataJob $dataJob){

        $path = storage_path('export_file/'.$dataJob->id);
        $zPath = storage_path('export_file');

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
            $des = $zPath.'/export.zip';
            $zip = new \ZipArchive();
            if(!$zip->open($des,\ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE)){


                return redirect()->back()->with('error','File does not exists or deleted');
            }
            else{

                for ($i=0;$i<count($files);$i++){

                    $zip->addFile($files[$i],($i+1).'.xls');

                }
                $zip->close();
                File::delete($path);
                return response()->download($des)->deleteFileAfterSend(true);
            }

        }

    }
    public function downloadFileByName($file){

        $p = base64_decode($file);
        return response()->download($p)->deleteFileAfterSend(true);

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

    public function generatingFile(ExportDataJob $dataJob){

        $path = storage_path('export_file/'.$dataJob->id);
        if(!File::exists($path)){
            File::makeDirectory($path,777,true);
        }
        $status = $dataJob->exportStatus()->where('status','pending')->first();
        $data = unserialize(gzuncompress($status->payload));
//        return $data;
        Excel::create($status->file_name,function ($excel) use ($data){
            $excel->sheet('sheet1',function ($sheet) use ($data){
                $sheet->loadView($data['view'],$data['data']);
            });
        })->store('xls',$path);
        $status->status = 'success';
        $status->save();
        return response()->json(['status'=>true]);
    }
}
