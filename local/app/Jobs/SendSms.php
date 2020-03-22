<?php

namespace App\Jobs;

use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Nathanmac\Utilities\Parser\Facades\Parser;

class SendSms extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $ansar_ids;
    public function __construct($ansar_ids)
    {
        $this->ansar_ids = $ansar_ids;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $user = env('SSL_USER_ID');
        $pass = env('SSL_PASSWORD');
        $sid = env('SSL_SID');
        $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
        foreach($this->ansar_ids as $ansar_id){
            $ansar = PersonalInfo::where('ansar_id',$ansar_id)->select('mobile_no_self')->first();
            if($ansar){
//                Log::info("EMBODIED ".$ansar_id);
//                $phone = '88'.trim($ansar->mobile_no_self);
//                $body = "You are embodied";
//                $param = "user=$user&pass=$pass&sms[0][0]=$phone&sms[0][1]=" . urlencode($body) . "&sid=$sid";
//                $crl = curl_init();
//                curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
//                curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
//                curl_setopt($crl, CURLOPT_URL, $url);
//                curl_setopt($crl, CURLOPT_HEADER, 0);
//                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
//                curl_setopt($crl, CURLOPT_POST, 1);
//                curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
//                $response = curl_exec($crl);
//                curl_close($crl);
//                $r = Parser::xml($response);
//                Log::info($r);
            }
        }
    }
}
