<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $offerZones = OfferZone::select('*');
            if ($request->range != 'all') {
                $offerZones = OfferZone::where('range_id', $request->range);
            }
            if ($request->unit != 'all') {
                if (isset($offerZones)) {
                    $offerZones->where('unit_id', $request->unit);
                } else {
                    $offerZones = OfferZone::where('unit_id', $request->unit);
                }
            }
            $offerZones = collect($offerZones->get())->groupBy('range_id')->toArray();
            $datas = [];
            foreach ($offerZones as $key => $offer_zone) {

                $division = Division::find($key);

                $offer_zone = collect($offer_zone)->groupBy('unit_id')->toArray();
                foreach ($offer_zone as $k => $v) {
                    $data = [];
                    $data['range'] = $division ? $division->division_name_bng : 'n\a';
                    $district = District::find($k);
                    $data['unit'] = $district ? $district->unit_name_bng : 'n\a';
                    $data['unitId'] = $k;
                    $data['areas'] = [];
                    $v = collect($v)->groupBy('offer_zone_range_id')->toArray();
                    foreach ($v as $kk => $vv) {
                        $d = [];
                        $d['division'] = Division::find($kk);
                        $d['units'] = [];
                        foreach ($vv as $u) {
                            array_push($d['units'], District::find($u['offer_zone_unit_id']));
                        }
                        array_push($data['areas'], $d);
                    }
                    array_push($datas, $data);
                }
            }
            return $datas;
        }
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
                'offerZoneRangeIds.*.offerZoneRangeId' => 'required|exists:tbl_division,id',
                'offerZoneRangeIds.*.offerZoneRangeUnits.*' => 'required|exists:tbl_units,id'
            ];
            $this->validate($request, $rules);
            DB::beginTransaction();
            try {

                $range_id = $request->rangeId;
                $unit_ids = $request->unitIds;
                $offerZoneRangeIds = $request->offerZoneRangeIds;
                foreach ($unit_ids as $unit_id) {

                    foreach ($offerZoneRangeIds as $zone) {
                        $offer_zone_range_id = $zone['offerZoneRangeId'];
                        foreach ($zone['offerZoneRangeUnits'] as $offer_zone_unit_id) {
                            $offerZone = OfferZone::where(compact('range_id', 'unit_id', 'offer_zone_range_id', 'offer_zone_unit_id'));
                            $offerZone->delete();
                            OfferZone::create(compact('range_id', 'unit_id', 'offer_zone_range_id', 'offer_zone_unit_id'));
                        }
                    }
                }
                DB::commit();


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => true, 'message' => $e->getMessage()]);
            }
            return response()->json(['status' => true, 'message' => 'success']);
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
    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            $offer_zones = collect(OfferZone::where('unit_id', $id)->get())->groupBy('range_id');
            $dis = District::find($id);
            $key = $offer_zones->keys()->toArray()[0];
            $div = Division::find($key);
            $datas = [];
            $datas['unit'] = $dis ? $dis->unit_name_bng : 'n\a';
            $datas['range'] = $div ? $div->division_name_bng : 'n\a';
            $datas['unit_id'] = $dis ? $dis->id : 0;
            $datas['range_id'] = $div ? $div->id : 0;
            //offerZoneRangeIds;{offerZoneRangeId:'',offerZoneRangeUnits:[]}
            $datas['offerZoneRangeIds'] = [];
            $offer_zone_ranges = collect($offer_zones->get($key))->groupBy('offer_zone_range_id');
            foreach ($offer_zone_ranges as $k => $v) {
                $d = [];
                $d['offerZoneRangeId'] = $k . '';
                $units = District::where('division_id', $k)->pluck('id')->toArray();
                $d['offerZoneRangeUnits'] = array_fill(0, count($units), null);
                foreach ($v as $u) {
                    $d['offerZoneRangeUnits'][array_search($u['offer_zone_unit_id'], $units)] = $u['offer_zone_unit_id'] . '';
                }
                array_push($datas['offerZoneRangeIds'], $d);
            }
            return response()->json($datas);


        }
        return view('HRM::OfferZone.edit', ['id' => $id]);
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
        if ($request->ajax()) {
//            return $request->all();
            $rules = [
                'rangeId' => 'required|exists:tbl_division,id',
                'unitId' => 'required',
                'offerZoneRangeIds' => 'required',
                'unitIds.*' => 'exists:tbl_units,id,division_id,' . $request->rangeId,
                'offerZoneRangeIds.*.offerZoneRangeId' => 'required|exists:tbl_division,id',
                'offerZoneRangeIds.*.offerZoneRangeUnits.*' => 'required|exists:tbl_units,id'
            ];
            $this->validate($request, $rules);
            DB::beginTransaction();
            try {

                $range_id = $request->rangeId;
                $unit_id = $request->unitId;
                OfferZone::where('unit_id', $unit_id)->delete();
                $offerZoneRangeIds = $request->offerZoneRangeIds;

                foreach ($offerZoneRangeIds as $zone) {
                    $offer_zone_range_id = $zone['offerZoneRangeId'];
                    foreach ($zone['offerZoneRangeUnits'] as $offer_zone_unit_id) {
                        $offerZone = OfferZone::where(compact('range_id', 'unit_id', 'offer_zone_range_id', 'offer_zone_unit_id'));
                        $offerZone->delete();
                        OfferZone::create(compact('range_id', 'unit_id', 'offer_zone_range_id', 'offer_zone_unit_id'));
                    }
                }

                DB::commit();


            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([['type' => 'error', 'message' => $e->getMessage()]]);
            }
            return response()->json([['type' => 'success', 'message' => 'Offer zone edited successfully']]);
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
        DB::beginTransaction();
        try {
            OfferZone::where('unit_id', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('session_error', $e->getMessage());
        }
        return redirect()->back()->with('session_success', 'Data deleted successfully');
    }
}
