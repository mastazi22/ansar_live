<?php

namespace App\Jobs;

use App\modules\HRM\Controllers\HrmController;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nathanmac\Utilities\Parser\Facades\Parser;

class SendTest extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $ansar_id;
    public function __construct()
    {
        //$this->ansar_id = $ansar_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        $r = app()->call('App\Http\Controllers\UserController@dbTest');
            Log::info($r);
    }
}
