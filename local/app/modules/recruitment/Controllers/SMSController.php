<?php

namespace App\modules\recruitment\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SmsQueueJob;
use App\modules\recruitment\Models\JobAppliciant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SMSController extends Controller
{
    //
    public function index()
    {
        return view('recruitment::applicant.applicant_sms_panel');
    }

    public function loadApplicantForSMS(Request $request)
    {

    }

    public function sendSMSToApplicant(Request $request)
    {
        $rules = [
            'circular' => 'required|regex:/^[0-9]+$/',
            'status' => 'required',
            'message' => 'required'
        ];
        $this->validate($request, $rules);
        $divisions = array_filter($request->divisions);
        $units = array_filter($request->units);
        $message = $request->message;
        $keys = [];
        preg_match_all('/[^{\}]+(?=})/', $message, $keys);
        $k = $keys[0];
        $query = JobAppliciant::where('job_circular_id', $request->circular);
        if(count($k)>0) $query->select('mobile_no_self', DB::raw(implode(',',$k)));
        else $query->select('mobile_no_self');
//        return $query->toSql();
        if (count($divisions) > 0) $query->whereIn('division_id', $divisions);
        if (count($units) > 0) $query->whereIn('unit_id', $units);
        if ($request->status == 'sel') {

            $query->where('status', 'selected');

        } else if ($request->status == 'acc') {

            $query->where('status', 'accepted');
        } else if ($request->status == 'app') {

            $query->where('status', 'applied');
        }else if ($request->status == 'pa') {

            $query->where('status', 'paid');
        }
//        return $query->toSql();
        DB::enableQueryLog();
        $data = $query->get();
//        return DB::getQueryLog();
        $datas = [];
        foreach ($data as $d) {
            $m = $message;
            foreach ($k as $key) {
                $m = str_replace("{".$key."}", $d->{$key}, $m);
            }
            array_push($datas, [
                'payload' => json_encode([
                    'to' => $d->mobile_no_self,
                    'body' => $m
                ]),
                'try' => 0
            ]);
        }
        foreach (array_filter($request->additional_number) as $ad) {
            array_push($datas, [
                'payload' => json_encode([
                    'to' => $ad,
                    'body' => $message
                ]),
                'try' => 0
            ]);
        }
		$dd = array_chunk($datas,10);
		foreach($dd as $d){
			$this->dispatch((new SmsQueueJob($d))->onQueue('recruitment'));
		}
//        return $datas;
        
        return response()->json(['status' => 'success', 'message' => 'Message send successfully']);

    }
    public function sendSMSToApplicantByUploadFile(Request $request)
    {
        $rules = [
            'sms_file' => 'required',
        ];
        $this->validate($request, $rules);
        $data = [];
        Excel::load($request->file('sms_file'), function ($reader) use(&$data) {
            $data = $reader->toArray()[0];
        });
        return $data;
        $datas = [];
        foreach ($data as $d) {
            $m = $d['sms_body'];
            array_push($datas, [
                'payload' => json_encode([
                    'to' => $d['mobile_no_self'],
                    'body' => $m
                ]),
                'try' => 0
            ]);
        }
        $dd = array_chunk($datas,10);
        foreach($dd as $d){
            $this->dispatch((new SmsQueueJob($d))->onQueue('recruitment'));
        }
//        return $datas;

        return response()->json(['status' => 'success', 'message' => 'Message send successfully']);

    }
}
