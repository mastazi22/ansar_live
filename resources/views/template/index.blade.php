@extends('template.master')
        @section('title','Dashboard')
        @section('small_title','Control panel')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>HRM</h3>

                        <p>Human Resource Management</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            {{--<div class="col-lg-4 col-xs-6">--}}
                {{--<!-- small box -->--}}
                {{--<div class="small-box bg-green">--}}
                    {{--<div class="inner">--}}
                        {{--<h3>PM</h3>--}}

                        {{--<p>Payroll Management</p>--}}
                    {{--</div>--}}
                    {{--<div class="icon">--}}
                        {{--<i class="fa fa-calculator"></i>--}}
                    {{--</div>--}}
                    {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                {{--</div>--}}
            {{--</div>--}}
            <!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>SD</h3>

                        <p>Salary Disbursement</p>
                    </div>
                    <div class="icon">
                        <i class="fa  fa-money"></i>
                    </div>
                    <a href="{{URL::to('SD')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>ADAPS</h3>

                        <p>Deployment Application Processing System</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-gears"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- /.row -->
    </section>
@endsection