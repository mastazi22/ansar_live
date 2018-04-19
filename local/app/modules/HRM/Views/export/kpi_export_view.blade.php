<table>
    <tr>
        <th>SL. No</th>
        <th>KPI Name</th>
        <th>Division</th>
        <th>Unit</th>
        <th>Thana</th>
        <th>KPI Address</th>
        <th>KPI Contact No.</th>
        <th>Total Ansar Request</th>
        <th>Total Given Ansar</th>
        <th>Total Embodied Ansar</th>
        <th>Percent</th>
        <th>Deficiency</th>
        <th>Surplus</th>
    </tr>
    @forelse($ansars as $kpi)
        <tr>
            <td>
                {{$index++}}
            </td>
            <td>
                {{$kpi->kpi_bng}}
            </td>
            <td>
                {{$kpi->division_eng}}
            </td>
            <td>
                {{$kpi->unit}}
            </td>
            <td>
                {{$kpi->thana}}
            </td>
            <td>
                {{$kpi->address}}
            </td>
            <td>
                {{$kpi->contact}}
            </td>
            <td>{{$kpi->total_ansar_request}}</td>
            <td>{{$kpi->total_ansar_given}}</td>
            <td>{{$kpi->total_embodied}}</td>
            <td>{{($kpi->total_embodied*100)/$kpi->total_ansar_given}}</td>
            <td>{{$kpi->total_ansar_request-$kpi->total_embodied>0?(($kpi->total_ansar_request-$kpi->total_embodied)):0}}</td>
            <td>{{$kpi->total_ansar_request-$kpi->total_embodied<0?(-($kpi->total_ansar_request-$kpi->total_embodied)):0}}</td>
        </tr>
    @empty
    @endforelse
</table>