<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobAppliciant;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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
        if ($request->status == 'sel') {

            DB::beginTransaction();
            try {
                DB::statement("call send_sms_to_applicant(:message,:circular_id,:divisions,:units,:a_status)",[
                    'message'=>$request->message,
                    'circular_id'=>$request->circular,
                    'divisions'=>count($divisions)>0?implode(',',$divisions):'',
                    'units'=>count($units)>0?implode(',',$units):'',
                    'a_status'=>'selected'
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
            return response()->json(['status' => 'success', 'message' => 'Message send successfully']);
        }
        else if ($request->status == 'acc') {

            DB::beginTransaction();
            try {
                DB::statement("call send_sms_to_applicant(:message,:circular_id,:divisions,:units,:a_status)",[
                    'message'=>$request->message,
                    'circular_id'=>$request->circular,
                    'divisions'=>count($divisions)>0?implode(',',$divisions):'',
                    'units'=>count($units)>0?implode(',',$units):'',
                    'a_status'=>'accepted'
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
            return response()->json(['status' => 'success', 'message' => 'Message send successfully']);
        }
        return response()->json(['status' => 'error', 'message' => 'invalid request']);

    }
}
