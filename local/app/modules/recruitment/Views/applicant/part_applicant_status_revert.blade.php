<?php $i = (intVal($applicants->currentPage() - 1) * $applicants->perPage()) + 1; ?>
<div>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed">
            <caption style="font-size: 20px;color:#111111">All {{$status}} applicants({{$applicants->total()}})
                <div class="input-group" style="margin-top: 10px">
                    <input ng-keyup="$event.keyCode==13?loadApplicant():''" class="form-control" ng-model="param.q"
                           type="text" placeholder="Search by national id,name,mobile_no or applicant id">
                    <span class="input-group-btn">
                    <button class="btn btn-primary" ng-click="loadApplicant()">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
                </div>
            </caption>
            <tr>
                <th>Sl. No</th>
                <th>Applicant ID</th>
                <th>Applicant Name</th>
                <th>Father Name</th>
                <th>Height</th>
                <th>Gender</th>
                <th>Birth Date</th>
                <th>Division</th>
                <th>District</th>
                <th>Thana</th>
                <th>Status</th>
                <th>Revert Status to</th>
                <th>Action</th>
            </tr>
            @forelse($applicants as $a)
                <tr ng-init="param[{{$i-1}}]['applicant_id']='{{$a->applicant_id}}'">
                    <td>{{$i++}}</td>
                    <td>{{$a->applicant_id}}</td>
                    <td>{{$a->applicant_name_bng}}</td>
                    <td>{{$a->father_name_bng}}</td>
                    <td>{{$a->height_feet."'".$a->height_inch."''"}}</td>
                    <td>{{$a->gender}}</td>
                    <td>{{$a->date_of_birth}}</td>
                    <td>{{$a->division->division_name_bng}}</td>
                    <td>{{$a->district->unit_name_bng}}</td>
                    <td>{{$a->thana->thana_name_bng}}</td>
                    <td>{{$a->status}}</td>
                    <td>
                        <select name="" ng-model="param[{{$i-2}}].status" class="form-control" style="height: auto !important;padding: 0 !important;">
                            <option ng-repeat="(k,v) in allStatus" ng-disabled="k=='{{$a->status}}'" value="[[k]]">[[v]]</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-xs" ng-disabled="!param[{{$i-2}}].status"
                                ng-click="revertStatus(param[{{$i-2}}])">Revert Status
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="bg-warning" colspan="14">No data available</td>
                </tr>
            @endforelse
        </table>
    </div>
    @if(count($applicants))
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="" class="control-label">Load limit</label>
                    <select class="form-control"  ng-model="param.limit" ng-change="loadApplicant()">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="pull-right" paginate ref="loadApplicant(url)">
                    {{$applicants->render()}}
                </div>
            </div>
        </div>
    @endif
</div>