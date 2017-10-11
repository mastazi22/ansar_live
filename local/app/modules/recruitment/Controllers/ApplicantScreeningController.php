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
                'appliciant as applicant_male' => function ($q) {
                    $q->where('gender', 'Male');
                },
                'appliciant as applicant_female' => function ($q) {
                    $q->where('gender', 'Female');
                }
            ]);
            if($request->exists('category')&&$request->category!='all'){
                $cicular_summery->where('id',$request->category);
            }
            if($request->exists('circular')&&$request->circular!='all'){
                $cicular_summery->where('id',$request->circular);
            }
            return response()->json($cicular_summery->get());
        }
        return view('recruitment::applicant.index');
    }
}
