<table class="table table-bordered">
    <tr>
        <th>SL. no</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>District</th>
        <th>Embodiment Date</th>
        <th>Joining Date</th>
    </tr>
    @forelse($ansars as $a)
        <tr>
            <td>
                {{$index++}}
            </td>
            <td>
                {{$a->ansar_id}}
            </td>
            <td>
                {{$a->name_bng}}
            </td>
            <td>
                {{$a->ansar_name_bng}}
            </td>
            <td>
                {{$a->unit_name_bng}}
            </td>
            <td>
                {{\Carbon\Carbon::parse($a->joining_date)->format('d-M-Y')}}
            </td>
            <td>
                {{$a->transfered_date?\Carbon\Carbon::parse($a->transfered_date)->format('d-M-Y'):'--'}}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="warning no-ansar">
                No Ansar is available to show
            </td>
        </tr>
    @endforelse
</table>