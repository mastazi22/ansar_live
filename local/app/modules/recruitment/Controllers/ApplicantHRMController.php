<?php

namespace App\modules\recruitment\Controllers;

use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\Designation;
use App\modules\HRM\Models\Edication;
use App\modules\HRM\Models\Nominee;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\TrainingInfo;
use App\modules\recruitment\Models\JobApplicantHRMDetails;
use App\modules\recruitment\Models\JobAppliciantEducationInfo;
use App\modules\recruitment\Models\JobEducationInfo;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ApplicantHRMController extends Controller
{
    //
    public function index(Request $request){
        if(strcasecmp($request->method(),'post')==0){
            if($request->ajax()){
                $applicants= JobApplicantHRMDetails::with(['division','district','thana'])
                    ->where('job_circular_id',$request->circular);
                if($request->range&&$request->range!='all'){
                    $applicants->where('division_id',$request->range);
                }
                if($request->unit&&$request->unit!='all'){
                    $applicants->where('unit_id',$request->unit);
                }
                if($request->thana&&$request->thana!='all'){
                    $applicants->where('thana_id',$request->thana);
                }
                if($request->q){
                    $applicants->where(function ($q)use ($request){
                        $q->where('ansar_name_eng','LIKE','%'.$request->q.'%');
                        $q->orWhere('ansar_name_bng','LIKE','%'.$request->q.'%');
                        $q->orWhere('mobile_no_self',$request->q);
                        $q->orWhere('national_id_no',$request->q);
                    });
                }
                $limit = $request->limit?$request->limit:50;
                return view('recruitment::hrm.part_hrm_applicant_info',['applicants'=>$applicants->paginate($limit)]);
            }
            else abort(403);
        }
        return view('recruitment::hrm.applicant_details_for_hrm');
    }
    public function applicantEditForHRM($type,$circular_id,$id){
        $ansarAllDetails = JobApplicantHRMDetails::with(['division','district','thana','skill','disease','designation','bloodGroup'])
            ->where('id',$id)
            ->where('job_circular_id',$circular_id)
            ->first();
        $ranks = Designation::all();
        $educations = JobEducationInfo::all();
//        return $educations->where('id',intval('7'))->first();
        if($type=='download') {
            $pdf = SnappyPdf::loadView('recruitment::hrm.hrm_form_details', compact('ansarAllDetails','ranks','educations'))
                ->setOption('encoding', 'UTF-8')
                ->setOption('zoom', 0.73);
            return $pdf->download();
        }else if($type=='view'){
            $pdf = SnappyPdf::loadView('recruitment::hrm.hrm_form_details', compact('ansarAllDetails','ranks','educations'))
                ->setOption('encoding', 'UTF-8')
                ->setOption('zoom', 0.73);
            return $pdf->stream();
        }
//        return view('recruitment::hrm.hrm_form_download',['ansarAllDetails'=>$applicant]);
    }
    public function moveApplicantToHRM($id){

        DB::beginTransaction();
        try{
            $applicant_hrm_details = JobApplicantHRMDetails::find($id);
            if($applicant_hrm_details){
                $data = clone $applicant_hrm_details;
                $ansar_id = intval(PersonalInfo::orderBy('ansar_id','desc')->first()->ansar_id)+1;
                $applicant_hrm_details['ansar_id'] = $data['ansar_id'] = $ansar_id;
                $education_info = $data['appliciant_education_info'];
                $training_info = $data['applicant_training_info'];
                $nominee_info = $data['applicant_nominee_info'];
                unset($data['appliciant_education_info']);
                unset($data['applicant_training_info']);
                unset($data['applicant_nominee_info']);
                foreach($education_info as $ed){
                    $ed->education_id = $ed->job_education_id;
                    unset($ed->job_education_id);
                    unset($ed->job_applicant_id);
                    unset($ed->created_at);
                    unset($ed->updated_at);
                }

                unset($data['updated_at']);
                unset($data['updated_at']);
                unset($data['applicant_id']);
                unset($data['job_circular_id']);
                $profile_pic = storage_path('data/photo');
                $sign_pic = storage_path('data/signature');
                if(!File::exists($profile_pic)) File::makeDirectory($profile_pic);
                if(!File::exists($sign_pic)) File::makeDirectory($sign_pic);
                if($data['profile_pic']){
                    if(!File::move($data['profile_pic'],$profile_pic.'/'.$ansar_id.'.jpg')){
                        throw new \Exception("Can`t move image. please try again later");
                    }

                }
                $data['profile_pic'] = 'data/photo/'.$ansar_id.'.jpg';
                if($data['sign_pic']){
                    if(!File::move($data['sign_pic'],$sign_pic.'/'.$ansar_id.'.jpg')){
                        throw new \Exception("Can`t move image. please try again later");
                    }
                    File::delete($data['sign_pic']);

                }
                $data['sign_pic'] = 'data/signature/'.$ansar_id.'.jpg';
                $data['verified'] = 0;
                $ansar_new = PersonalInfo::create($data);
                $status = $ansar_new->status()->save(new AnsarStatusInfo());
                $training = $ansar_new->training()->save(new TrainingInfo($training_info));
                $education = $ansar_new->education()->save(new Edication($education_info));
                $nominee = $ansar_new->nominee()->save(new Nominee($nominee_info));
                if($status&&$training&&$education&&$nominee){
                    return response()->json(['status'=>'success','message'=>'Ansar move to HRM successfully']);
                }
                throw new \Exception("An error occur while moving. Please try again later");


            } else{
                throw new \Exception("Invalid request");
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status'=>'error','message'=>$e->getMessage()]);
        }
    }
}
