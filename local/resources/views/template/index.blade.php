@extends('template.master')
@section('title','Dashboard')
@section('small_title','Control panel')
@section('content')
        <!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <a href="{{URL::to('HRM')}}" class="small-box-footer">
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>HRM</h3>

                        <p style="color: black">Human Resource Management</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="small-box-footer" style="height: 15px"></div>
                </div>
            </div>
        </a>
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
            <div class="small-box bg-yellow disable-module">
                <div class="inner">
                    <h3>SD</h3>

                    <p>Salary Disbursement</p>
                </div>
                <div class="icon">
                    <i class="fa  fa-money"></i>
                </div>
                <div class="small-box-footer disable-module" style="height: 15px; background: #ADADAD"></div>
                {{--<a href="{{URL::to('SD')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red disable-module">
                <div class="inner">
                    <h3>ADAPS</h3>

                    <p>Deployment Application Processing System</p>
                </div>
                <div class="icon">
                    <i class="fa fa-gears"></i>
                </div>
                <div class="small-box-footer disable-module" style="height: 15px; background: #ADADAD"></div>
                {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->
</section>
@endsection