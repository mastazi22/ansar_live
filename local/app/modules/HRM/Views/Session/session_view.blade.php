{{--User: Shreya--}}
{{--Date: 10/14/2015--}}
{{--Time: 11:00 AM--}}

@extends('template.master')
@section('title','Session Information')
@section('small_title')
    <a class="btn btn-primary btn-sm" href="{{URL::to('HRM/session')}}">
        <span class="glyphicon glyphicon-plus"></span> Add new Session
    </a>

@endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('session_information_list') !!}
@endsection
@section('content')


    <div>
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
            @endif
                    <!-- Content Header (Page header) -->

            <!-- Main content -->
            <section class="content">
                <div class="box box-solid">

                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Session Year</th>
                                <th>Starting Session Month</th>
                                <th>Ending Session Month</th>
                                <th>Session Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($session_info)>0)
                                @foreach($session_info as $session_infos)
                                    <tr>
                                        <td>{{ $session_infos->session_year }}</td>
                                        <td>{{ $session_infos->session_start_month }}</td>
                                        <td>{{ $session_infos->session_end_month }}</td>
                                        <td>{{ $session_infos->session_name }}</td>

                                        <td>
                                            <a href="{{URL::to('HRM/session-edit/'.$session_infos->id)."/"}}@if(Request::exists('page')){{Request::get('page')}}@else{{'1'}}@endif"
                                               class="btn btn-primary btn-xs" title="Edit"><span
                                                        class="glyphicon glyphicon-edit"></span></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="warning">
                                    <td colspan="5">No information found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    <div class="table_pagination">
                        {!! $session_info->render() !!}
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
    </div><!-- /.content-wrapper -->
    <script>
        function check() {
            return confirm('Are you sure to delete this entry');
        }
    </script>

@endsection