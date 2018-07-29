<?php $i = (intVal($vdp_infos->currentPage() - 1) * $vdp_infos->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption style="font-size: 16px">
            <strong>VDP/Ansar List({{$vdp_infos->total()}})</strong>
            <button class="btn btn-primary btn-xs" ng-click="selectAll()">
                <i class="fa fa-plus"></i>&nbsp;Select All
            </button>
            <button class="btn btn-danger btn-xs" ng-if="selected.length>0" ng-click="removeAll()">
                <i class="fa fa-remove"></i>&nbsp;Remove All
            </button>
            <button class="btn btn-success btn-xs" ng-if="selected.length>0" ng-click="sendOffer()">
                <i class="fa fa-send"></i>&nbsp;Send Offer
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
            <th>Height</th>
            <th>Age</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        @if(count($vdp_infos))
            @foreach($vdp_infos as $info)
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
                    <td>{{$info->height_feet." Feet ".$info->height_inch." Inch"}}</td>
                    <td>{{$info->age()}}</td>
                    @if($info->status=='new')
                        <td>
                            <span class="label label-danger">Unverified</span>
                        </td>
                    @elseif($info->status=='verified')
                        <td>
                            <span class="label label-warning">Verified</span>
                        </td>
                    @else
                        <td>
                            <span class="label label-success">Approved</span>
                        </td>
                    @endif
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
