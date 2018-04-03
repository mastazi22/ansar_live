<?php $i = (intVal($vdp_infos->currentPage() - 1) * $vdp_infos->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total VDP Member({{$vdp_infos->total()}})</span>
            <a href="{{URL::route('AVURP.info.create')}}" class="btn btn-primary btn-xs pull-right">
                <i class="fa fa-plus"></i>&nbsp;Create New Entry
            </a>
        </caption>

        <tr>
            <th>#</th>
            <th>VDP ID</th>
            <th>Name(English)</th>
            <th>Name(Bangla)</th>
            <th>Date of Birth</th>
            <th>Division</th>
            <th>District</th>
            <th>Thana</th>
            <th>Union</th>
            <th>Ward</th>
            <th>Action</th>

        </tr>

        @if(count($vdp_infos))
            @foreach($vdp_infos as $info)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$info->geo_id}}</td>
                    <td>{{$info->ansar_name_eng}}</td>
                    <td>{{$info->ansar_name_bng}}</td>
                    <td>{{$info->date_of_birth}}</td>
                    <td>{{$info->division->division_name_bng}}</td>
                    <td>{{$info->unit->unit_name_bng}}</td>
                    <td>{{$info->thana->thana_name_bng}}</td>
                    <td>{{$info->union->union_name_bng}}</td>
                    <td>{{$info->union_word_id}}</td>
                    <td>
                        {{--<a href="{{URL::route('AVURP.info.edit',$info->id)}}" class="btn btn-xs btn-primary">
                            <i class="fa fa-edit"></i>&nbsp;Edit
                        </a>--}}
                        <a href="{{URL::route('AVURP.info.show',$info->id)}}" class="btn btn-xs btn-primary">
                            <i class="fa fa-eye"></i>&nbsp;View
                        </a>
                        <a href="{{URL::route('AVURP.info.edit',$info->id)}}" class="btn btn-xs btn-primary">
                            <i class="fa fa-edit"></i>&nbsp;Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12" class="bg-warning">
                    No VPD member found
                </td>
            </tr>
        @endif

    </table>
</div>
@if(count($vdp_infos))
    <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
        {{$vdp_infos->render()}}
    </div>
@endif
