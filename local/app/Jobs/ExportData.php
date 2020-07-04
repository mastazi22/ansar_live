<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\modules\HRM\Models\DataExportStatus;
use App\modules\HRM\Models\ExportDataJob;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Psy\Util\Str;
use WebSocket\Client;

class ExportData extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $export_data;
    private $export_status;
    private $all_headers = [
        'all_ansar' => ['SL No.', 'Ansar ID', 'Name','Birth Date','Rank',   'Home District', 'Thana'],
        'not_verified_ansar' => ['SL No.', 'Ansar ID', 'Name','Birth Date','Rank',   'Home District', 'Thana'],
        'free_ansar' => ['SL No.', 'Ansar ID', 'Name','Birth Date','Rank',   'Home District', 'Thana'],
        'paneled_ansar' => ["SL. No", "Ansar ID", "Rank", "Name", "Birth Date", "Home District", "Thana", "Panel Date & Time", "Panel Id"],
        'embodied_ansar' => ["SL. No", "Ansar ID", "Rank", "Name", "Birth Date", "Home District", "Thana", "Kpi Name", "Embodiment Date", "Embodiment Id"],
        'rest_ansar' => ["SL. No", "Ansar ID", "Name", "Birth Date","Rank",  "Home District", "Thana", "Rest Date"],
        'freezed_ansar' => ["SL. No", "Ansar ID", "Name", "Birth Date","Rank",  "Home District", "Thana", "Freeze Reason", "Freeze Date"],
        'blocked_ansar' => ["SL. No", "Ansar ID", "Name", "Birth Date","Rank",  "Home District", "Thana", "Block Reason", "Block Date"],
        'blacked_ansar' => ["SL. No", "Ansar ID", "Name", "Birth Date","Rank",  "Home District", "Thana", "Black Reason", "Black Date"],
        'offerred_ansar' => ["SL. No", "Ansar ID", "Name", "Birth Date","Rank",  "Home District", "Thana", "Offer District", "Offer Date"],
        'own_embodied_ansar' => ["SL. No", "Ansar ID", "Name", "Birth Date","Rank",  "Home District", "Thana", "Kpi Name", "Embodiment Date", "Embodiment Id"],
        'embodied_ansar_in_different_district' => ["SL. No", "Ansar ID", "Name", "Birth Date","Rank",  "Home District", "Thana", "Kpi Name", "Embodiment Date", "Embodiment Id"],
        '3_year_over_list' => ["SL. No", "Ansar ID", "Name", "Rank", "Current KPI Name", "KPI Unit", "KPI Thana", "Joining Date", "Service Ended Date"],
    ];
    private $type;

    public function __construct($export_data, $export_status, $type)
    {

        $this->export_data = collect($export_data)->toArray();
        $this->export_status = $export_status;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $save_path = storage_path('export_file');
        if (!File::exists($save_path)) File::makeDirectory($save_path);
        if (File::exists($save_path . '/' . $this->export_status->file_name . '.xls')) {

            Excel::load($save_path . '/' . $this->export_status->file_name . '.xls', function ($excel) {

                $excel->sheet('sheet1', function ($sheet) {

                    $counter = $this->export_status->counter*100+1;
                    $dd = [];
                    foreach ($this->export_data as $r) {

                        $r = array_values((array)$r);
                        array_unshift($r, $counter++);
                        array_push($dd, $r);
                        ////Log::info("MODIFYING : ".$counter);


                    }
                    $sheet->rows($dd);
                    $this->export_status->counter +=1;
                    $this->export_status->save();

                });

            })->store('xls', $save_path);

        } else {
            Excel::create($this->export_status->file_name, function ($excel) {

                $i = 1;
                $counter = $this->export_status->counter*100+1;
                $dd = [];
                array_push($dd, $this->all_headers[$this->type]);
                foreach ($this->export_data as $r) {

                    $r = array_values((array)$r);
                    array_unshift($r, $counter++);
                    array_push($dd, $r);
                   // //Log::info($counter);


                }
                $excel->sheet('sheet1', function ($sheet) use ($dd) {

                    $sheet->fromArray($dd, null, 'A1', false, false);

                });

            })->store('xls', $save_path);
            $this->export_status->status='success';
            $this->export_status->counter +=1;
            $this->export_status->save();

        }
        if($this->export_status->counter==$this->export_status->total_part){
            $edj = ExportDataJob::find($this->export_status->data_export_job_id);
            $edj->file_completed+=1;
            $edj->save();
            if($edj->file_completed==$edj->total_file){
                //do something
                //Log::info("FILE GENERATING COMPLETE. ID : ".$edj->id);
                $client = new Client($edj->notification_url);
                $client->send(json_encode(['user'=>'server','to'=>$edj->user_id,'message'=>"<h3 style='margin-top: 0'>Data exported complete.</h3><a class='btn btn-xs btn-info' href='".$edj->download_url."'>download</a> or <a class='btn btn-danger btn-xs' href='".$edj->delete_url."'>delete</a>"]));
                $client->close();
            }
        }

    }
}
