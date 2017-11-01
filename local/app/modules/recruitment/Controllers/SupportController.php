<?php

namespace App\modules\recruitment\Controllers;

use App\Jobs\FeedbackSMS;
use App\modules\recruitment\Models\FeebBack;
use App\modules\recruitment\Models\JobAppliciant;
use http\Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    //
    public function problemReport(){
        $applicants  = FeebBack::where('problem_type','payment')->where('status','pending')->paginate(50);
        return view('recruitment::support.applicants_feedback',['applicants'=>$applicants]);
    }

    public function replyProblem(Request $request,$id){

        if(!$request->exists('type')){
            return redirect()->back()->with('error_message','Invalid Request');
        }
        if($request->type=='verify'){
            DB::beginTransaction();
            try {
                $feedBack = FeebBack::find($id);
                if($feedBack){
                    $applicant = $feedBack->applicant;
                    $payment = $applicant?$applicant->payment:null;
                    if ($payment) {
                        $payment->returntxID = $payment->txID;
                        $payment->bankTxID = $feedBack->txid;
                        $payment->bankTxStatus = 'SUCCESS';
                        $payment->txnAmount = 200;
                        $payment->spCode = '000';
                        $payment->spCodeDes = 'ApprovedManual';
                        $payment->paymentOption = $feedBack->payment_option;
                        $payment->save();
                        $applicant->status = 'applied';
                        $applicant->save();
                        $feedBack->status='verify';
                        $feedBack->save();
                        $this->dispatch(new FeedbackSMS($request->message,$applicant->mobile_no_self));
                        DB::commit();
                        return redirect()->back()->with('success_message', 'verified successfully');
                    }
                }
                return redirect()->back()->with('error_message', 'This applicant not found');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error_message', $e->getMessage());
            }
        }
        elseif ($request->type=='reject'){
            DB::beginTransaction();
            try {
                $feedBack = FeebBack::find($id);
                if($feedBack){
                    $feedBack->status='verify';
                    $feedBack->save();
                    $this->dispatch(new FeedbackSMS($request->message,$feedBack->mobile_no_self));
                    DB::commit();
                    return redirect()->back()->with('success_message', 'rejected successfully');
                }
                return redirect()->back()->with('error_message', 'This applicant not found');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error_message', $e->getMessage());
            }
        }
        else{
            return redirect()->back()->with('error_message','Invalid Request');
        }
    }
    public function replyProblemDelete($id){
        $feedback = FeebBack::find($id);
        if($feedback){
            $feedback->delete();
            return redirect()->back()->with('success_message', 'deleted successfully');
        }
        return redirect()->back()->with('error_message', 'not found');
    }
}
