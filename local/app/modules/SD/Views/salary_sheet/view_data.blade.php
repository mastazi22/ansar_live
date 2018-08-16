<?php $i = (intVal($history->currentPage() - 1) * $history->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total({{$history->total()}})</span>
        </caption>

        <tr>
            <th>#</th>
            <th>KPI name</th>
            <th>KPI Division</th>
            <th>KPI District</th>
            <th>KPI thana</th>
            <th>Generated Date</th>
            <th>Generate For Month</th>
            <th>Type</th>
            <th>Disburse status</th>
            <th>Deposit status</th>
            <th style="width:100px;">Action</th>

        </tr>

        @if(count($history))
            @foreach($history as $info)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$info->kpi->kpi_name}}</td>
                    <td>{{$info->kpi->division->division_name_bng}}</td>
                    <td>{{$info->kpi->unit->unit_name_bng}}</td>
                    <td>{{$info->kpi->thana->thana_name_bng}}</td>
                    <td>{{\Carbon\Carbon::parse($info->generated_date)->format('d-M-Y')}}</td>
                    <td>{{$info->generated_for_month}}</td>
                    <td>{{$info->generated_type}}</td>
                    <td>
                        @if($info->disburst_status=="pending")
                            <span class="label label-warning">pending</span>
                        @elseif($info->disburst_status=="done")
                            <span class="label label-success">done</span>
                        @else
                            <span class="label label-danger">canceled</span><br>
                            ({{$info->cancel_reason}})
                        @endif
                    </td>
                    <td>
                        @if(!$info->deposit)
                            <span class="label label-danger">Not Available</span>
                        @elseif($info->generated_type=="salary"&&$info->deposit->paid_amount<$info->summery["total_max_amount"])
                            <span class="label label-warning">Partial</span>
                        @elseif($info->generated_type=="bonus"&&$info->deposit->paid_amount<$info->salaryHistory->sum('amount'))
                            <span class="label label-warning">Partial</span>
                        @else
                            <span class="label label-success">Fully Deposit</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-primary btn-xs" ng-click="viewDetails('{{$info->id}}')">
                            <i class="fa fa-eye"></i>
                        </button>
                        <a title="export detail in excel" class="btn btn-primary btn-xs" href="{{URL::to('SD/salary_management',$info->id)}}?type=export">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                        <a title="download payroll" class="btn btn-primary btn-xs" href="{{URL::route('SD.salary_management.view_payroll_by_id',$info->id)}}">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="9" class="bg-warning">
                    No Payment History Available
                </td>
            </tr>
        @endif

    </table>
</div>
@if($history->total()>$history->perPage())
    <div style="overflow: hidden">
        <div class="pull-left">
            <select name="" id="" ng-model="param.limit" ng-change="loadData()">
                <option value="30">30</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="500">500</option>
            </select>
        </div>
        <div class="pull-right" style="margin: -20px 0" paginate ref="loadData(url)">
            {{$history->render()}}
        </div>
    </div>
@endif
