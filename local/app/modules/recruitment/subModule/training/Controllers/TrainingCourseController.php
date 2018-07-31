<?php

namespace App\modules\recruitment\subModule\training\Controllers;

use App\modules\recruitment\subModule\training\Models\TrainingCategory;
use App\modules\recruitment\subModule\training\Models\TrainingCenter;
use App\modules\recruitment\subModule\training\Models\TrainingCourseCenter;
use App\modules\recruitment\subModule\training\Models\TrainingCourses;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TrainingCourseController extends Controller
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
        return view("$this->module::training.courses.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = TrainingCategory::pluck('training_category_name_bng','id');
        $categories->prepend('--Select a category--','');
        $centers = TrainingCenter::all();
        return view("$this->module::training.courses.create",compact('categories','centers'));
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
            'course_name' => 'required',
            'course_category_id' => 'required',
            'course_center_ids' => 'required',
            'course_center_ids.*' => 'required|regex:/^[0-9]+$/',
        ];
        $this->validate($request, $rules);
        DB::connection($this->module)->beginTransaction();
        try {
            $tc = TrainingCourses::create($request->except(['course_center_ids']));
            $models = [];
            foreach ($request->course_center_ids as $center_id){
                array_push($models,new TrainingCourseCenter(compact('center_id')));
            }
            $tc->courseCenter()->saveMany($models);
            DB::connection($this->module)->commit();

        } catch (\Exception $e) {
            DB::connection($this->module)->rollback();
            return redirect()->route("$this->module.courses.index")->with('session_error', "An error occur while create new courses. Please try agin later");
//            return redirect()->route('recruitment.courses.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route("$this->module.courses.index")->with('session_success', "New courses added successfully");
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
        //
        $categories = TrainingCategory::pluck('training_category_name_bng','id');
        $categories->prepend('--Select a category--','');
        $centers = TrainingCenter::all();
        $data = TrainingCourses::find($id);
        return view("$this->module::training.courses.edit", compact('data','categories','centers'));
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
            $c = TrainingCourses::find($id);
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
                $data = TrainingCourses::with('category')->where(function ($query) use ($request) {
                    $query->orWhere('course_name', 'like', "%{$request->q}%");
                });
            }
        }
        if ($data) return response()->json($data->get());
        else return response()->json(TrainingCourses::with(['category','center'])->get());
    }
}
