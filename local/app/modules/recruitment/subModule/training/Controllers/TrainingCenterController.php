<?php

namespace App\modules\recruitment\subModule\training\Controllers;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\recruitment\subModule\training\Models\TrainingCenter;
use App\modules\recruitment\subModule\training\Models\TrainingCenterQuota;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TrainingCenterController extends Controller
{
    private $module = "recruitment.training";
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        return view("$this->module::training.center.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $ranges = Division::where('id','<>',0)->get();
        $units = District::where('id','<>',0)->get();
        return view("$this->module::training.center.create",compact('ranges','units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request->all();
        $rules = [
            'center_name' => 'required',
            'trainee_divisions.*' => 'required|regex:/^[0-9]+$/',
            'trainee_units.*' => 'required|regex:/^[0-9]+$/',
            'quota.*.unit_id' => 'required|regex:/^[0-9]+$/',
            'quota.*.no_of_quota' => 'required|regex:/^[0-9]+$/',
            'division_id' => 'required|regex:/^[0-9]+$/',
            'unit_id' => 'required|regex:/^[0-9]+$/',
            'thana_id' => 'required|regex:/^[0-9]+$/',
        ];
        $this->validate($request, $rules);
        DB::connection($this->module)->beginTransaction();
        try {
            $data = $request->except(["quota"]);
            $data["trainee_divisions"] = implode(",",$data["trainee_divisions"]);
            $data["trainee_units"] = implode(",",$data["trainee_units"]);
            $tc = TrainingCenter::create($data);
            $models = [];
            foreach ($request->quota as $q){
                array_push($models,new TrainingCenterQuota($q));
            }
            $tc->quota()->saveMany($models);
            DB::connection($this->module)->commit();

        } catch (\Exception $e) {
            DB::connection($this->module)->rollback();
            return response()->json(["status"=>false,"message"=>$e->getMessage()]);
//            return redirect()->route('recruitment.courses.index')->with('session_error',$e->getMessage());
        }
        Session::flush('session_success', "New center added successfully");
        return response()->json(["status"=>true]);
    }

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
        $data = TrainingCenter::find($id);
        $ranges = Division::where('id','<>',0)->get();
        $units = District::where('id','<>',0)->get();
        return view("$this->module::training.center.edit", compact('data','ranges','units'));
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
            'training_courses_name_eng' => 'required',
            'training_courses_name_bng' => 'required'
        ];
        $this->validate($request, $rules);
        if (!$request->exists('status')) $request['status'] = 'inactive';
        DB::beginTransaction();
        try {
            $c = TrainingCenter::find($id);
            $c->update($request->all());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
//            return redirect()->route('recruitment.courses.index')->with('session_error',"An error occur while create new courses. Please try agin later");
            return redirect()->route("$this->module.courses.index")->with('session_error', $e->getMessage());
        }
        return redirect()->route("$this->module.courses.index")->with('session_success', "Category updated successfully");
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
                $data = TrainingCenter::with(['division','unit','thana'])->where(function ($query) use ($request) {
                    $query->where('center_name', 'like', "%{$request->q}%");
                });
            }
        }
        if ($data) return response()->json($data->get());
        else return response()->json(TrainingCenter::with(['division','unit','thana'])->get());
    }
}
