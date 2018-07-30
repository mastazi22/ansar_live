<?php

namespace App\modules\AVURP\Controllers;

use App\modules\AVURP\Models\KpiInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class KpiInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $kpi_infos = KpiInfo::with(['division','unit','thana']);
            if($request->range&&$request->range!="all"){
                $kpi_infos->where('division_id',$request->range);
            }
            if($request->unit&&$request->unit!="all"){
                $kpi_infos->where('unit_id',$request->unit);
            }
            if($request->thana&&$request->thana!="all"){
                $kpi_infos->where('thana_id',$request->thana);
            }
            $limit = $request->limit?$request->limit:30;
            $kpi_infos = $kpi_infos->paginate($limit);
            return view('AVURP::kpi.data',compact('kpi_infos'));
        }
        return view('AVURP::kpi.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('AVURP::kpi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->ajax()){
            $rules = [
              "kpi_name"=>"required|unique:avurp.avurp_kpi_info",
              "division_id"=>"required",
              "unit_id"=>"required",
              "thana_id"=>"required",
            ];
            $this->validate($request,$rules);
            DB::connection("avurp")->beginTransaction();
            try{
                $kpi = KpiInfo::create($request->except(['action_user_id']));
                DB::connection("avurp")->commit();
            }catch(\Exception $e){
                DB::connection("avurp")->rollback();
                return response()->json(['message'=>$e->getMessage()],500);
            }
            Session::flash('success_message', 'New KPI added successfully');
            return response()->json([]);
        }
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
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if($request->ajax()){
            $kpi = KpiInfo::find($id);
            return response()->json($kpi);
        }
        return view('AVURP::kpi.edit',compact('id'));
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
            "kpi_name"=>"required|unique:avurp.avurp_kpi_info,kpi_name,$id",
            "division_id"=>"required",
            "unit_id"=>"required",
            "thana_id"=>"required",
        ];
        $this->validate($request,$rules);
        DB::connection("avurp")->beginTransaction();
        try{
            $kpi = KpiInfo::findOrFail($id);
            $kpi->update([
                "kpi_name"=>$request->kpi_name,
                "division_id"=>$request->division_id,
                "unit_id"=>$request->unit_id,
                "thana_id"=>$request->thana_id,
                "address"=>$request->address,
                "contact_no"=>$request->contact_no,
            ]);
            DB::connection("avurp")->commit();
        }catch(\Exception $e){
            DB::connection("avurp")->rollback();
            return response()->json(['message'=>$e->getMessage()],500);
        }
        Session::flash('success_message', 'KPI info updated successfully');
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
    public function kpiList(Request $request)
    {
        $d = $request->division;
        $u = $request->unit;
        $t = $request->thana;
        $kpis = KpiInfo::select('id','kpi_name');
        if($d&&$d!='all'){
            $kpis->where('division_id',$d);
        }
        if($u&&$u!='all'){
            $kpis->where('unit_id',$u);
        }
        if($t&&$t!='all'){
            $kpis->where('thana_id',$t);
        }
        return $kpis->get();
    }

}
