<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\JobCircular;
use http\Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantScreeningController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            DB::enableQueryLog();
            $cicular_summery = JobCircular::with('category')->withCount([
                'appliciant',
                'appliciantMale',
                'appliciantFemale',
                'appliciantPaid',
                'appliciantNotPaid'
            ]);
            if ($request->exists('category') && $request->category != 'all') {
                $cicular_summery->where('job_category_id', $request->category);
            }
            if ($request->exists('circular') && $request->circular != 'all') {
                $cicular_summery->where('id', $request->circular);
            }
            if ($request->exists('status') && $request->status != 'all') {
                $cicular_summery->where('status', $request->status);
                /*$cicular_summery->where(function($q) use($request){
                    $q->whereHas('category',function($q) use($request){
                        $q->where('status',$request->status);
                    });
                    $q->orWhere('status',$request->status);
                });*/
            }
            $summery = $cicular_summery->get();
            Log::info(DB::getQueryLog());
            return response()->json($summery);
        }
        return view('recruitment::applicant.index');
    }

    public function searchApplicant()
    {
        return view('recruitment::applicant.search');
    }

    public function loadApplicants(Request $request)
    {
        $rules = [
            'category' => ['regex:/^([0-9]+)|(all)$/'],
            'circular' => ['regex:/^([0-9]+)|(all)$/']
        ];
        $this->validate($request, $rules);

        $query = JobAppliciant::whereHas('circular', function ($q) use ($request) {
            $q->where('status', 'active');
            if ($request->exists('circular') && $request->circular != 'all') {
                $q->where('id', $request->circular);
            }
            $q->whereHas('category', function ($q) use ($request) {
                $q->where('status', 'active');
                if ($request->exists('category') && $request->category != 'all') {
                    $q->where('id', $request->category);
                }
            });
        })->with(['division', 'district', 'thana'])->where('status', 'applied');
        return response()->json($query->get());
    }

    public function applicantListSupport(Request $request, $type=null)
    {
        DB::enableQueryLog();
        if ($request->q) {
            $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment'])->where(function ($query) use ($request) {
                $query->whereHas('payment', function ($q) use ($request) {
                    $q->where('txID', 'like', '%' . $request->q . '%');
                })->orWhere('mobile_no_self', 'like', '%' . $request->q . '%');
            });
            if($type=='applied'){
                $applicants->whereHas('payment', function ($q) {
                    $q->whereNotNull('txID');
                    $q->where('bankTxStatus','SUCCESS');
                });
                $applicants->where('status', $type);
            }
            else if($type=='Male'||$type=='Female'){
                $applicants->where('gender', $type);
            }
            else if($type){
                $applicants->where('status', $type);
            }
            $applicants = $applicants->paginate(50);
        }
        else{
            $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment']);
            if($type=='applied'){
                $applicants->whereHas('payment', function ($q) {
                    $q->whereNotNull('txID');
                });
            }
            else if($type=='Male'||$type=='Female'){
                $applicants->where('gender', $type);
            }
            else if($type){
                $applicants->where('status', $type);
            }
            $applicants = $applicants->paginate(50);
        }
//        return DB::getQueryLog();
        return view('recruitment::applicant.applicants_support', ['applicants' => $applicants,'type'=>$type]);
    }
    public function applicantList(Request $request, $circular_id,$type=null)
    {
        if($request->ajax()){
            DB::enableQueryLog();
            if ($request->q) {
                $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment'])->where(function ($query) use ($request) {
                    $query->whereHas('payment', function ($q) use ($request) {
                        $q->where('txID', 'like', '%' . $request->q . '%');
                    })->orWhere('mobile_no_self', 'like', '%' . $request->q . '%');
                });
                if($type=='applied'){
                    $applicants->whereHas('payment', function ($q) {
                        $q->whereNotNull('txID');
                        $q->where('bankTxStatus','SUCCESS');
                    });
                }
                else if($type=='Male'||$type=='Female'){
                    $applicants->where('gender', $type);
                }
                else if($type=='pending'){
                    $applicants->where(function($q){
                        $q->whereHas('payment', function ($q) {
                            $q->whereNotNull('txID');
                            $q->where('bankTxStatus','FAIL');
                        });
                        $q->orWhere('status','pending');
                    });
                }
                if($request->range&&$request->range!='all'){
                    $applicants->where('division_id', $request->range);
                }
                if($request->unit&&$request->unit!='all'){
                    $applicants->where('unit_id', $request->unit);
                }
                if($request->thana&&$request->thana!='all'){
                    $applicants->where('thana_id', $request->thana);
                }
                $applicants->where('job_circular_id',$circular_id);
                $applicants = $applicants->paginate(50);
            }
            else{
                $applicants = JobAppliciant::with(['division', 'district', 'thana', 'payment']);
                if($type=='applied'){
                    $applicants->whereHas('payment', function ($q) {
                        $q->whereNotNull('txID');
                        $q->where('bankTxStatus','SUCCESS');
                    });
                }
                if($type=='Male'||$type=='Female'){
                    $applicants->where('gender', $type);
                }
                else if($type=='pending'){
                    $applicants->where(function($q){
                        $q->whereHas('payment', function ($q) {
                            $q->whereNotNull('txID');
                            $q->where('bankTxStatus','FAIL');
                        });
                        $q->orWhere('status','pending');
                    });
                }
                if($request->range&&$request->range!='all'){
                    $applicants->where('division_id', $request->range);
                }
                if($request->unit&&$request->unit!='all'){
                    $applicants->where('unit_id', $request->unit);
                }
                if($request->thana&&$request->thana!='all'){
                    $applicants->where('thana_id', $request->thana);
                }
                $applicants->where('job_circular_id',$circular_id);
                $applicants = $applicants->paginate(50);
            }
