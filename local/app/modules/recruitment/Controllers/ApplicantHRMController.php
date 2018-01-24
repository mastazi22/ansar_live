<?php

namespace App\modules\recruitment\Controllers;

use App\modules\HRM\Models\Designation;
use App\modules\recruitment\Models\JobApplicantHRMDetails;
use App\modules\recruitment\Models\JobAppliciantEducationInfo;
use App\modules\recruitment\Models\JobEducationInfo;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
}
