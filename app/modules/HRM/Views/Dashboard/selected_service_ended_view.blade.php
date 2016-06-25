{{--User: Shreya--}}
{{--Date: 12/24/2015--}}
{{--Time: 2:46 PM--}}

<?php $i = $index; ?>
@if(count($ansars)>0)
    @foreach($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$ansar->id}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->kpi}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->j_date}}</td>
            <td>{{$ansar->se_date}}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td class="warning" colspan="9">No Ansar Found</td>
    </tr>
@endif