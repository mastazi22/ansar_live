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
                                <th>Action</th>
                            </tr>
                            <?php $i = 0;?>
                            @forelse($present_list as $present)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$present->ansar->ansar_id}}</td>
                                    <td>{{$present->ansar->ansar_name_bng}}</td>
                                    <td>{{$present->ansar->embodiment->kpi->kpi_name}}</td>
                                    <td>{{$present->ansar->division->division_name_bng}}</td>
                                    <td>{{$present->ansar->district->unit_name_bng}}</td>
                                    <td>{{$present->ansar->thana->thana_name_bng}}</td>
                                    <td>
                                        <span ng-if="!present.editing[{{$i-1}}]">Present</span>
                                        <select ng-if="present.editing[{{$i-1}}]" ng-model="moga[{{$i-1}}]">
                                            <option value="">Select a status</option>
                                            <option value="absent">Absent</option>
                                            <option value="leave">Leave</option>
                                        </select>
                                    </td>
                                    <td>
                                        <a href="#" ng-click="enableEditing({{$i-1}})" class="btn btn-primary btn-xs" ng-if="!present.editing[{{$i-1}}]">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="#" ng-click="present.isEditing[{{$i-1}}]=0" class="btn btn-primary btn-xs" ng-if="present.editing[{{$i-1}}]">
                                            <i class="fa fa-save"></i>
                                        </a>
                                        <a href="#" ng-click="present.editing[{{$i-1}}]=0" class="btn btn-danger btn-xs" ng-if="present.editing[{{$i-1}}]">
                                            <i class="fa fa-close"></i>
                                        </a>
                                    </td>
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
                                <th>Action</th>
                            </tr>
                            <?php $i = 0;?>
                            @forelse($absent_list as $absent)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$absent->ansar->ansar_id}}</td>
                                    <td>{{$absent->ansar->ansar_name_bng}}</td>
                                    <td>{{$absent->ansar->embodiment->kpi->kpi_name}}</td>
                                    <td>{{$absent->ansar->division->division_name_bng}}</td>
                                    <td>{{$absent->ansar->district->unit_name_bng}}</td>
                                    <td>{{$absent->ansar->thana->thana_name_bng}}</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-xs">
                                            <i class="fa fa-edit"></i>&nbsp;Edit Attendance
                                        </a>
                                    </td>
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
                                <th>Action</th>
                            </tr>
                            <?php $i = 0;?>
                            @forelse($leave_list as $leave)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$leave->ansar->ansar_id}}</td>
                                    <td>{{$leave->ansar->ansar_name_bng}}</td>
                                    <td>{{$leave->ansar->embodiment->kpi->kpi_name}}</td>
                                    <td>{{$leave->ansar->division->division_name_bng}}</td>
                                    <td>{{$leave->ansar->district->unit_name_bng}}</td>
                                    <td>{{$leave->ansar->thana->thana_name_bng}}</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-xs">
                                            <i class="fa fa-edit"></i>&nbsp;Edit Attendance
                                        </a>
                                    </td>
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