//        return DB::getQueryLog();
            return view('recruitment::applicant.data', ['applicants' => $applicants]);
        }

        return view('recruitment::applicant.applicants', ['type'=>$type,'circular_id'=>$circular_id]);
    }

    public function markAsPaid($id)
    {
        return view('recruitment::applicant.mark_as_paid', ['id' => $id]);
    }

    public function updateAsPaid(Request $request, $id)
    {
        $rules = [
            'bankTxID' => 'required',
            'paymentOption' => 'required',

        ];
        $this->validate($request, $rules);
        DB::beginTransaction();
        try {
            $applicant = JobAppliciant::where('applicant_id', $id)->first();
            $payment = $applicant->payment;
            if ($payment) {
                $payment->returntxID = $payment->txID;
                $payment->bankTxID = $request->bankTxID;
                $payment->bankTxStatus = 'SUCCESS';
                $payment->txnAmount = 200;
                $payment->spCode = '000';
                $payment->spCodeDes = 'ApprovedManual';
                $payment->paymentOption = $request->paymentOption;
                $payment->save();
                $applicant->status = 'applied';
                $applicant->save();
                DB::commit();
                return redirect()->route('recruitment.applicant.list',['type'=>'pending'])->with('success_message', 'updated successfully');
            }
            return redirect()->route('recruitment.applicant.list')->with('error_message', 'This applicant has not pay yet');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('flash_error', $e->getMessage());
        }
        return view('recruitment::applicant.mark_as_paid', ['id' => $id]);
    }
    public function updateAsPaidByFile(Request $request)
    {
        if(!strcasecmp($request->method(),'post')){
            $rules = [
                'file' => 'required'

            ];
            $this->validate($request, $rules);
           // return $request->file('file')->path();
            DB::beginTransaction();
            try {
                $data = Excel::load($request->file('file'),function($reader){

                })->get();
//                return $data;
                foreach ($data as $d){
                    $applicant = JobAppliciant::whereHas('payment', function($q) use($d){
                        $q->where('txID',trim($d['txid']));
                    })->first();
                    if($applicant){
                        $payment = $applicant->payment;
                        if ($payment) {
                            if($applicant->status=='applied'){
                                Log::info('Found exists');
                                continue;
                            }
                            $payment->returntxID = trim($d->returntxid);
                            $payment->bankTxID = trim($d->banktxid);
                            $payment->bankTxStatus = 'SUCCESS';
                            $payment->txnAmount = 200;
                            $payment->spCode = '000';
                            $payment->spCodeDes = 'ApprovedManual';
                            $payment->paymentOption = trim($d->paymentoption);
                            $payment->save();
                            $applicant->status = 'applied';
                            $applicant->save();
                            Log::info('Found '.$d);
                            DB::commit();

                        }else{
                            Log::info('not found '.$d);
                        }
                    }
                    else{
                        Log::info('not found a'.$d);
                    }
//                return redirect()->route('recruitment.applicant.list')->with('error_message', 'This applicant has not pay yet');
                }
                return redirect()->route('recruitment.applicant.list',['type'=>'pending'])->with('success_message', 'updated successfully');
            } catch (Exception $e) {
                DB::rollback();
                Log::info($e->getTraceAsString());
                return $e->getTraceAsString();
                return redirect()->back()->with('flash_error', $e->getMessage());
            }
        }
        else{
            return view('recruitment::applicant.mark_as_paid_file');
        }
    }
}
