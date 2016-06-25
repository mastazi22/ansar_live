<?php
$index=1;
?>
@if(count($kpi_infos)>0)
    @foreach($kpi_infos as $kpi_info)
        <tr>
            <td>{{$index}}</td>
            <td>{{$kpi_info->ansar_id}}</td>
            <td>{{$kpi_info->ansar_name_eng}}</td>
            <td>{{$kpi_info->name_eng}}</td>
            <td>{{$kpi_info->sex}}</td>
            <td>{{$kpi_info->kpi_name}}</td>
            <td>{{$kpi_info->unit_name_eng}}</td>
            <td>{{$kpi_info->thana_name_eng}}</td>
            <td>{{\Carbon\Carbon::createFromFormat('Y-m-d',$kpi_info->reporting_date)->format('d-M-Y')}}</td>
            <td>{{\Carbon\Carbon::createFromFormat('Y-m-d',$kpi_info->joining_date)->format('d-M-Y')}}</td>
            <input type="hidden" name="kpi_id_withdraw" value="{{$kpi_info->kpi_id}}">
        </tr>
<?php
$index++;
?>
    @endforeach
@else
    <tr colspan="7" class="warning" id="not-find-info">
        <td colspan="7">No Ansar Found to Withdraw</td>
    </tr>
@endif
<script>
    function dateConvert(){

    }
</script>