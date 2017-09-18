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
use Illuminate\Support\Facades\Response;

trait ExportDataToExcel
{
    public function exportData($data,$type=''){
        try {
            DB::beginTransaction();
            $data = collect($data)->chunk(100)->toArray();
            $counter = 0;
            $export_job = Auth::user()->exportJob()->create([
                'total_file' => (int)ceil(count($data) / (float)20),
                'file_completed' => 0
            ]);
            $per_file = 0;
            $total = count($data);
            foreach ($data as $d) {
                if ($counter % 20 == 0) {
                    $file_name = \Illuminate\Support\Str::random(8) . Carbon::now()->timestamp;
                    $status = $export_job->exportStatus()->create([
                        'file_name' => $file_name,
                        'user_id' => Auth::user()->id,
                        'status' => 'pending',
                        'total_part' => $total - $per_file >= 20 ? 20 : $total - $per_file,
                        'counter' => 0
                    ]);
                    $per_file += 20;
                }
                $counter++;
                if (isset($status)) {
                    $this->dispatch(new ExportData($d, $status, $type));
                }

            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return Response::json(['status' => false,'type'=>'export', 'message' => "OPS!!! An error occur while exporting. Please try again later"]);
        }
        return Response::json(['status' => true,'type'=>'export', 'message' => "Export request submit successfully. You will be notified when export complete"]);

    }
}