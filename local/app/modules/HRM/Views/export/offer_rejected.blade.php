<table class="table table-bordered">
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Offer Unit</th>
        <th>Offer Rejected Date</th>
    </tr>
    @forelse($ansars as $a)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$a->ansar_id}}</td>
            <td>{{$a->ansar_name_eng}}</td>
            <td>{{$a->code}}</td>
            <td>{{$a->unit_name_bng}}</td>
            <td>{{\Carbon\Carbon::parse($a->reject_date)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <th class="warning" colspan="6">No Ansar Found</th>
        </tr>
    @endforelse
</table>