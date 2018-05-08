<table class="table table-bordered">
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        @if(auth()->user()->type==11)
            <th>Mobile no</th>
        @endif
        <th>Offer Unit</th>
        <th>Offer Date</th>
    </tr>
    @forelse($ansars as $a)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$a->ansar_id}}</td>
            <td>{{$a->ansar_name_eng}}</td>
            <td>{{$a->code}}</td>
            @if(auth()->user()->type==11)
                <td>{{$a->mobile_no_self}}</td>
            @endif
            <td>{{$a->unit_name_bng}}</td>
            <td>{{\Carbon\Carbon::parse($a->sms_send_datetime)->format('d-M-Y h:i:s')}}</td>
        </tr>
    @empty
        <tr>
            <th class="warning" colspan="6">No Ansar Found</th>
        </tr>
    @endforelse
</table>