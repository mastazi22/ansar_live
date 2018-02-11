<?php

namespace App\Jobs;

use App\Helper\SMSTrait;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DisembodiedSMS extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels,SMSTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $date;
    private $reason;
    private $mobile_no;

    /**
     * DisembodiedSMS constructor.
     * @param $date
     * @param $reason
     * @param $mobile_no
     */
    public function __construct($date, $reason, $mobile_no)
    {
        $this->date = $date;
        $this->reason = $reason;
        $this->mobile_no = $mobile_no;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $message = "Apni {$this->date} tarikh e '{$this->reason}' karon e disembodied hoyechen. ";
        $this->sendSMS($this->mobile_no,$message);
    }
}
