<?php

namespace App\modules\recruitment\Controllers;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\recruitment\Models\JobApplicantMarks;
use App\modules\recruitment\Models\JobApplicantQuota;
use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\JobCircular;
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
                'range'=>'regex:/^[0-9]+$/',
                'unit'=>'regex:/^[0-9]+$/',
                'circular'=>'required|regex:/^[0-9]+$/',
            ];
//            return $request->all();
            $this->validate($request,$rules);
            $category_type = JobCircular::find($request->circular)->category->category_type;
            $applicants = JobApplicantMarks::with(['applicant'=>function($q){
                $q->with(['appliciantEducationInfo'=>function($q){
                    $q->with('educationInfo');
                },'district','thana']);
            }])->whereHas('applicant',function($q) use($request){

                $q->whereHas('accepted',function(){

                })->where('status','accepted')->where('job_circular_id',$request->circular);
                if($request->unit){
                    $q->where('unit_id',$request->unit);
                }
                if($request->range){
                    $q->where('division_id',$request->range);
                }
            })->select(DB::raw('*,(IFNULL(written,0)+IFNULL(viva,0)+IFNULL(physical,0)+IFNULL(edu_training,0)+IFNULL(edu_experience,0)+IFNULL(physical_age,0)) as total_mark'))->orderBy('is_bn_candidate','desc')->orderBy('specialized','desc')->orderBy('total_mark','desc');
            /*$applicants = JobAppliciant::with(['appliciantEducationInfo'=>function($q){
                $q->with('educationInfo');
            },'district','marks'=>function($qq){
                $qq->select(DB::raw('*,(written+viva+physical+edu_training) as total_mark'));
            }])->whereHas('accepted',function(){

            })->where('status','accepted')->where('job_circular_id',$request->circular)->where('unit_id',$request->unit);
//            return $applicants->get();*/
            if($request->unit){
                $pdf = SnappyPdf::loadView('recruitment::reports.accepted_list',[
                    'applicants'=>$applicants->get(),
                    'unit'=>District::find($request->unit),
                    'type'=>$category_type
                ])
                    ->setPaper('a4')
                    ->setOption('footer-left',url('/'))
                    ->setOption('footer-right',Carbon::now()->format('d-M-Y H:i:s'))
                    ->setOrientation('landscape');
            } else{
                $pdf = SnappyPdf::loadView('recruitment::reports.accepted_list',[
                    'applicants'=>$applicants->get(),
                    'range'=>Division::find($request->range),
                    'type'=>$category_type
                ])
                    ->setPaper('a4')
                    ->setOption('footer-left',url('/'))
                    ->setOption('footer-right',Carbon::now()->format('d-M-Y H:i:s'))
                    ->setOrientation('landscape');
            }
            return $pdf->download();
            /*return view('recruitment::reports.accepted_list',[
                'applicants'=>$applicants->get(),
                'unit'=>District::find($request->unit)
            ]);*/
        }
        return view('recruitment::reports.applicant_accepted_report');
    }
    public function applicantMarksReport(Request $request){
        if(strcasecmp($request->method(),'post')==0){
            $rules = [
                'circular'=>'required|regex:/^[0-9]+$/',
            ];
            $this->validate($request,$rules);
//            DB::enableQueryLog();
            $applicants = JobAppliciant::with(['marks'=>function($q){
                $q->select(DB::raw('*,(written+viva+physical+edu_training+ physical_age) as total_mark'));
            },'district','circular.markDistribution','thana'])->whereHas('marks',function ($q){
            })->where('job_circular_id',$request->circular);


            if($request->exists('unit')&&$request->unit!='all'){
                $applicants->where('unit_id',$request->unit);
            }

            if($request->exists('range')&&$request->range!='all'){
                $applicants->where('division_id',$request->range);
            }
            $applicants = $applicants->orderBy('unit_id')->orderBy('thana_id')->get();

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
        $category_type = JobCircular::find($request->circular)->category->category_type;
//        return $category_type;
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
        Excel::create('applicant_list('.$request->status.')',function ($excel) use($applicants,$request,$category_type){

            $excel->sheet('sheet1',function ($sheet) use ($applicants,$request,$category_type){
                $sheet->setAutoSize(false);
                $sheet->setWidth('A', 5);
                $sheet->loadView('recruitment::reports.excel_data',['index'=>((intval($request->page)-1)*300)+1,'applicants'=>$applicants->skip((intval($request->page)-1)*300)->limit(300)->get(),'status'=>$request->status,'ctype'=>$category_type]);
            });
        })->download('xls');
    }
}
