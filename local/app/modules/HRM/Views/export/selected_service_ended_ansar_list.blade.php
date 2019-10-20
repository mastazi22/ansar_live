<table>
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Current KPI Name</th>
        <th>KPI Unit</th>
        <th>KPI Thana</th>
        <th>Embodiment Date</th>
        <th>Service Ended Date</th>
    </tr>
    <tbody>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$index++}}</td>
            <td><a href="{{URL::to('HRM/entryreport')}}/{{$ansar->id}}">{{$ansar->id}}</a></td>
            <td>{{$ansar->name}}</td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->kpi}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->j_date)->format('d-M-Y')}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->se_date)->format('d-M-Y')}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="9">No Ansar Found</td>
        </tr>
    @endforelse
    </tbody>
</table>