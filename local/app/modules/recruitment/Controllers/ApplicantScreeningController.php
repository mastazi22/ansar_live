<?php

namespace App\modules\recruitment\Controllers;

use App\modules\recruitment\Models\JobCircular;
use function foo\func;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ApplicantScreeningController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cicular_summery = JobCircular::with('category')->withCount([
                'appliciant',
                'appliciantMale',
                'appliciantFemale',
                'appliciantPaid'
            ]);
            if($request->exists('category')&&$request->category!='all'){
                $cicular_summery->where('id',$request->category);
            }
            if($request->exists('circular')&&$request->circular!='all'){
                $cicular_summery->where('id',$request->circular);
            }
            if($request->exists('status')&&$request->status!='all'){
                $cicular_summery->where('status',$request->status);
                /*$cicular_summery->where(function($q) use($request){
                    $q->whereHas('category',function($q) use($request){
                        $q->where('status',$request->status);
                    });
                    $q->orWhere('status',$request->status);
                });*/
            }
            return response()->json($cicular_summery->get());
        }
        return view('recruitment::applicant.index');
    }
}
