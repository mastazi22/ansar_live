<table class="table table-bordered full">
    <tr>
        <th>SL. no</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>KPI Name</th>
        <th>Own District</th>
        <th>KPI Range</th>
        <th>KPI Unit</th>
        <th>Reporting Date</th>
        <th>Embodiment Date</th>
        <th>Service ended date</th>
    </tr>
    @forelse($ansars as $a)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$a->id}}</td>
            <td>{{$a->name}}</td>
            <td>{{$a->rank}}</td>
            <td>{{$a->kpi}}</td>
            <td>{{$a->unit}}</td>

            <td>{{$a->division_name_bng}}</td>
            <td>{{$a->unit_name_bng}}</td>
            <td>{{\Carbon\Carbon::parse($a->r_date)->format('d-M-Y')}}</td>
            <td>{{\Carbon\Carbon::parse($a->j_date)->format('d-M-Y')}}</td>
            <td>{{\Carbon\Carbon::parse($a->se_date)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="warning">
                No Ansar available
            </td>
        </tr>
    @endforelse
</table>