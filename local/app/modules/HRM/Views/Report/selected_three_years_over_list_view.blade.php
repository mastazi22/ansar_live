
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
        <td>{{\Carbon\Carbon::parse($ansar->r_date)->format('d-M-Y')}}</td>
        <td>{{\Carbon\Carbon::parse($ansar->j_date)->format('d-M-Y')}}</td>
        <td>{{\Carbon\Carbon::parse($ansar->se_date)->format('d-M-Y')}}</td>
    </tr>
@endforeach
@endif