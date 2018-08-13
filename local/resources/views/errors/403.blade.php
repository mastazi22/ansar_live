@extends('template.master')
@section('title','404 Error')
@section('breadcrumb')
    {{--{!! Breadcrumbs::generate() !!}--}}
    @endsection
@section('content')
    <div>
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-yellow" style="margin-top: -10px"> 403</h2>

                <div class="error-content">
                    <h3><i class="fa fa-warning text-yellow"></i> Oops! This Page is FORBIDDEN for you.</h3>

                    <p style="margin-top: 20px">you may <a href="{{URL::to('/')}}">return to dashboard</a>
                    </p>

                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
@stop