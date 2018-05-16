<?php

namespace App\Jobs;

use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\SD\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateAttendance extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $data;
    private $day;
    private $month;
    private $year;

    /**
     * GenerateAttendance constructor.
     * @param $data
     * @param $day
     * @param $month
     * @param $year
     */
    public function __construct($data, $day, $month, $year)
    {
        $this->data = $data;
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

//        Log::info($this->data);
        if(!DB::connection('sd')->getDatabaseName()){
            Log::info("SERVER RECONNECTING....");
            DB::reconnect('sd');
        }
        Log::info("CONNECTION DATABASE : ".DB::connection('sd')->getDatabaseName());
        DB::connection('sd')->beginTransaction();
        try {
            foreach ($this->data as $data){
                $exists = Attendance::where([
                    'day'=>$this->day,
                    'month'=>$this->month,
                    'year'=>$this->year,
                    'ansar_id'=>$data->ansar_id,
                    'kpi_id'=>$data->id,
                ])->exists();
                Log::info("EXISts DATABASE : ".($exists?1:0));
                if($exists) continue;
                Attendance::create([
                    'day'=>$this->day,
                    'month'=>$this->month,
                    'year'=>$this->year,
                    'ansar_id'=>$data->ansar_id,
                    'kpi_id'=>$data->id,
                ]);
            }
            DB::connection('sd')->commit();
        }
        catch (\Exception $e) {
            DB::connection('sd')->rollback();
            Log::info($e->getTraceAsString());
            //return response(collect(['status' => 'error', 'message' => $e->getMessage()])->toJson(), 400, ['Content-Type' => 'application/json']);
        }

    }
}
