<?php $i = (intVal($unions->currentPage() - 1) * $unions->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total Unions({{$unions->total()}})</span>
            @if(count($unions))
                <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
                    {{$unions->render()}}
                </div>
            @endif
        </caption>

        <tr>
            <th>#</th>
            <th>Union Name(English)</th>
            <th>Union Name(Bangla)</th>
            <th>Union Code</th>
            <th>Division</th>
            <th>District</th>
            <th>Thana</th>
            <th>Action</th>

        </tr>

        @if(count($unions))
            @foreach($unions as $union)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$union->union_name_eng}}</td>
                    <td>{{$union->union_name_bng}}</td>
                    <td>{{$union->code}}</td>
                    <td>{{$union->division->division_name_bng}}</td>
                    <td>{{$union->unit->unit_name_bng}}</td>
                    <td>{{$union->thana->thana_name_bng}}</td>
                    <td>
                        <a href="{{URL::route('HRM.union.edit',$union->id)}}" class="btn btn-xs btn-primary">
                            <i class="fa fa-edit"></i>&nbsp;Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12" class="bg-warning">
                    No Union found
                </td>
            </tr>
        @endif

    </table>
</div>
@if(count($unions))
    <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
        {{$unions->render()}}
    </div>
@endif
