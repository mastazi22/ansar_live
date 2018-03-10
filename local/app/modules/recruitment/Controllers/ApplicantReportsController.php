<?php

namespace App\modules\recruitment\Controllers;

use App\modules\HRM\Models\District;
use App\modules\recruitment\Models\JobApplicantMarks;
use App\modules\recruitment\Models\JobApplicantQuota;
use App\modules\recruitment\Models\JobAppliciant;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantReportsController extends Controller
{
    //
    public function applicantStatusReport(Request $request){

        if(strcasecmp($request->method(),'post')==0){
            $rules=[
                'circular'=>'required|regex:/^[0-9]+$/',
                'status'=>'required'
            ];
            $this->validate($request,$rules);
            $applicants = JobAppliciant::with(['division','district','thana','marks'])->where('status',$request->status)
                ->where('job_circular_id',$request->circular);
            if($request->exists('range')&&$request->range!='all'){
                $applicants->where('division_id',$request->range);
            }
            if($request->exists('unit')&&$request->unit!='all'){
                $applicants->where('unit_id',$request->unit);
            }
            if($request->exists('thana')&&$request->thana!='all'){
                $applicants->where('thana_id',$request->thana);
            }
            return view('recruitment::reports.data',['applicants'=>$applicants->paginate(300),'status'=>$request->status]);
        }
        return view('recruitment::reports.applicants_status_report');
    }
    public function applicantAcceptedListReport(Request $request){

        if(strcasecmp($request->method(),'post')==0){
            $rules = [
                'unit'=>'required|regex:/^[0-9]+$/',
                'circular'=>'required|regex:/^[0-9]+$/',
            ];
            $this->validate($request,$rules);
            $applicants = JobApplicantMarks::with(['applicant'=>function($q){
                $q->with(['appliciantEducationInfo'=>function($q){
                    $q->with('educationInfo');
                },'district','thana']);
            }])->whereHas('applicant',function($q) use($request){

                $q->whereHas('accepted',function(){

                })->where('status','accepted')->where('job_circular_id',$request->circular)->where('unit_id',$request->unit);
            })->select(DB::raw('*,(written+viva+physical+edu_training) as total_mark'))->orderBy('total_mark','desc');
            /*$applicants = JobAppliciant::with(['appliciantEducationInfo'=>function($q){
                $q->with('educationInfo');
            },'district','marks'=>function($qq){
                $qq->select(DB::raw('*,(written+viva+physical+edu_training) as total_mark'));
            }])->whereHas('accepted',function(){

            })->where('status','accepted')->where('job_circular_id',$request->circular)->where('unit_id',$request->unit);
//            return $applicants->get();*/
            $pdf = SnappyPdf::loadView('recruitment::reports.accepted_list',[
                'applicants'=>$applicants->get(),
                'unit'=>District::find($request->unit)
            ])
                ->setPaper('a4')
                ->setOption('footer-left',url('/'))
                ->setOption('footer-right',Carbon::now()->format('d-M-Y H:i:s'))
                ->setOrientation('landscape');
            return $pdf->download();
            /*return view('recruitment::reports.accepted_list',[
                'applicants'=>$applicants->get(),
                'unit'=>District::find($request->unit)
            ]);*/
        }
        return view('recruitment::reports.applicant_accepted_report');
    }
    public function applicantMarksReport(Request $request){
//        return $request->all();
        if(strcasecmp($request->method(),'post')==0){
            $rules = [
                'circular'=>'required|regex:/^[0-9]+$/',
            ];
            $this->validate($request,$rules);
//            DB::enableQueryLog();
            $applicants = JobAppliciant::with(['marks'=>function($q){
                $q->select(DB::raw('*,(written+viva+physical+edu_training) as total_mark'));
            },'district','circular.markDistribution'])->whereHas('marks',function ($q){

            })->where('job_circular_id',$request->circular);
            if($request->exists('unit')&&$request->unit!='all'){
                $applicants->where('unit_id',$request->unit);
            }
            if($request->exists('range')&&$request->range!='all'){
                $applicants->where('division_id',$request->range);
            }
            $applicants = $applicants->orderBy('unit_id')->get();
//            return $applicants;
//            return DB::getQueryLog();
            $excel = Excel::create('applicant_marks',function ($excel) use($applicants){
                $excel->sheet('sheet1',function ($sheet) use($applicants){
                    $sheet->loadView('recruitment::reports.marks_list',[
                        'applicants'=>$applicants
                    ]);
                });
            });
            return $excel->download('xls');
        }
        return view('recruitment::reports.applicant_marks_report');
    }

    public function exportData(Request $request){
        $rules=[
            'circular'=>'required|regex:/^[0-9]+$/',
            'status'=>'required',
            'page'=>'required|regex:/^[0-9]+$/'
        ];
        $this->validate($request,$rules);
        $applicants = JobAppliciant::with(['division','district','thana','marks'])->where('status',$request->status)
            ->where('job_circular_id',$request->circular);
        if($request->exists('range')&&$request->range!='all'){
            $applicants->where('division_id',$request->range);
        }
        if($request->exists('unit')&&$request->unit!='all'){
            $applicants->where('unit_id',$request->unit);
        }
        if($request->exists('thana')&&$request->thana!='all'){
            $applicants->where('thana_id',$request->thana);
        }
        Excel::create('applicant_list('.$request->status.')',function ($excel) use($applicants,$request){

            $excel->sheet('sheet1',function ($sheet) use ($applicants,$request){
                $sheet->setAutoSize(false);
                $sheet->setWidth('A', 5);
                $sheet->loadView('recruitment::reports.excel_data',['index'=>((intval($request->page)-1)*300)+1,'applicants'=>$applicants->skip((intval($request->page)-1)*300)->limit(300)->get(),'status'=>$request->status]);
            });
        })->download('xls');
    }
}
