<?php

namespace App\modules\recruitment\subModule\training\Controllers;

use App\modules\recruitment\subModule\training\Models\TrainingCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TrainingCategoryController extends Controller
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
        return view("$this->module::training.category.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view("$this->module::training.category.create");
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
            'training_category_name_eng' => 'required',
            'training_category_name_bng' => 'required'
        ];
        $this->validate($request, $rules);
        if (!$request->exists('status')) $request['status'] = 'inactive';
        DB::connection($this->module)->beginTransaction();
        try {
            $c = TrainingCategory::create($request->all());
            DB::connection($this->module)->commit();

        } catch (\Exception $e) {
            DB::connection($this->module)->rollback();
            return redirect()->route("$this->module.category.index")->with('session_error', "An error occur while create new category. Please try agin later");
//            return redirect()->route('recruitment.category.index')->with('session_error',$e->getMessage());
        }
        return redirect()->route("$this->module.category.index")->with('session_success', "New category added successfully");
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
        $data = TrainingCategory::find($id);
        return view("$this->module::training.category.edit", compact('data'));
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
            'training_category_name_eng' => 'required',
            'training_category_name_bng' => 'required'
        ];
        $this->validate($request, $rules);
        if (!$request->exists('status')) $request['status'] = 'inactive';
        DB::beginTransaction();
        try {
            $c = TrainingCategory::find($id);
            $c->update($request->all());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
//            return redirect()->route('recruitment.category.index')->with('session_error',"An error occur while create new category. Please try agin later");
            return redirect()->route("$this->module.category.index")->with('session_error', $e->getMessage());
        }
        return redirect()->route("$this->module.category.index")->with('session_success', "Category updated successfully");
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
                $data = TrainingCategory::where(function ($query) use ($request) {
                    $query->orWhere('training_category_name_eng', 'like', "%{$request->q}%");
                    $query->orWhere('training_category_name_bng', 'like', "%{$request->q}%");
                    $query->orWhere('training_category_description', 'like', "%{$request->q}%");
                });
            }
        }
        if ($request->exists('status') && $request->status != 'all') {
            if (!$data) {
                $data = TrainingCategory::where('status', $request->status);
            } else {
                $data->where('status', $request->status);
            }
        }
        if ($data) return response()->json($data->get());
        else return response()->json(TrainingCategory::all());
    }
}