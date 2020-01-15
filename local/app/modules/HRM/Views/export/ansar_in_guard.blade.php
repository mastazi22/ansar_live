<table class="table table-bordered">
    <tr>
        <th>SL. no</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Date of birth</th>
        <th>Height</th>
        <th>Education</th>
        <th>District</th>
        <th>Mobile</th>
        <th>Share Id</th>
        <th>Embodiment Date</th>
        <th>Transfer Date</th>
    </tr>
    @forelse($ansars as $a)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$a->ansar_id}}</td>
            <td>{{$a->name_bng}}</td>
            <td>{{$a->ansar_name_bng}}</td>
            <td>{{$a->dob?\Carbon\Carbon::parse($a->dob)->format('d M, Y'):$a->dob}}</td>
            <td>{{$a->height}}</td>
            <td>{{$a->education}}</td>
            <td>{{$a->unit_name_bng}}</td>
            <td>{{$a->mobile_no_self}}</td>
            <td>{{$a->avub_share_id}}</td>
            <td>{{\Carbon\Carbon::parse($a->joining_date)->format('d-M-Y')}}</td>
            <td>{{$a->transfered_date?\Carbon\Carbon::parse($a->transfered_date)->format('d-M-Y'):'--'}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="warning no-ansar">No Ansar is available to show</td>
        </tr>
    @endforelse
</table>