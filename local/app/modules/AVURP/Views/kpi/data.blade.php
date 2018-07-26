<?php $i = (intVal($kpi_infos->currentPage() - 1) * $kpi_infos->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total KPI({{$kpi_infos->total()}})</span>
            <a href="{{URL::route('AVURP.kpi.create')}}" class="btn btn-primary btn-xs pull-right">
                <i class="fa fa-plus"></i>&nbsp;Create New KPI
            </a>
        </caption>

        <tr>
            <th>#</th>
            <th>KPI Name</th>
            <th>Division</th>
            <th>Unit</th>
            <th>Thana</th>
            <th>Address</th>
            <th>Contact No</th>
            <th>Action</th>

        </tr>

        @if(count($kpi_infos))
            @foreach($kpi_infos as $info)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$info->kpi_name}}</td>
                    <td>{{$info->division->division_name_bng}}</td>
                    <td>{{$info->unit->unit_name_bng}}</td>
                    <td>{{$info->thana->thana_name_bng}}</td>
                    <td>{{$info->address}}</td>
                    <td>{{$info->contact_no}}</td>
                    <td>
                        <a href="{{URL::route('AVURP.kpi.edit',$info->id)}}" class="btn btn-xs btn-primary">
                            <i class="fa fa-edit"></i>&nbsp;Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" class="bg-warning">
                    No KPI info available
                </td>
            </tr>
        @endif

    </table>
</div>
@if(count($kpi_infos))
    <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
        {{$kpi_infos->render()}}
    </div>
@endif
