<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobAppliciant;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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
            $applicants = JobAppliciant::with(['division','district','thana'])->where('status',$request->status)
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
            return view('recruitment::reports.data',['applicants'=>$applicants->paginate(300)]);
        }
        return view('recruitment::reports.applicants_status_report');
    }

    public function exportData(Request $request){
        $rules=[
            'circular'=>'required|regex:/^[0-9]+$/',
            'status'=>'required',
            'page'=>'required|regex:/^[0-9]+$/'
        ];
        $this->validate($request,$rules);
        $applicants = JobAppliciant::with(['division','district','thana'])->where('status',$request->status)
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
                $sheet->loadView('recruitment::reports.excel_data',['index'=>((intval($request->page)-1)*300)+1,'applicants'=>$applicants->skip((intval($request->page)-1)*300)->limit(300)->get()]);
            });
        })->download('xls');
    }
}
