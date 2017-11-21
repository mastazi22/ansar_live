<table>
    <tr>
        <th>Sl. No.</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Own District</th>
        <th>Date of Birth</th>
        <th>Gender</th>
        <th>Blocklisted from where</th>
        <th>Blocklisted Reason</th>
        <th>Blocklisted Date</th>
    </tr>
    @forelse($ansars as $a)
        <tr ng-repeat="a in ansars.ansars">
            <td>{{$index++}}</td>
            <td>{{$a->id}}</td>
            <td>{{$a->name}}</td>
            <td>{{$a->rank}}</td>
            <td>{{$a->unit}}</td>
            <td>{{\Carbon\Carbon::parse($a->birth_date)->format('d-M-Y')}}</td>
            <td>{{$a->sex}}</td>
            <td>{{$a->block_list_from}}</td>
            <td>{{$a->comment_for_block}}</td>
            <td>{{\Carbon\Carbon::parse($a->date_for_block)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="warning">
                No Ansar available
            </td>
        </tr>
    @endforelse
</table>