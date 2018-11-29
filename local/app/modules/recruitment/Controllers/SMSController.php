<?php

namespace App\modules\recruitment\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SmsQueueJob;
use App\modules\recruitment\Models\JobAppliciant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $query = JobAppliciant::select('mobile_no_self', ...$k)
            ->where('job_circular_id', $request->circular);
        if (count($divisions) > 0) $query->whereIn('division_id', $divisions);
        if (count($units) > 0) $query->whereIn('unit_id', $units);
        if ($request->status == 'sel') {

            $query->where('status', 'selected');

        } else if ($request->status == 'acc') {

            $query->where('status', 'accepted');
        } else if ($request->status == 'app') {

            $query->where('status', 'applied');
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
//        return $datas;
        $this->dispatch((new SmsQueueJob($datas))->onQueue('recruitment'));
        return response()->json(['status' => 'success', 'message' => 'Message send successfully']);

    }
}
