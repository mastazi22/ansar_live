<?php

namespace App\modules\HRM\Models;

use App\Jobs\BlockStatusSms;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AnsarFutureState extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_future_state';

    protected $guarded = [];

    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class, 'ansar_id', 'ansar_id');
    }

    public function getDataAttribute($value)
    {
        if (!isset($value) || empty($value)) return '';
        return unserialize($value);
    }

    public function moveToPanel()
    {
        $data = unserialize($this->data);
        $status = AnsarStatusInfo::where('ansar_id', $this->ansar_id)->first();
        if (!$status) throw new \Exception("");
        PanelModel::create($data);
        switch ($status->getStatus()[0]) {
            case AnsarStatusInfo::FREE_STATUS:
                $status->update([
                    'free_status' => 0,
                    'pannel_status' => 1,
                ]);
                break;
            case AnsarStatusInfo::REST_STATUS:
                $rest_info = RestInfoModel::where('ansar_id', $this->ansar_id)->first();
                $rest_info->saveLog("Panel", Carbon::now()->format('Y-m-d'), "transfer to panel");
                $status->update([
                    'rest_status' => 0,
                    'pannel_status' => 1,
                ]);
                $rest_info->delete();
                break;
        }
    }

    public function moveToEmbodiment()
    {
        $data = unserialize($this->data);
        $status = AnsarStatusInfo::where('ansar_id', $this->ansar_id)->first();
        if (!$status) throw new \Exception("");
        EmbodimentModel::create($data);
        switch ($status->getStatus()[0]) {
            case AnsarStatusInfo::PANEL_STATUS:
                $status->panel->saveLog('Emboded');
                $status->panel->delete();
                $status->update([
                    'pannel_status' => 0,
                    'embodied_status' => 1,
                ]);
                break;
            case AnsarStatusInfo::REST_STATUS:
                $rest_info = RestInfoModel::where('ansar_id', $this->ansar_id)->first();
                $rest_info->saveLog();
                $status->update([
                    'rest_status' => 0,
                    'pannel_status' => 1,
                ]);
                $rest_info->delete();
                break;
        }
    }

    public function moveToRest()
    {
        $data = unserialize($this->data);
        $status = AnsarStatusInfo::where('ansar_id', $this->ansar_id)->first();
        if (!$status) throw new \Exception("");
        RestInfoModel::create($data);
        $em_info = EmbodimentModel::where('ansar_id', $this->ansar_id)->first();
        $em_info->saveLog('Rest', $data['rest_date'], $data['comment'], $data['disembodiment_reason_id']);
        $status->update(['embodied_status' => 0, 'rest_status' => 1]);
        $em_info->delete();
    }

    public function moveToUnverified()
    {
        $data = unserialize($this->data);
        $status = AnsarStatusInfo::where('ansar_id', $this->ansar_id)->first();
        if (!$status) throw new \Exception("");
        $block_info = BlockListModel::where('ansar_id', $this->ansar_id)->first();
        $block_info->update($data);
        $status->ansar->update(['verified' => 0]);
        $status->update(['block_list_status' => 0]);
    }

    public function moveToFree()
    {
        $data = unserialize($this->data);
        $status = AnsarStatusInfo::where('ansar_id', $this->ansar_id)->first();
        if (!$status) throw new \Exception("");
        $blacklist_info = BlackListModel::where('ansar_id', $this->ansar_id)->first();
        BlackListInfoModel::create($data);
        $blacklist_info->delete();
        $status->update(['free_status' => 1, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
    }

    public function moveToBlock()
    {
        $status = AnsarStatusInfo::where('ansar_id', $this->ansar_id)->first();
        $data = unserialize($this->data);
        BlockListModel::create($data);

        switch ($status->getStatus()[0]) {

            case AnsarStatusInfo::FREE_STATUS:
                $status->update(['block_list_status' => 1, 'free_status' => 0]);
                CustomQuery::addActionlog(['ansar_id' => $this->ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                break;

            case AnsarStatusInfo::PANEL_STATUS:
                $status->ansar->panel->saveLog("Blocklist", $data['date_for_block'], $data['comment_for_block']);
                $status->ansar->panel->delete();
                $status->update(['block_list_status' => 1, 'pannel_status' => 0]);
                CustomQuery::addActionlog(['ansar_id' => $this->ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                break;

            case AnsarStatusInfo::EMBODIMENT_STATUS:
                $status->ansar->embodiment->saveLog("Blocklist", $data['date_for_block'], 8);
                $status->ansar->embodiment->delete();
                $status->update(['block_list_status' => 1, 'embodied_status' => 0]);
                break;

            case AnsarStatusInfo::REST_STATUS:
                $status->ansar->rest->saveLog("Blocklist", $data['date_for_block'], $data['comment_for_block']);
                $status->ansar->rest->delete();
                $status->update(['block_list_status' => 1, 'rest_status' => 0]);
                break;
            case AnsarStatusInfo::OFFER_BLOCK_STATUS:
                $offer_blocked = OfferBlockedAnsar::where('ansar_id', $this->ansar_id)->first();
                $offer_blocked->delete();
                $status->update(['block_list_status' => 1, 'offer_block_status' => 0]);
                break;
            default:
                throw new \Exception('This Ansar can`t be blocked.Because he is BLACKED');
                break;

        }

        dispatch(new BlockStatusSms($this->ansar_id, $data['comment_for_block']));
    }
}
