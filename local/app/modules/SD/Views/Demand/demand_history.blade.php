@extends('template.master')
@section('content')
    <section class="content-header">
        <h1>Demand History</h1>
    </section>
    <section class="content" ng-controller="demandSheetController">
        <div class="box box-primary">
            <!-- form start -->

            <div class="box-body">
                <div class="table-responsive">
                    <?php $i=(($logs->currentPage()-1)*$logs->perPage())+1; ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl no</th>
                            <th>KPI Name</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Generated Date</th>
                            <th>Memorandum ID</th>
                            <th>Total Amount</th>
                            <th>Total Min Amount</th>
                            <th>View Sheet</th>
                        </tr>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$log->kpi->kpi_name}}</td>
                                <td>{{\Carbon\Carbon::parse($log->form_date)->format('d M, Y')}}</td>
                                <td>{{\Carbon\Carbon::parse($log->to_date)->format('d M, Y')}}</td>
                                <td>{{\Carbon\Carbon::parse($log->generated_date)->format('d M, Y')}}</td>
                                <td>{{$log->memorandum_no}}</td>
                                <td>{{$log->total_amount}}</td>
                                <td>{{$log->total_min_paid_amount}}</td>
                                <td>
                                    <a target="_blank" href="{{url('SD/viewdemandsheet',['id'=>$log->id])}}">
                                        <i class="fa fa-lg fa-file-pdf-o"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="bg-warning">No History Available</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div style="float: right">
                    {!! $logs->render() !!}
                </div>
            </div><!-- /.box-body -->

            <div class="box-footer">

            </div>

        </div>
    </section>
@endsection