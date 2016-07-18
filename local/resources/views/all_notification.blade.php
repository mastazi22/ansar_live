{{--User: Shreya--}}
{{--Date: 10/15/2015--}}
{{--Time: 10:49 AM--}}

@extends('template.master')
@section('title','All notification')
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
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content">

            <div class="box box-solid">
                <div class="box-body">
                    <ul>
                        @forelse(Notification::getAllNotification() as $notification)
                            <li>
                                <a href="#">
                                    <i class="fa fa-users text-aqua"></i>
                                    <blockquote>{{$notification->user_name}}</blockquote>
                                    forget password. Change his password
                                </a>
                            </li>
                            @empty
                            <li>No forget password available</li>
                        @endforelse
                    </ul>
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