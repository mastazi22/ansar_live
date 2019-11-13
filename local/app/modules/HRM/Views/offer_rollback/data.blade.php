<?php $i = (intVal($ansars->currentPage() - 1) * $ansars->perPage()) + 1; ?>
<div class="table-responsive">
    <table class="table table-bordered table-condensed">
        <caption>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="search by Ansar ID" ng-model="param.ansar_id"
                       ng-keypress="$event.keyCode==13?loadData():''">
                <span class="input-group-btn">
                    <button class="btn btn-default" ng-click="loadData()">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-default" ng-click="clearSearch()">
                        <i class="fa fa-close"></i>
                    </button>
                </span>
            </div>
        </caption>
        <tr>
            <th>Sl. No</th>
            <th>Ansar ID</th>
            <th>Name</th>
            <th>Rank</th>
            <th>Home Division</th>
            <th>Home District</th>
            <th>Last Offer District</th>
            <th>Block Date</th>
            <th>Action</th>
        </tr>
        @forelse($ansars as $ansar)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$ansar->personalinfo->ansar_id}}</td>
                <td>{{$ansar->personalinfo->ansar_name_bng}}</td>
                <td>{{$ansar->personalinfo->designation->name_bng}}</td>
                <td>{{$ansar->personalinfo->division->division_name_bng}}</td>
                <td>{{$ansar->personalinfo->district->unit_name_bng}}</td>
                <td>{{$ansar->unit->unit_name_bng}}</td>
                <td>{{$ansar->blocked_date}}</td>
                <td>
                    <button class="btn btn-primary btn-xs" ng-click="rollback('{{$ansar->id}}')">Rollback offer</button>
                    <button class="btn btn-primary btn-xs" ng-click="sendToPanel('{{$ansar->id}}')">Send to panel
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="bg-warning">
                    No Ansar Available
                </td>
            </tr>
        @endforelse
    </table>
</div>
@if(count($ansars))
    <div class="row">
        <div class="col-sm-9 col-sm-offset-3">
            <div class="pull-right" paginate ref="loadData(url)">
                {{$ansars->render()}}
            </div>
        </div>
    </div>
@endif