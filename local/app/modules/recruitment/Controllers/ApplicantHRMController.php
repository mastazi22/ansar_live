<?php

namespace App\modules\recruitment\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\Designation;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Edication;
use App\modules\HRM\Models\Nominee;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\TrainingInfo;
use App\modules\recruitment\Models\JobApplicantHRMDetails;
use App\modules\recruitment\Models\JobEducationInfo;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApplicantHRMController extends Controller
{
    //
    public function index(Request $request)
    {
        if (strcasecmp($request->method(), 'post') == 0) {
            if ($request->ajax()) {
                $applicants = JobApplicantHRMDetails::onlyTrashed()->with(['division', 'district', 'thana'])
                    ->where('job_circular_id', $request->circular);
                if ($request->range && $request->range != 'all') {
                    $applicants->where('division_id', $request->range);
                }
                if ($request->unit && $request->unit != 'all') {
                    $applicants->where('unit_id', $request->unit);
                }
                if ($request->thana && $request->thana != 'all') {
                    $applicants->where('thana_id', $request->thana);
                }
                if ($request->q) {
                    $applicants->where(function ($q) use ($request) {
                        $q->where('ansar_name_eng', 'LIKE', '%' . $request->q . '%');
                        $q->orWhere('ansar_name_bng', 'LIKE', '%' . $request->q . '%');
                        $q->orWhere('mobile_no_self', $request->q);
                        $q->orWhere('national_id_no', $request->q);
                    });
                }
                $limit = $request->limit ? $request->limit : 50;
                return view('recruitment::hrm.part_hrm_applicant_info', ['applicants' => $applicants->paginate($limit)]);
            } else abort(403);
        }
        return view('recruitment::hrm.applicant_details_for_hrm');
    }
    public function print_card(Request $request)
    {
        if (strcasecmp($request->method(), 'post') == 0) {
            if ($request->ajax()) {
                $applicants = JobApplicantHRMDetails::with(['division', 'district', 'thana','designation','bloodGroup'])
                    ->where('job_circular_id', $request->circular)
                    ->where(function ($q){
                        $q->whereNotNull('ansar_id');
                        $q->Where('ansar_id','>',0);
                    });
                if ($request->range && $request->range != 'all') {
                    $applicants->where('division_id', $request->range);
                }
                if ($request->unit && $request->unit != 'all') {
                    $applicants->where('unit_id', $request->unit);
                }
                if ($request->thana && $request->thana != 'all') {
                    $applicants->where('thana_id', $request->thana);
                }
                if ($request->q) {
                    $id = District::where('unit_name_eng','LIKE',"%{$request->q}%")
                        ->orWhere('unit_name_bng','LIKE',"%{$request->q}%")->first();
                    if($id&&(!$request->unit||$request->unit=='all')) {
                        $applicants->where(function ($q) use ($request, $id) {
                            $q->where('ansar_name_eng', 'LIKE', '%' . $request->q . '%');
                            $q->orWhere('ansar_name_bng', 'LIKE', '%' . $request->q . '%');
                            $q->orWhere('mobile_no_self', $request->q);
                            $q->orWhere('national_id_no', $request->q);
                            $q->orWhere('unit_id', $id->id);
                        });
                    } else{
                        $applicants->where(function ($q) use ($request, $id) {
                            $q->where('ansar_name_eng', 'LIKE', '%' . $request->q . '%');
                            $q->orWhere('ansar_name_bng', 'LIKE', '%' . $request->q . '%');
                            $q->orWhere('mobile_no_self', $request->q);
                            $q->orWhere('national_id_no', $request->q);
                        });
                    }
                }
                $limit = $request->limit ? $request->limit : 50;
//                return $applicants->paginate($limit);
                return view('recruitment::hrm.part_hrm_applicant_card_info', ['applicants' => $applicants->paginate($limit)]);
            } else abort(403);
        }
        return view('recruitment::hrm.print_applicant_id_card_for_hrm');
    }

    public function applicantEditForHRM($type, $circular_id, $id)
    {
        $ansarAllDetails = JobApplicantHRMDetails::onlyTrashed()->with(['division', 'district', 'thana', 'skill', 'disease', 'designation', 'bloodGroup'])
            ->where('id', $id)
            ->where('job_circular_id', $circular_id)
            ->first();
        $ranks = Designation::all();
        $educations = JobEducationInfo::all();
//        return $educations->where('id',intval('7'))->first();
        if ($type == 'download') {
            $pdf = SnappyPdf::loadView('recruitment::hrm.hrm_form_details', compact('ansarAllDetails', 'ranks', 'educations'))
                ->setOption('encoding', 'UTF-8')
                ->setOption('zoom', 0.73);
            return $pdf->download();
        } else if ($type == 'view') {
            $pdf = SnappyPdf::loadView('recruitment::hrm.hrm_form_details', compact('ansarAllDetails', 'ranks', 'educations'))
                ->setOption('encoding', 'UTF-8')
                ->setOption('zoom', 0.73);
            return $pdf->stream();
        }
//        return view('recruitment::hrm.hrm_form_download',['ansarAllDetails'=>$applicant]);
    }

    public function moveApplicantToHRM($id)
    {

        DB::connection('hrm')->beginTransaction();
        DB::beginTransaction();
        try {
            $applicant_hrm_details = JobApplicantHRMDetails::onlyTrashed()->find($id);
            if ($applicant_hrm_details) {
                $data = clone $applicant_hrm_details;
                $ansar_id = intval(PersonalInfo::orderBy('ansar_id', 'desc')->first()->ansar_id) + 1;
                $applicant_hrm_details['ansar_id'] = $data['ansar_id'] = $ansar_id;
                $education_info = $data['appliciant_education_info'];
                $training_info = $data['applicant_training_info'];
                $nominee_info = $data['applicant_nominee_info'];
                unset($data['appliciant_education_info']);
                unset($data['applicant_training_info']);
                unset($data['applicant_nominee_info']);
                foreach ($education_info as $ed) {
                    $ed->education_id = $ed->job_education_id;
                    unset($ed->job_education_id);
                    unset($ed->job_applicant_id);
                    unset($ed->created_at);
                    unset($ed->updated_at);
                }

                unset($data['updated_at']);
                unset($data['updated_at']);
                unset($data['deleted_at']);
                unset($data['applicant_id']);
                unset($data['job_circular_id']);
//                return $data;
//                return $data['profile_pic'];
                $profile_pic = storage_path('data' . DIRECTORY_SEPARATOR . 'photo');
                $sign_pic = storage_path('data' . DIRECTORY_SEPARATOR . 'signature');
                if (!File::exists($profile_pic)) File::makeDirectory($profile_pic);
                if (!File::exists($sign_pic)) File::makeDirectory($sign_pic);
                if ($data['profile_pic']&&File::exists($data['profile_pic'])) {
                    if (!File::move($data['profile_pic'], $profile_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg')) {
                        throw new \Exception("Can`t move image. please try again later");
                    }

                }
                $data['profile_pic'] = 'data/photo/' . $ansar_id . '.jpg';
                if ($data['sign_pic']&&File::exists($data['sign_pic'])) {
                    if (!File::move($data['sign_pic'], $sign_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg')) {
                        throw new \Exception("Can`t move image. please try again later");
                    }

                }
                $data['sign_pic'] = 'data/signature/' . $ansar_id . '.jpg';
                $data['verified'] = 0;
                $ansar_new = new PersonalInfo(json_decode(json_encode($data), true));
                foreach ($training_info as $training) {
                    $ansar_new->training()->save(new TrainingInfo((array)$training));
                }
                foreach ($education_info as $education) {
                    $ansar_new->education()->save(new Edication((array)$education));
                }
                foreach ($nominee_info as $nominee) {
                   $ansar_new->nominee()->save(new Nominee((array)$nominee));
                }
                $ansar_new->status()->save(new AnsarStatusInfo());
                $ansar_new->save();
                $applicant_hrm_details->save();
                $applicant_hrm_details->restore();

                DB::connection('hrm')->commit();
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Ansar move to HRM successfully']);


            } else {
                throw new \Exception("Invalid request");
            }

        } catch (\Error $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            DB::connection('hrm')->rollback();
            DB::rollback();
            if(isset($sign_pic))$this->rollbackFile($sign_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['sign_pic']);
            if(isset($profile_pic))$this->rollbackFile($profile_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['profile_pic']);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            DB::connection('hrm')->rollback();
            DB::rollback();
            if(isset($sign_pic))$this->rollbackFile($sign_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['sign_pic']);
            if(isset($profile_pic))$this->rollbackFile($profile_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['profile_pic']);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            DB::connection('hrm')->rollback();
            DB::rollback();
            if(isset($sign_pic))$this->rollbackFile($sign_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['sign_pic']);
            if(isset($profile_pic))$this->rollbackFile($profile_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['profile_pic']);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function moveBulkApplicantToHRM(Request $request)
    {
        $response = [
            'success'=>0,
            'fail'=>0,
        ];
        foreach ($request->hrmIds as $id){
            DB::connection('hrm')->beginTransaction();
            DB::beginTransaction();
            try {
                $applicant_hrm_details = JobApplicantHRMDetails::onlyTrashed()->find($id);
                if ($applicant_hrm_details) {
                    $data = clone $applicant_hrm_details;
                    $ansar_id = intval(PersonalInfo::orderBy('ansar_id', 'desc')->first()->ansar_id) + 1;
                    $applicant_hrm_details['ansar_id'] = $data['ansar_id'] = $ansar_id;
                    $education_info = $data['appliciant_education_info'];
                    $training_info = $data['applicant_training_info'];
                    $nominee_info = $data['applicant_nominee_info'];
                    unset($data['appliciant_education_info']);
                    unset($data['applicant_training_info']);
                    unset($data['applicant_nominee_info']);
                    foreach ($education_info as $ed) {
                        $ed->education_id = $ed->job_education_id;
                        unset($ed->job_education_id);
                        unset($ed->job_applicant_id);
                        unset($ed->created_at);
                        unset($ed->updated_at);
                    }

                    unset($data['updated_at']);
                    unset($data['updated_at']);
                    unset($data['deleted_at']);
                    unset($data['applicant_id']);
                    unset($data['job_circular_id']);
//                return $data;
//                return $data['profile_pic'];
                    $profile_pic = storage_path('data' . DIRECTORY_SEPARATOR . 'photo');
                    $sign_pic = storage_path('data' . DIRECTORY_SEPARATOR . 'signature');
                    if (!File::exists($profile_pic)) File::makeDirectory($profile_pic);
                    if (!File::exists($sign_pic)) File::makeDirectory($sign_pic);
                    if ($data['profile_pic']&&File::exists($data['profile_pic'])) {
                        if (!File::move($data['profile_pic'], $profile_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg')) {
                            throw new \Exception("Can`t move image. please try again later");
                        }

                    }
                    $data['profile_pic'] = 'data/photo/' . $ansar_id . '.jpg';
                    if ($data['sign_pic']&&File::exists($data['sign_pic'])) {
                        if (!File::move($data['sign_pic'], $sign_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg')) {
                            throw new \Exception("Can`t move image. please try again later");
                        }

                    }
                    $data['sign_pic'] = 'data/signature/' . $ansar_id . '.jpg';
                    $data['verified'] = 0;
                    $ansar_new = new PersonalInfo(json_decode(json_encode($data), true));
                    foreach ($training_info as $training) {
                        $ansar_new->training()->save(new TrainingInfo((array)$training));
                    }
                    foreach ($education_info as $education) {
                        $ansar_new->education()->save(new Edication((array)$education));
                    }
                    foreach ($nominee_info as $nominee) {
                        $ansar_new->nominee()->save(new Nominee((array)$nominee));
                    }
                    $ansar_new->status()->save(new AnsarStatusInfo());
                    $ansar_new->save();
                    $applicant_hrm_details->save();
                    $applicant_hrm_details->restore();

                    DB::connection('hrm')->commit();
                    DB::commit();
                    $response['success']++;


                } else {
                    throw new \Exception("Invalid request");
                }

            } catch (\Error $e) {
                Log::info($e->getMessage());
                Log::info($e->getTraceAsString());
                DB::connection('hrm')->rollback();
                DB::rollback();
                if(isset($sign_pic))$this->rollbackFile($sign_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['sign_pic']);
                if(isset($profile_pic))$this->rollbackFile($profile_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['profile_pic']);
                $response['fail']++;
            } catch (\Throwable $e) {
                Log::info($e->getMessage());
                Log::info($e->getTraceAsString());
                DB::connection('hrm')->rollback();
                DB::rollback();
                if(isset($sign_pic))$this->rollbackFile($sign_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['sign_pic']);
                if(isset($profile_pic))$this->rollbackFile($profile_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['profile_pic']);
                $response['fail']++;
            }catch (\Exception $e) {
                Log::info($e->getMessage());
                Log::info($e->getTraceAsString());
                DB::connection('hrm')->rollback();
                DB::rollback();
                if(isset($sign_pic))$this->rollbackFile($sign_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['sign_pic']);
                if(isset($profile_pic))$this->rollbackFile($profile_pic . DIRECTORY_SEPARATOR . $ansar_id . '.jpg', $applicant_hrm_details['profile_pic']);
                $response['fail']++;
            }
        }
        return response()->json(['status'=>'success','message'=>"Total ".count($request->hrmIds).". Success {$response['success']}. fail {$response['fail']}"]);
    }

    function rollbackFile($current_file, $old_file)
    {
        if (File::exists($current_file)) {
            try {
                File::move($current_file, $old_file);
            } catch (\Throwable $t) {

            }
        }
    }
}
