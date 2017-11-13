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
        if ($request->status == 'selected') {
            $query = JobAppliciant::whereHas('selectedApplicant', function () {

            })->where('status', $request->status)->where('job_circular_id', $request->circular);
            $divisions = array_filter($request->divisions);
            $units = array_filter($request->units);
            if (count($divisions) > 0) {
                $query->whereIn('division_id', $divisions);
            }
            if (count($units) > 0) {
                $query->whereIn('unit_id', $units);
            }
            $applicants = $query->get();
            DB::beginTransaction();
            try {
                foreach ($applicants as $a) {

                    $a->selectedApplicant()->update([
                        'message' => $request->message,
                        'sms_status' => 'on'
                    ]);
                    DB::commit();

                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
            return response()->json(['status' => 'success', 'message' => 'Message send successfully']);
        }
        else if ($request->status == 'accepted') {
            $query = JobAppliciant::whereHas('accepted', function () {

            })->where('status', $request->status)->where('job_circular_id', $request->circular);
            $divisions = array_filter($request->divisions);
            $units = array_filter($request->units);
            if (count($divisions) > 0) {
                $query->whereIn('division_id', $divisions);
            }
            if (count($units) > 0) {
                $query->whereIn('unit_id', $units);
            }
            $applicants = $query->get();
            DB::beginTransaction();
            try {
                foreach ($applicants as $a) {

                    $a->accepted()->update([
                        'message' => $request->message,
                        'sms_status' => 'on'
                    ]);
                    DB::commit();

                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
            return response()->json(['status' => 'success', 'message' => 'Message send successfully']);
        }
        return response()->json(['status' => 'error', 'message' => 'invalid request']);

    }
}
