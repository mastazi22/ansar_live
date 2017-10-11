
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
            <td>{{$ansar->name}}</td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->birth_date}}</td>
            <td>{{$ansar->sex}}</td>
            <td>{{$ansar->black_list_from}}</td>
            <td>{{$ansar->black_list_comment}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->black_listed_date)->format('d-M-Y')}}</td>
        </tr>
    @endforeach
@endif
