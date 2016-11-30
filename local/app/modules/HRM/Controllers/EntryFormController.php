<?php

namespace App\modules\HRM\Controllers;

use App\Events\ActionUserEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\AllDisease;
use App\modules\HRM\Models\AllEducationName;
use App\modules\HRM\Models\AllSkill;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class EntryFormController extends Controller
{

    public function entrylist()
    {
        $user = Auth::user();
        $userType = Auth::user()->type;
        if ($userType == 11 || $userType == 22 || $userType == 33 || $userType == 66) {
            $notVerified = PersonalInfo::where('verified', '1')->orwhere('verified', '0')->count('ansar_id');
            $Verified = Personalinfo::where('verified', '2')->count('ansar_id');

        } elseif ($userType == 55) {
            $notVerified = PersonalInfo::where('verified', '0')->where('user_id', Auth::user()->id)->count('ansar_id');
            $Verified = Personalinfo::where('verified', '1')->where('user_id', Auth::user()->id)->count('ansar_id');
        } else {
            $notVerified = PersonalInfo::where('verified', '1')->count('ansar_id');
            $Verified = Personalinfo::where('verified', '2')->where('user_id', Auth::user()->id)->count('ansar_id');
        }
        return View::make('HRM::Entryform.entrylist')->with(['notVerified' => $notVerified, 'Verified' => $Verified]);
    }

    public function entryInfo(Request $request)
    {
//        return $request->ansar_id;
        if($request->ansar_id){
            $ansar = PersonalInfo::where('ansar_id',$request->ansar_id)->first();
            if(!$ansar) return Redirect::back()->with('entryInfo','<p class="text text-danger">No Ansar found with this id</p>');
            $data = View::make('HRM::Entryform.entry_info',['ansarAllDetails'=>$ansar,'label'=>(object)Config::get('report.label'),'type'=>'eng','title'=>(object)Config::get('report.title')]);
            return Redirect::back()->with('entryInfo',$data->render());

        }
        return View::make('HRM::Entryform.entryinfo');
    }
    public function ansarDetails($ansarid)
    {
        $alldetails = PersonalInfo::where('ansar_id', $ansarid)->first();
        return View::make('entryform/viewform')->with('ansarAllDetails', $alldetails);
    }

    public function entryVerify(Request $request)
    {

        $usertype = Auth::user()->type;
        $rules = ['verified_id'=>'required|numeric|regex:/^[0-9]+$/'];
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return response("Invalid request(400)",400);
        }
        $verifyid = $request->input('verified_id');
        $ansar = PersonalInfo::where('ansar_id',$verifyid)->first();
        if(empty($ansar->mobile_no_self)||!preg_match('/^[0-9]+$/',$ansar->mobile_no_self))
            return Response::json(['status'=>false,'message'=>'This ansar can`t be verified. Because this ansar`s mobile no is empty or invalid']);
        if ($usertype == 55) {
            $success = PersonalInfo::where('ansar_id', $verifyid)->update(['verified' => 1]);
            if ($success) {
                CustomQuery::addActionlog(['ansar_id' => $verifyid, 'action_type' => 'VERIFIED', 'from_state' => 'ENTRY', 'to_state' => 'VERIFIED', 'action_by' => auth()->user()->id]);
                return 1;

            } else
                return 0;
        }
        if ($usertype == 44 || $usertype == 11 || $usertype == 22 || $usertype == 33 || $usertype == 66) {
            $success = PersonalInfo::where('ansar_id', $verifyid)->update(['verified' => 2]);
            $statusSuccess = AnsarStatusInfo::where('ansar_id', $verifyid)->update(['free_status' => 1]);

            if ($success && $statusSuccess) {
                CustomQuery::addActionlog(['ansar_id' => $verifyid, 'action_type' => 'VERIFIED', 'from_state' => 'VERIFIED', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);
                return 1;
            } else
                return 0;
        }
    }
    public function entryChunkVerify(Request $request)
    {
        $user = Auth::user();
        $ids = $request->input('not_verified');
        $rules =[
            'not_verified'=>'required|is_array|array_type:int'
        ];
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return Response::json(['status'=>false,'message'=>'Invalid Request']);
        }
        $messages = [];
        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($ids); $i++) {
                $ansar = PersonalInfo::where('ansar_id', $ids[$i])->first();
                if(!preg_match('/^(\+88)?[0-9]{11}$/',$ansar->mobile_no_self)){
                    //return 'error';
                    array_push($messages,['status'=>false,'message'=>'Ansar id '.$ansar->ansar_id.' does not contain valid mobile no']);
                }
                else{
                    if($user->type==55){
                        $ansar->verified = 1;
                        $ansar->save();
                    }
                    else {
                        $ansar->verified = 2;
                        $ansar->status->free_status = 1;
                        $ansar->save();
                        $ansar->status->save();
                    }
                    CustomQuery::addActionlog(['ansar_id' => $ids[$i], 'action_type' => 'VERIFIED', 'from_state' => 'ENTRY', 'to_state' => 'VERIFIED', 'action_by' => auth()->user()->id]);
                    array_push($messages,['status'=>true,'message'=>$ansar->ansar_id.' verified successfully']);
                    DB::commit();
                }


            }
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'messege' => $e->getMessage()]);
        }
        return Response::json(['status' => true, 'messege' => $messages]);
    }

    public function Reject(Request $request)
    {
        $user = Auth::user();
        $usertype = $user->type;
        $rules = ['reject_id'=>'required|numeric|regex:/^[0-9]+$/'];
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return response("Invalid request(400)",400);
        }
        $rejectid = $request->input('reject_id');

        if ($usertype == 44) {
            $success = PersonalInfo::where('ansar_id', $rejectid)->update(['verified' => 0]);
            if ($success) {
                Event::fire(new ActionUserEvent(['ansar_id' => $rejectid, 'action_type' => 'REJECT', 'from_state' => 'VERIFIED', 'to_state' => 'ENTRY', 'action_by' => auth()->user()->id]));
                return 1;

            } else
                return 0;
        }
    }

    public function entryReport($ansarid,$type='eng')
    {
        $ansardetails = PersonalInfo::where('ansar_id', $ansarid)->first();
        return View::make('HRM::Entryform.reportEntryForm')->with(['ansarAllDetails'=>$ansardetails,'type'=>$type,'title'=>(object)Config::get('report.title'),'label'=>(object)Config::get('report.label')]);
    }

    public function getfreezelist()
    {

    }

    public function getAllDisease()
    {
        $allDisease = AllDisease:: orderBy('id', 'desc')->get();
        return Response::json($allDisease);
    }

    public function getAllSkill()
    {
        $allSkill = AllSkill::orderBy('id', 'desc')->get();
        return Response::json($allSkill);
    }

    public function entryAdvancedSearch()
    {
        return View::make('HRM::Entryform.advancedsearch');
    }

    public function ansarOriginalInfo()
    {
        return View::make('HRM::Entryform.originalinfo');
    }

    public function getAllEducation()
    {
//        $allEducation = AllEducationName::where('id', '!=', 0)->get();
        $allEducation = AllEducationName::all();
        return Response::json($allEducation);
    }
}