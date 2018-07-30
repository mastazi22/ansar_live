<?php

namespace App\modules\AVURP\Controllers;

use App\Http\Controllers\Controller;
use App\modules\AVURP\Models\KpiInfo;
use App\modules\AVURP\Models\Memorandum;
use App\modules\AVURP\Models\VDPAnsarInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmbodimentController extends Controller
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
            $rules = [
                "unit" => 'required',
            ];
            $this->validate($request, $rules);
            $offered_ansar = VDPAnsarInfo::with(['offer'])->whereHas('offer', function ($q) use ($request) {
                $q->where('unit_id', $request->unit);
                $q->where('sms_receive_status', 'yes');
            })->whereHas('status', function ($q) use ($request) {
                $q->where('offer_sms_status', 1);
            })->paginate(50);
            return view("AVURP::embodiment.data", compact('offered_ansar'));
        }
        return view('AVURP::embodiment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                'mem.mem_id' => 'required|unique:avurp.avurp_mem_id,mem_id',
                'mem.mem_date' => 'required',
                'shortKpi' => 'required',
                'duration' => 'required|regex:/^[0-9]+$/',
                'joining_date' => 'required',
                'ids.*' => 'required|regex:/^[0-9]+$/|exists:avurp.avurp_vdp_ansar_info,id'
            ];
            $this->validate($request, $rules);
            DB::connection('avurp')->beginTransaction();
            try {
                $mem = Memorandum::create([
                    'mem_id' => $request->mem["mem_id"],
                    'mem_date' => $request->mem["mem_date"],
                ]);
                $vdps = VDPAnsarInfo::with(['offer', 'status'])->whereIn('id', $request->ids)->get();
                $kpi = KpiInfo::find($request->shortKpi);
                foreach ($vdps as $vdp) {
                    if ($vdp->offer && $vdp->offer->unit_id != $kpi->unit_id) {
                        throw new \Exception("Invalid vdp selected");
                    }
                    $data = [
                        'embodied_date' => Carbon::parse($request->joining_date)->format("Y-m-d"),
                        'kpi_id' => $kpi->id,
                        'duration' => $request->duration,
                        'mem_id' => $mem->id,
                        'sms_id' => $vdp->offer->id,
                        'service_ended_date' => Carbon::parse($request->joining_date)->addDays($request->duration)->format("Y-m-d")

                    ];
                    $vdp->embodiment()->create($data);
                    $vdp->offer()->delete();
                    $vdp->status()->update([
                        'offer_sms_status' => 0,
                        'embodied_status' => 1,
                        'retire_status' => 0,
                        'dead_status' => 0
                    ]);

                }
                DB::connection('avurp')->commit();
            } catch (\Exception $e) {
                DB::connection('avurp')->rollback();
                return response()->json(['status' => "error", 'message' => $e->getMessage()]);
            }
            return response()->json(['status' => "success", 'message' => "Embodiment complete successfully"]);
        }
        return abort(403);
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
        //
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
    }

    public function selectAll(Request $request)
    {
        if ($request->ajax()) {
            $ids = VDPAnsarInfo::whereHas('offer', function ($q) use ($request) {
                $q->where('unit_id', $request->unit);
                $q->where('sms_receive_status', 'yes');
            })->whereHas('status', function ($q) use ($request) {
                $q->where('offer_sms_status', 1);
            })->pluck('id');
            return response()->json(compact('ids'));
        }
        return abort(403);
    }
}
