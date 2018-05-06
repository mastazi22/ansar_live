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
                            <th>View Sheet</th>
                        </tr>
                        @foreach($logs as $log)
                            @foreach($log->demand as $demand)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$log->kpi_name}}</td>
                                    <td>{{\Carbon\Carbon::parse($demand->form_date)->format('d M, Y')}}</td>
                                    <td>{{\Carbon\Carbon::parse($demand->to_date)->format('d M, Y')}}</td>
                                    <td>{{\Carbon\Carbon::parse($demand->generated_date)->format('d M, Y')}}</td>
                                    <td>
                                        <a target="_blank" href="{{url('SD/viewdemandsheet',['id'=>$demand->id])}}">
                                            <i class="fa fa-lg fa-file-pdf-o"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
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