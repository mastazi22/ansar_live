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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Mockery\Exception;

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

    public function entryform()
    {

        return View::make('HRM::Entryform.entryform');
    }

    public function ansarDetails($ansarid)
    {
        $alldetails = PersonalInfo::where('ansar_id', $ansarid)->first();
        return View::make('entryform/viewform')->with('ansarAllDetails', $alldetails);
    }

    public function entryVerify(Request $request)
    {

        $usertype = Auth::user()->type;
        if ($request->exists('chunk_verification')) {
            $ids = $request->input('not_verified');
            DB::beginTransaction();
            try {
                for ($i = 0; $i < count($ids); $i++) {
                    $success = PersonalInfo::where('ansar_id', $ids[$i])->update(['verified' => 2]);
                    $status = AnsarStatusInfo::where('ansar_id',$ids[$i])->update(['free_status'=>1]);
                    CustomQuery::addActionlog(['ansar_id' => $ids[$i], 'action_type' => 'VERIFIED', 'from_state' => 'ENTRY', 'to_state' => 'VERIFIED', 'action_by' => auth()->user()->id]);
                    if (!$success&&!$status) throw new Exception("An Error Occur While Verifying. Please try again later");
                    DB::commit();

                }
            } catch (Exception $e) {
                DB::rollback();
                return Response::json(['status' => false, 'messege' => $e->getMessage()]);
            }
            return Response::json(['status' => true, 'messege' => 'Ansar Verification Complete Successfully']);
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

    public function Reject(Request $request)
    {
        $user = Auth::user();
        $usertype = $user->type;
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

    public function entryReport($ansarid)
    {
        $ansardetails = PersonalInfo::where('ansar_id', $ansarid)->first();
        return View::make('HRM::Entryform/reportEntryForm')->with('ansarAllDetails', $ansardetails);
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