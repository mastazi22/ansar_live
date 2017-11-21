<table>
    <tr>
        <th>Sl. No.</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Own District</th>
        <th>Date of Birth</th>
        <th>Gender</th>
        <th>Blacklisted from where</th>
        <th>Blacklisted Reason</th>
        <th>Blacklisted Date</th>
    </tr>
    @forelse($ansars as $a)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$a->id}}</td>
            <td>{{$a->name}}</td>
            <td>{{$a->rank}}</td>
            <td>{{$a->unit}}</td>
            <td>{{\Carbon\Carbon::parse($a->birth_date)->format('d-M-Y')}}</td>
            <td>{{$a->sex}}</td>
            <td>{{$a->black_list_from}}</td>
            <td>{{$a->black_list_comment}}</td>
            <td>{{\Carbon\Carbon::parse($a->black_listed_date)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="warning">
                No Ansar available
            </td>
        </tr>
    @endforelse
</table>