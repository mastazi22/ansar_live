<?php $i = (intVal($offered_ansar->currentPage() - 1) * $offered_ansar->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption style="font-size: 16px">
            <strong>VDP/Ansar List({{$offered_ansar->total()}})</strong>
            <button class="btn btn-primary btn-xs" ng-click="selectAll()">
                <i class="fa fa-plus"></i>&nbsp;Select All
            </button>
            <button class="btn btn-danger btn-xs" ng-if="selected.length>0" ng-click="removeAll()">
                <i class="fa fa-remove"></i>&nbsp;Remove All
            </button>
            <button class="btn btn-success btn-xs" ng-if="selected.length>0" ng-click="openEmbodimentModal()">
                <i class="fa fa-cog"></i>&nbsp;Embodied
            </button>
        </caption>
        <tr>
            <th>SL. No</th>
            <th>GEO ID</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Division</th>
            <th>District</th>
            <th>Upazila/Thana</th>
            <th>Union</th>
            <th>Word</th>
            <th>Offer Date</th>
            <th>Action</th>
        </tr>

        @if(count($offered_ansar))
            @foreach($offered_ansar as $info)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$info->geo_id}}</td>
                    <td>{{$info->ansar_name_bng or $info->ansar_name_eng}}</td>
                    <td>{{$info->designation}}</td>
                    <td>{{$info->division->division_name_bng}}</td>
                    <td>{{$info->unit->unit_name_bng}}</td>
                    <td>{{$info->thana->thana_name_bng}}</td>
                    <td>{{$info->union?$info->union->union_name_bng:'--'}}</td>
                    <td>{{$info->union_word_id}}</td>
                    <td>{{$info->offer->sms_send_datetime}}</td>
                    <td>
                        <button ng-if="selected.indexOf('{{$info->id}}')<0" class="btn btn-primary btn-xs" ng-click="addToSelection('{{$info->id}}')">
                            <i class="fa fa-check"></i>&nbsp;select
                        </button>
                        <button ng-if="selected.indexOf('{{$info->id}}')>=0" class="btn btn-danger btn-xs" ng-click="removeFromSelection('{{$info->id}}')">
                            <i class="fa fa-remove"></i>&nbsp;remove
                        </button>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="11" class="bg-warning">
                    No VPD member found
                </td>
            </tr>
        @endif

    </table>
</div>
@if(count($offered_ansar))
    <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
        {{$offered_ansar->render()}}
    </div>
@endif
