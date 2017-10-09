{{--User: Shreya--}}
{{--Date: 11/5/2015--}}
{{--Time: 1:50 PM--}}

@foreach($kpi_infos as $kpi_info)

    <tr>
        <td>{{$kpi_info->ansar_id}}</td>
        <td>{{$kpi_info->ansar_name_eng}}</td>
        <td>{{$kpi_info->name_eng}}</td>
        <td>{{$kpi_info->sex}}</td>
        <td>{{$kpi_info->kpi_name}}</td>
        <td>{{$kpi_info->unit_name_eng}}</td>
        <td>{{$kpi_info->thana_name_eng}}</td>
        <td>{{\Carbon\Carbon::parse($kpi_info->reporting_date)->format('d-M-Y')}}</td>
        <td>{{\Carbon\Carbon::parse($kpi_info->joining_date)->format('d-M-Y')}}</td>
        <td><div class="styled-checkbox">
            <input type="checkbox" id="a_{{$kpi_info->ansar_id}}" name="ch[]" class="reduce-guard-strength-check" value="{{ $kpi_info->ansar_id }}">
                <label for="a_{{$kpi_info->ansar_id}}"></label>
        </div>
        </td>
    </tr>

@endforeach