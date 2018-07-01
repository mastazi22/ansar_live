<?php

namespace App\modules\recruitment\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\recruitment\Models\JobCircularQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobApplicantQuotaController extends Controller
{
    //
    public function index(Request $request)
    {

        if (strcasecmp($request->method(), 'post') == 0) {
            if (!$request->ajax()) abort(401);
            $job_circular_id = $request->job_circular_id;
            $type = $request->type;
            $job_circular_quota = JobCircularQuota::where(compact('job_circular_id','type'))->first();
            if(!$job_circular_quota) {
                $job_circular_quota = JobCircularQuota::create(compact('job_circular_id','type'));
            }
            if($type=="range"){
                $quota = Division::with(['applicantQuota'=>function($q) use($job_circular_quota){
                    $q->where('job_circular_quota_id',$job_circular_quota->id);
                }]);
            }else{
                $quota = District::with(['applicantQuota'=>function($q) use($job_circular_quota){
                    $q->where('job_circular_quota_id',$job_circular_quota->id);
                }]);
            }
            return response()->json(['quota'=>$quota->get(),"cq"=>$job_circular_quota->id]);
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
            'district' => 'required_if:type,unit|regex:/^[0-9]+$/',
            'range_id' => 'required_if:type,range|regex:/^[0-9]+$/',
            'male' => 'regex:/^[0-9]+$/',
            'female' => 'regex:/^[0-9]+$/',
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try {
            if($request->type=='unit')$data = District::with('applicantQuota')->find($request->district);
            else $data = Division::with('applicantQuota')->find($request->range_id);
            if ($data&&$data->applicantQuota) {
                $data->applicantQuota()->update($request->only(['male','female','job_circular_quota_id']));
            } else {
                $data->applicantQuota()->create($request->only(['male','female','job_circular_quota_id']));
            }
            db::commit();
        }catch (\Exception $e){
            DB::rollback();
            Log::info($e->getTraceAsString());
//            return response()->json(['status'=>true,'message'=>'Can`t update quota. Please try again later']);
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }
        return response()->json(['status'=>true,'message'=>'Quota updated successfully']);
    }
}
