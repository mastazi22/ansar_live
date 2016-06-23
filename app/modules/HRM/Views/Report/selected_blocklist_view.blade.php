
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
        <td>{{$ansar->name}}</td>
        <td>{{$ansar->rank}}</td>
        <td>{{$ansar->unit}}</td>
        <td>{{$ansar->birth_date}}</td>
        <td>{{$ansar->sex}}</td>
        <td>{{$ansar->block_list_from}}</td>
        <td>{{$ansar->comment_for_block}}</td>
        <td>{{\Carbon\Carbon::createFromFormat('Y-m-d',$ansar->date_for_block)->format('d-M-Y')}}</td>
    </tr>
@endforeach
@endif