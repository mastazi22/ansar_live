<?php

namespace App\modules\SD\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\SD\Models\DemandConstant;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SDController extends Controller
{
    public function index()
    {
        return view('SD::index');
    }

    public function demandSheet()
    {
        return view('SD::demand_sheet');
    }

    public function attendanceSheet()
    {
        return "This is attendance sheet";
    }

    public function demandConstant()
    {
        return view("SD::demand_constant")->with(['constants' => DemandConstant::all()]);
    }

    public function salarySheet()
    {
        return "This is salary sheet";
    }

    public function updateConstant(Request $request)
    {
        $rules = [];
        $messages = [
            'required' => 'This field can`t be empty',
            'numeric' => 'This field must be numeric',
            'min' => 'Value must be greater then 0'
        ];
        foreach ($request->except(['_token']) as $key => $value) {
            $rules[$key] = 'required|numeric|min:1';
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('SD/demandconstant')->withErrors($validator)->withInput($request->except(['_token']));
        }
        $demandConstant = new DemandConstant();
        $demandConstant->where('cons_name', 'ration_fee')->update(['cons_value' => $request->get('ration_fee')]);
        $demandConstant->where('cons_name', 'barber_and_cleaner_fee')->update(['cons_value' => $request->get('barber_and_cleaner_fee')]);
        $demandConstant->where('cons_name', 'transportation')->update(['cons_value' => $request->get('transportation')]);
        $demandConstant->where('cons_name', 'medical_fee')->update(['cons_value' => $request->get('medical_fee')]);
        $demandConstant->where('cons_name', 'margha_fee')->update(['cons_value' => $request->get('margha_fee')]);
        $demandConstant->where('cons_name', 'per_day_salary_ansar')->update(['cons_value' => $request->get('per_day_salary_ansar')]);
        $demandConstant->where('cons_name', 'per_day_salary_pc_and_apc')->update(['cons_value' => $request->get('per_day_salary_pc_and_apc')]);
        // return ['statys'=>$demandConstant->save()];
        return Redirect::to('SD/demandconstant')->with('constant_update_success', 'Demand constant update successfully');


    }

    function test()
    {
        //return view('SD::test');
        return SnappyPdf::loadView('SD::test')->stream();
    }
}
