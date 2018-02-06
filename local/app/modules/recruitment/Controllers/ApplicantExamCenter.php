<?php

namespace App\modules\recruitment\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\District;
use App\modules\recruitment\Models\JobApplicantExamCenter;
use App\modules\recruitment\Models\JobCircular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicantExamCenter extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JobApplicantExamCenter::with('circular');
            if ($request->circular) {
                $data->where('job_circular_id', $request->circular);
            }
//            return $data->get();
            return view('recruitment::exam_center.data', ['data' => $data->get()]);

        }
        return view('recruitment::exam_center.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $circulars = JobCircular::pluck('circular_name', 'id');
        $units = District::all();
        return view('recruitment::exam_center.create', compact('circulars', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'job_circular_id' => 'required|numeric|exists:job_circular,id',
            'selection_date' => 'required',
            'selection_place' => 'required',
            'selection_units' => 'required',
            'written_viva_date' => 'required',
            'written_viva_place' => 'required',
            'written_viva_units' => 'required',
        ];
        $this->validate($request, $rules);

        DB::beginTransaction();
        try{
            $input = $request->except(['search_unit']);
            JobApplicantExamCenter::create($input);
            DB::commit();
            return redirect()->route('recruitment.exam-center.index')->with('session_success','Exam center created successfully');
        }catch(\Throwable $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Error $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data = JobApplicantExamCenter::find($id);
        $circulars = JobCircular::pluck('circular_name', 'id');
        $units = District::all();
        return view('recruitment::exam_center.edit', compact('circulars', 'units','data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'job_circular_id' => 'required|numeric|exists:job_circular,id',
            'selection_date' => 'required',
            'selection_place' => 'required',
            'selection_units' => 'required',
            'written_viva_date' => 'required',
            'written_viva_place' => 'required',
            'written_viva_units' => 'required',
        ];
        $this->validate($request, $rules);

        DB::beginTransaction();
        try{
            $input = $request->except(['search_unit']);
            $exam_center = JobApplicantExamCenter::findOrFail($id);
            $exam_center->update($input);
            DB::commit();
            return redirect()->route('recruitment.exam-center.index')->with('session_success','Exam center Updated successfully');
        }catch(\Throwable $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Error $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        DB::beginTransaction();
        try{
            $exam_center = JobApplicantExamCenter::findOrFail($id);
            $exam_center->delete();
            DB::commit();
            return redirect()->route('recruitment.exam-center.index')->with('session_success','Exam center Deleted successfully');
        }catch(\Throwable $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Error $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }
    }
}
