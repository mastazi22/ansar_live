<?php

namespace App\modules\recruitment\subModule\training\Controllers;

use App\modules\recruitment\subModule\training\Models\TrainingCourses;
use App\modules\recruitment\subModule\training\Models\TrainingSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TrainingSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    private $module = "recruitment.training";
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        return view("$this->module::training.session.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $training_course = TrainingCourses::pluck('course_name', 'id')->prepend('--Select a training course--', '');
        return view("$this->module::training.session.create",compact('training_course'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       /* $rules = [
            'training_session_name_eng' => 'required',
            'training_session_name_bng' => 'required'
        ];
        $this->validate($request, $rules);
        if (!$request->exists('status')) $request['status'] = 'inactive';
        DB::connection($this->module)->beginTransaction();
        try {
            $c = TrainingCategory::create($request->all());
            DB::connection($this->module)->commit();

        } catch (\Exception $e) {
            DB::connection($this->module)->rollback();
            return redirect()->route("$this->module.session.index")->with('session_error', "An error occur while create new session. Please try agin later");
//            return redirect()->route('recruitment.session.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route("$this->module.session.index")->with('session_success', "New session added successfully");
    */}

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return void
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
        $training_course = TrainingCourses::pluck('course_name', 'id')->prepend('--Select a training course--', '');
        $data = TrainingSession::find($id);
        return view("$this->module::training.session.edit", compact('data','training_course'));
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
            'session_name' => 'required',
            'pay_amount' => 'required',
            'training_course_id' => 'required|regex:/^[1-9]?[1-9]+$/',
            'start_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/'],
            'end_date' => ['required', 'regex:/^[0-9]{2}-[A-Za-z]{3}-[0-9]{4}$/']
        ];
        $this->validate($request, $rules);
        DB::connection($this->module)->beginTransaction();
        try {
            $request['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $request['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            if (!$request->exists('auto_terminate')) $request['auto_terminate'] = '0';
            $request['payment_status'] = !$request->payment_status ? 'off' : $request->payment_status;
            $request['login_status'] = !$request->login_status ? 'off' : $request->login_status;
            $request['application_status'] = !$request->application_status ? 'off' : $request->application_status;
            $request['session_status'] = !$request->session_status ? 'off' : $request->session_status;
            $c = TrainingSession::find($id);
            $c->update($request->all());
            DB::connection($this->module)->commit();

        } catch (\Exception $e) {
            DB::connection($this->module)->rollBack();
//            return redirect()->route('recruitment.circular.index')->with('session_error',"An error occur while updating circular. Please try again later");
            return redirect()->route("$this->module.session.index")->with('session_error', $e->getMessage());
        }
        return redirect()->route("$this->module.session.index")->with('session_success', "Data updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    private function getData($request)
    {
        $data = '';
        if ($request->exists('q') && $request->q) {
            if (!$data) {
                $data = TrainingSession::with("course")->where(function ($query) use ($request) {
                    $query->where('session_name', 'like', "%{$request->q}%");
                });
            }
        }
        /*if ($request->exists('status') && $request->status != 'all') {
            if (!$data) {
                $data = TrainingCategory::where('status', $request->status);
            } else {
                $data->where('status', $request->status);
            }
        }*/
        if ($data) return response()->json($data->get());
        else return response()->json(TrainingSession::with("course")->get());
    }
}
