{{--User: Shreya--}}
{{--Date: 10/15/2015--}}
{{--Time: 10:49 AM--}}

@extends('template.master')
@section('title','All User Request')
@section('content')

    <div>
        <div id="all-loading"
             style="position:fixed;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.27);z-index: 100; margin-left: 30%; display: none; overflow: hidden">
            <div style="position: fixed;width: 20%;height: auto;margin: 20% auto;text-align: center;background: #FFFFFF">
                <img class="img-responsive" src="{{asset('dist/img/loading-data.gif')}}"
                     style="position: relative;margin: 0 auto">
                <h4>Loading....</h4>
            </div>

        </div>
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        @if(Session::has('success'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success')}}
                </div>
            </div>
        @endif
        @if(Session::has('error'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-warning-sign"></span> {{Session::get('error')}}
                </div>
            </div>
        @endif
        <section class="content">
            <?php $i=1; ?>
            <div class="box box-solid">
                <div class="box-body">
                    <table class="table table-stripped table-bordered table-condensed">
                        <tr>
                            <th>Sl. no</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile no</th>
                            <th>User type</th>
                            <th>Request by</th>
                            <th>Action</th>

                        </tr>
                        @forelse(Notification::getAllUserRequestNotification() as $notification)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$notification->first_name.' '.$notification->last_name}}</td>
                                <td>{{$notification->email}}</td>
                                <td>{{$notification->mobile_no}}</td>
                                <td>{{$notification->user_type}}</td>
                                <td>{{$notification->user->user_name}}</td>
                                <td>
                                    <form method="post" action="{{URL::to("/cancel_user_request/{$notification->id}")}}" style="display: inline-block;">
                                        {!! csrf_field() !!}
                                        <button type="submit" class="btn btn-danger btn-xs">Cancel</button>
                                    </form>
                                    <form method="post" action="{{URL::to("/approved_user_request/{$notification->id}")}}" style="display: inline-block;">
                                        {!! csrf_field() !!}
                                        <button type="submit" class="btn btn-primary btn-xs">Approve</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="bg-warning">No request available</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
            <!-- /.box
            -footer -->
            <!--Modal Close-->
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection