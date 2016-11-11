<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\Division;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return View::make('HRM::Division.index',['data'=>Division::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return View::make('HRM::Division.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $rules = [
          'division_name_eng'=>'required',
          'division_name_bng'=>'required',
          'division_code'=>'required',
        ];
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
//            return $valid->messages()->toJson();
            return Redirect::back()->withErrors($valid)->withInput($request->all());
        }
        Division::create($request->all());
        return Redirect::route('HRM.range.index')->with('success','Division create complete');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return View::make('HRM::Division.edit',['data'=>Division::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $rules = [
            'division_name_eng'=>'required',
            'division_name_bng'=>'required',
            'division_code'=>'required',
        ];
        $valid = Validator::make($request->all(),$rules);
        if($valid->fails()){
            return Redirect::back()->withError($valid)->withInput($request->all());
        }
        Division::find($id)->update($request->all());
        return Redirect::route('HRM.range.index')->with('success','Range update complete');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
