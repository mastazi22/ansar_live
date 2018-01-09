<table class="table table-bordered">
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Own District</th>
        <th>Freeze Date</th>
        <th>Unfreeze Date</th>
        <th>Current Status</th>
    </tr>
    @forelse($ansars as $a)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$a->ansar_id}}</td>
            <td>{{$a->ansar_name_eng}}</td>
            <td>{{$a->code}}</td>
            <td>{{$a->unit}}</td>
            <td>{{\Carbon\Carbon::parse($a->freeze_date)->format('d-M-Y')}}</td>
            <td>{{\Carbon\Carbon::parse($a->unfreeze_date)->format('d-M-Y')}}</td>
            <td>{{implode(',',\App\modules\HRM\Models\AnsarStatusInfo::where('ansar_id',$a->ansar_id)->first()->getStatus())}}</td>
        </tr>
    @empty
        <tr>
            <th class="warning" colspan="5">No Ansar Found</th>
        </tr>
    @endforelse
</table>