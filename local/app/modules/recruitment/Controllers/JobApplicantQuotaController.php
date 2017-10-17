<?php

namespace App\modules\recruitment\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobApplicantQuotaController extends Controller
{
    //
    public function index(Request $request)
    {

        if (strcasecmp($request->method(), 'post') == 0) {
            if (!$request->ajax()) abort(401);
            $quota = District::with('applicantQuota');
            if ($request->exists('division') && $request->division != 'all') {
                $quota->where('division_id', $request->division);
            }
            if ($request->exists('district') && $request->district != 'all') {
                $quota->where('id', $request->district);
            }
            return response()->json($quota->get());
        }
        return view('recruitment::applicant_quota.index');
    }

    public function edit(Request $request)
    {

        return view('recruitment::applicant_quota.edit');
    }
    public function update(Request $request)
    {
        if (!$request->ajax()) abort(401);
        $rules = [
            'district' => 'required|regex:/^[0-9]+$/',
            'male' => 'regex:/^[0-9]+$/',
            'female' => 'regex:/^[0-9]+$/',
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try {
            $data = District::with('applicantQuota')->find($request->district);
            if ($data->applicantQuota) {
                $data->applicantQuota()->update($request->only(['male','female']));
            } else {
                $data->applicantQuota()->create($request->only(['male','female']));
            }
            db::commit();
        }catch (\Exception $e){
            DB::rollback();
//            return response()->json(['status'=>true,'message'=>'Can`t update quota. Please try again later']);
            return response()->json(['status'=>true,'message'=>$e->getMessage()]);
        }
        return response()->json(['status'=>true,'message'=>'Quota updated successfully']);
    }
}
