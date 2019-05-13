<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\OfferZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("HRM::OfferZone.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("HRM::OfferZone.create");
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
//            return $request->all();
            $rules = [
                'rangeId' => 'required|exists:tbl_division,id',
                'unitIds' => 'required',
                'offerZoneRangeIds' => 'required',
                'unitIds.*' => 'exists:tbl_units,id,division_id,' . $request->rangeId,
                'offerZoneRangeIds.*.offerZoneRangeId'=>'required|exists:tbl_division,id',
                'offerZoneRangeIds.*.offerZoneRangeUnits.*'=>'required|exists:tbl_units,id'
            ];
            $this->validate($request,$rules);
            DB::beginTransaction();
            try {

                $range_id = $request->rangeId;
                $unit_ids = $request->unitIds;
                $offerZoneRangeIds = $request->offerZoneRangeIds;
                foreach ($unit_ids as $unit_id){

                    foreach($offerZoneRangeIds as $zone){
                        $offer_zone_range_id = $zone['offerZoneRangeId'];
                        foreach ($zone['offerZoneRangeUnits'] as $offer_zone_unit_id){
                            $offerZone = OfferZone::where(compact('range_id','unit_id','offer_zone_range_id','offer_zone_unit_id'));
                            $offerZone->delete();
                            OfferZone::create(compact('range_id','unit_id','offer_zone_range_id','offer_zone_unit_id'));
                        }
                    }
                }
                DB::commit();


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status'=>true,'message'=>$e->getMessage()]);
            }
            return response()->json(['status'=>true,'message'=>'success']);
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
}
