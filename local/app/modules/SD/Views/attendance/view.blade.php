<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title" style="text-align: center;font-weight: bold">{!! $title !!}</h4>
</div>
<div class="modal-body">
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#Present" data-parent="#accordion">Present</a>
                </h4>
            </div>
            <div id="Present" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped">
                            <tr>
                                <th>SL. No</th>
                                <th>Ansar ID</th>
                                <th>Name</th>
                                <th>Present KPI Name</th>
                                <th>Own Division</th>
                                <th>Own District</th>
                                <th>Own Thana</th>
                                <th>Status</th>
                                @if(!$salary_disburst_status)
                                    <th>Action</th>
                                @endif
                            </tr>
                            <?php $i = 0;?>
                            @forelse($present_list as $present)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$present->ansar->ansar_id}}</td>
                                    <td>{{$present->ansar->ansar_name_bng}}</td>
                                    <td>{{$present->ansar->embodiment?$kpi->kpi_name.($kpi->id==$present->ansar->embodiment->kpi->id?'':'('.$present->ansar->embodiment->kpi->kpi_name.')'):$kpi->kpi_name}}</td>
                                    <td>{{$present->ansar->division->division_name_bng}}</td>
                                    <td>{{$present->ansar->district->unit_name_bng}}</td>
                                    <td>{{$present->ansar->thana->thana_name_bng}}</td>
                                    <td>
                                        <span ng-if="!present.editing[{{$i-1}}]">Present</span>
                                        @if(!$salary_disburst_status)
                                            <select ng-if="present.editing[{{$i-1}}]"
                                                    ng-model="present.status[{{$i-1}}]">
                                                <option value="">Select a status</option>
                                                <option value="absent">Absent</option>
                                                <option value="leave">Leave</option>
                                            </select>
                                        @endif
                                    </td>
                                    @if(!$salary_disburst_status)
                                        <td>
                                            <a href="#" ng-click="present.editing[{{$i-1}}]=1"
                                               class="btn btn-primary btn-xs" ng-if="!present.editing[{{$i-1}}]">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="#"
                                               ng-disabled="!present.status[{{$i-1}}]||present.loading[{{$i-1}}]"
                                               ng-click="updateAttendanceStatus({{$i-1}},'{{$present->id}}','present')"
                                               class="btn btn-primary btn-xs" ng-if="present.editing[{{$i-1}}]">
                                                <i class="fa "
                                                   ng-class="{'fa-save':!present.loading[{{$i-1}}],'fa-spinner fa-pulse':present.loading[{{$i-1}}]}"></i>
                                            </a>
                                            <a href="#" ng-disabled="present.loading[{{$i-1}}]"
                                               ng-click="present.editing[{{$i-1}}]=0;present.status[{{$i-1}}]=''"
                                               class="btn btn-danger btn-xs" ng-if="present.editing[{{$i-1}}]">
                                                <i class="fa fa-close"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="bg-warning">No Data Available</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#Absent" data-parent="#accordion">Absent</a>
                </h4>
            </div>
            <div id="Absent" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped">
                            <tr>
                                <th>SL. No</th>
                                <th>Ansar ID</th>
                                <th>Name</th>
                                <th>Present KPI Name</th>
                                <th>Own Division</th>
                                <th>Own District</th>
                                <th>Own Thana</th>
                                <th>Status</th>
                                @if(!$salary_disburst_status)
                                    <th>Action</th>
                                @endif
                            </tr>
                            <?php $i = 0;?>
                            @forelse($absent_list as $absent)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$absent->ansar->ansar_id}}</td>
                                    <td>{{$absent->ansar->ansar_name_bng}}</td>
                                    <td>{{$present->ansar->embodiment?$kpi->kpi_name.($kpi->id==$present->ansar->embodiment->kpi->id?'':'('.$present->ansar->embodiment->kpi->kpi_name.')'):$kpi->kpi_name}}</td>
                                    <td>{{$absent->ansar->division->division_name_bng}}</td>
                                    <td>{{$absent->ansar->district->unit_name_bng}}</td>
                                    <td>{{$absent->ansar->thana->thana_name_bng}}</td>
                                    <td>
                                        <span ng-if="!absent.editing[{{$i-1}}]">Absent</span>
                                        @if(!$salary_disburst_status)
                                            <select ng-if="absent.editing[{{$i-1}}]" ng-model="absent.status[{{$i-1}}]">
                                                <option value="">Select a status</option>
                                                <option value="present">Present</option>
                                                <option value="leave">Leave</option>
                                            </select>
                                        @endif
                                    </td>
                                    @if(!$salary_disburst_status)
                                        <td>
                                            <a href="#" ng-click="absent.editing[{{$i-1}}]=1"
                                               class="btn btn-primary btn-xs"
                                               ng-if="!absent.editing[{{$i-1}}]">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="#" ng-disabled="!absent.status[{{$i-1}}]||absent.loading[{{$i-1}}]"
                                               ng-click="updateAttendanceStatus({{$i-1}},'{{$absent->id}}','absent')"
                                               class="btn btn-primary btn-xs" ng-if="absent.editing[{{$i-1}}]">
                                                <i class="fa "
                                                   ng-class="{'fa-save':!absent.loading[{{$i-1}}],'fa-spinner fa-pulse':absent.loading[{{$i-1}}]}"></i>
                                            </a>
                                            <a href="#" ng-disabled="absent.loading[{{$i-1}}]"
                                               ng-click="absent.editing[{{$i-1}}]=0;absent.status[{{$i-1}}]=''"
                                               class="btn btn-danger btn-xs" ng-if="absent.editing[{{$i-1}}]">
                                                <i class="fa fa-close"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="bg-warning">No Data Available</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#Leave" data-parent="#accordion">Leave</a>
                </h4>
            </div>
            <div id="Leave" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped">
                            <tr>
                                <th>SL. No</th>
                                <th>Ansar ID</th>
                                <th>Name</th>
                                <th>Present KPI Name</th>
                                <th>Own Division</th>
                                <th>Own District</th>
                                <th>Own Thana</th>
                                <th>Status</th>
                                @if(!$salary_disburst_status)
                                    <th>Action</th>
                                @endif
                            </tr>
                            <?php $i = 0;?>
                            @forelse($leave_list as $leave)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$leave->ansar->ansar_id}}</td>
                                    <td>{{$leave->ansar->ansar_name_bng}}</td>
                                    <td>{{$present->ansar->embodiment?$kpi->kpi_name.($kpi->id==$present->ansar->embodiment->kpi->id?'':'('.$present->ansar->embodiment->kpi->kpi_name.')'):$kpi->kpi_name}}</td>
                                    <td>{{$leave->ansar->division->division_name_bng}}</td>
                                    <td>{{$leave->ansar->district->unit_name_bng}}</td>
                                    <td>{{$leave->ansar->thana->thana_name_bng}}</td>
                                    <td>
                                        <span ng-if="!leave.editing[{{$i-1}}]">Leave</span>
                                        @if(!$salary_disburst_status)
                                            <select ng-if="leave.editing[{{$i-1}}]" ng-model="leave.status[{{$i-1}}]">
                                                <option value="">Select a status</option>
                                                <option value="absent">Absent</option>
                                                <option value="present">Present</option>
                                            </select>
                                        @endif
                                    </td>
                                    @if(!$salary_disburst_status)
                                        <td>
                                            <a href="#" ng-click="leave.editing[{{$i-1}}]=1"
                                               class="btn btn-primary btn-xs"
                                               ng-if="!leave.editing[{{$i-1}}]">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="#" ng-disabled="!leave.status[{{$i-1}}]||leave.loading[{{$i-1}}]"
                                               ng-click="updateAttendanceStatus({{$i-1}},'{{$leave->id}}','leave')"
                                               class="btn btn-primary btn-xs" ng-if="leave.editing[{{$i-1}}]">
                                                <i class="fa "
                                                   ng-class="{'fa-save':!leave.loading[{{$i-1}}],'fa-spinner fa-pulse':leave.loading[{{$i-1}}]}"></i>
                                            </a>
                                            <a href="#" ng-disabled="leave.loading[{{$i-1}}]"
                                               ng-click="leave.editing[{{$i-1}}]=0;leave.status[{{$i-1}}]=''"
                                               class="btn btn-danger btn-xs" ng-if="leave.editing[{{$i-1}}]">
                                                <i class="fa fa-close"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="bg-warning">No Data Available</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>