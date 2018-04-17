<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\Unions;
use App\modules\HRM\Repositories\data\DataInterface;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UnionController extends Controller
{
    private $dataRepo;

    /**
     * FormSubmitHandler constructor.
     */
    public function __construct(DataInterface $dataRepo)
    {
        $this->dataRepo = $dataRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if($request->ajax()){
            $limit = $request->limit?$request->limit:30;
            $range = $request->division_id?$request->division_id:'all';
            $unit = $request->unit_id?$request->unit_id:'all';
            $thana = $request->thana_id?$request->thana_id:'all';
            $unions = Unions::with(['division','unit','thana']);
            if($range!='all'){
                $unions->where('division_id',$range);
            }
            if($unit!='all'){
                $unions->where('unit_id',$unit);
            }
            if($thana!='all'){
                $unions->where('thana_id',$thana);
            }
            $unions = $unions->paginate($limit);
            return view('HRM::unions.data',compact('unions'));
        }
        return view('HRM::unions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('HRM::unions.create');
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
            'union_name_eng'=>'required',
            'union_name_bng'=>'required',
            'code'=>'required',
            'division_id'=>'required',
            'unit_id'=>'required',
            'thana_id'=>'required',
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        try{
            Unions::create($request->all());
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['message'=>$e->getMessage()],500);
        }
        Session::flash('success_message','Union added successfully');
        return response()->json([]);
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
    public function edit(Request $request,$id)
    {
        if($request->ajax()){
            try{
                $union = Unions::findOrFail($id);
            }catch(\Exception $e){
                return response()->json(['message'=>$e->getMessage()],500);
            }
            return $union;
        }
        return view('HRM::unions.edit',compact('id'));
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
        $rules = [
            'union_name_eng'=>'required',
            'union_name_bng'=>'required',
            'code'=>'required',
            'division_id'=>'required',
            'unit_id'=>'required',
            'thana_id'=>'required',
        ];
        $this->validate($request,$rules);
        DB::beginTransaction();
        DB::connection('avurp')->beginTransaction();
        try{
            $union = Unions::findOrFail($id);
            $union->update($request->all());
            DB::connection('avurp')->table('avurp_vdp_ansar_info')->where('union_id',$id)->update($request->only(['division_id','unit_id','thana_id']));
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            DB::connection('avurp')->rollback();
            Log::info($e->getTraceAsString());
            return response()->json(['message'=>$e->getMessage()],500);
        }
        Session::flash('success_message','Union added successfully');
        return response()->json([]);
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
    public function showAll(Request $request)
    {
        //

        return response()->json($this->dataRepo->getUnions($request->division_id,$request->unit_id,$request->thana_id));
    }

}
