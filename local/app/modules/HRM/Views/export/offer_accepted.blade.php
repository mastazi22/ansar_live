<table class="table table-bordered">
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Offer Accepted Date</th>
    </tr>
    @forelse($ansars as $a)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$a->ansar_id}}</td>
            <td>{{$a->ansar_name_eng}}</td>
            <td>{{$a->code}}</td>
            <td>{{\Carbon\Carbon::parse($a->sms_received_datetime)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <th class="warning" colspan="5">No Ansar Found</th>
        </tr>
    @endforelse
</table>