<table>

    <tr>
        <th>SL. no</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Own District</th>
        <th>KPI Name</th>
        <th>KPI Unit</th>
        <th>Embodiment Date</th>
    </tr>
    <?php $i = $index; ?>
    @if(count($ansars)==0)
        <tr class="warning">
            <td colspan="11">No Ansar Found to show</td>
        </tr>
    @else
        @foreach($ansars as $ansar)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$ansar->id}}</td>
                <td>{{$ansar->rank}}</td>
                <td>{{$ansar->name}}</td>
                <td>{{$ansar->unit}}</td>
                <td>{{$ansar->kpi}}</td>
                <td>{{$ansar->k_unit}}</td>
                <td>{{\Carbon\Carbon::parse($ansar->j_date)->format('d-M-Y')}}</td>
            </tr>
        @endforeach
    @endif
</table>