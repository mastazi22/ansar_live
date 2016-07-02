
<?php $i = $index; ?>
@if(count($ansars)==0)
    <tr class="warning">
        <td colspan="11">No Ansar Found</td>
    </tr>
@else
    @foreach($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$ansar->id}}</td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{$ansar->kpi}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{\Carbon\Carbon::createFromFormat('Y-m-d',$ansar->r_date)->format('d-M-Y')}}</td>
            <td>{{\Carbon\Carbon::createFromFormat('Y-m-d',$ansar->j_date)->format('d-M-Y')}}</td>
            <td>{{$ansar->reason}}</td>
            <td>{{\Carbon\Carbon::createFromFormat('Y-m-d',$ansar->re_date)->format('d-M-Y')}}</td>
        </tr>
    @endforeach
@endif
