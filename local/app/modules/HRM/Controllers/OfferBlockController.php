<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OfferBlockController extends Controller
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
            $offer_blocked = OfferBlockedAnsar::with(['personalinfo', 'unit']);
            if ($request->ansar_id) {
                $offer_blocked->where('ansar_id', $request->ansar_id);
            }
            $ansars = $offer_blocked->paginate(30);
            return view('HRM::offer_rollback.data', compact('ansars'));
        }
        return view('HRM::offer_rollback.offer_rollback');
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->type == 'rollback') {
            return response()->json($this->rollBackOffer($id));
        } else if ($request->type == 'sendtopanel') {
            return response()->json($this->sendToPanel($id));
        } else {
            return response()->json(['status' => false, 'message' => "Invalid request"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function rollBackOffer($id)
    {
        DB::beginTransaction();
        try {
            $blocked_ansar = OfferBlockedAnsar::findOrFail($id);
            $now = Carbon::now();
            $endDate = Carbon::now()->addHours(24);
            OfferSMS::create([
                'sms_send_datetime' => $now->format('Y-m-d h:i:s'),
                'ansar_id' => $blocked_ansar->ansar_id,
                'sms_end_datetime' => $endDate->format('Y-m-d h:i:s'),
                'district_id' => $blocked_ansar->last_offer_unit,
                'come_from' => 'Offer Block',
                'action_user_id' => auth()->user()->id
            ]);
            AnsarStatusInfo::where('ansar_id', $blocked_ansar->ansar_id)->update(['offer_block_status' => 0, 'offer_sms_status' => 1]);
            $blocked_ansar->status = "unblocked";
            $blocked_ansar->unblocked_date = Carbon::now()->format('Y-m-d');
            $blocked_ansar->save();
            $blocked_ansar->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => false, 'message' => $exception->getMessage()];
        }
        return ['status' => true, 'message' => 'Rollback complete'];
    }

    private function sendToPanel($id)
    {
        DB::beginTransaction();
        try {
            $blocked_ansar = OfferBlockedAnsar::findOrFail($id);
            $now = Carbon::now();
            $panel_log = PanelInfoLogModel::where('ansar_id', $blocked_ansar->ansar_id)->orderBy('panel_date', 'desc')->first();
            PanelModel::create([
                'memorandum_id' => $panel_log && isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                'panel_date' => $now,
                're_panel_date' => $now,
                'come_from' => 'Offer Cancel',
                'ansar_merit_list' => 1,
                'ansar_id' => $blocked_ansar->ansar_id,
            ]);
            AnsarStatusInfo::where('ansar_id', $blocked_ansar->ansar_id)->update(['offer_block_status' => 0, 'pannel_status' => 1]);
            $blocked_ansar->status = "unblocked";
            $blocked_ansar->unblocked_date = Carbon::now()->format('Y-m-d');
            $blocked_ansar->save();
            $blocked_ansar->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => false, 'message' => $exception->getMessage()];
        }
        return ['status' => true, 'message' => 'Sending to panel complete'];
    }
}
