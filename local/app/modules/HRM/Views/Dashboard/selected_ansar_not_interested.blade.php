<?php $i = $index; ?>
@if(count($ansars)>0)
    @foreach($ansars as $ansar)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$ansar->id}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->birth_date}}</td>
            <td>{{$ansar->sex}}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td class="warning" colspan="8">No Ansar Found</td>
    </tr>
@endif