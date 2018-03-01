<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\modules\recruitment\Models\SmsQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class FeedbackSMS extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $message;
    private $mobile;
    public function __construct($message,$mobile)
    {
        $this->message= $message;
        $this->mobile= $mobile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::connection('recruitment')->beginTransaction();
        try{
            $payload = json_encode([
                'to'=>$this->mobile,
                'body'=>$this->message
            ]);
            SmsQueue::create([
                'payload'=>$payload,
                'try'=>0
            ]);
            DB::connection('recruitment')->commit();
        }catch(\Exception $e){
            DB::connection('recruitment')->rollback();
        }catch(\Error $e){
            DB::connection('recruitment')->rollback();
        }catch(\Throwable $e){
            DB::connection('recruitment')->rollback();
        }
        //
        /*$messID        = rand(1000,9999);
        $messageID     = $messID;
        $apiUser       = 'join_ans_vdp';
        $apiPass       = 'shurjoSM123';

        $sms_data = http_build_query(
            array(
                'API_USER' => $apiUser,
                'API_PASSWORD' => $apiPass,
                'MOBILE' => $this->mobile,
                'MESSAGE' =>$this->message,
                'MESSAGE_ID' => $messageID
            )
        );

        $ch  = curl_init();
        $url = "https://shurjobarta.shurjorajjo.com.bd/barta_api/api.php";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
        curl_setopt($ch,CURLOPT_POSTFIELDS,$sms_data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close ($ch);
        var_dump ($response);*/
    }
}
