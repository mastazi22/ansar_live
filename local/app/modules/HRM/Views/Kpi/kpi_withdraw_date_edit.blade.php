{{--User: Shreya--}}
{{--Date: 10/14/2015--}}
{{--Time: 12:00 PM--}}

@extends('template.master')
@section('title','KPI Withdrawal Date Edit')
@section('breadcrumb')
    {!! Breadcrumbs::render('kpi_withdrawal_date_edit_form', $id) !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#withdraw-date').datepicker({
                dateFormat:'dd-M-yy'
            });
        })
    </script>
    <style>
        .form-horizontal .control-label {
            padding-top: 7px;
            margin-bottom: 0;
            text-align: left;
        }
    </style>
    <div>
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('withdraw_date_update') !!}--}}
        {{--</div>--}}
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('entryform') !!}--}}
        {{--</div>--}}
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            @if($errors->has('id'))
                <div style="padding: 10px 20px 0 20px;">
                    <div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        Invalid Request
                    </div>
                </div>
            @endif
            <div class="row">
                <!-- left column -->
                <div class="col-lg-8 col-centered">
                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">

                        <div class="box-body">

                            {!! Form::open(array('route' => 'withdraw-date-update', 'class' => 'form-horizontal')) !!}
                            <div class="box-body">
                                <input type="hidden" name="id" class="form-control" value="{{ $kpi_details->id }}">

                                <div class="form-group">
                                    {!! Form::label('kpi_name', 'KPI Name:', $attributes = array('class' => 'col-md-3 control-label')) !!}
                                    <div class="col-md-9">
                                        {!! Form::text('kpi_name', $value = $kpi_info->kpi_name, $attributes = array('class' => 'form-control', 'id' => 'kpi_name', 'disabled')) !!}
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('withdraw-date', 'Withdraw Date:', $attributes = array('class' => 'col-md-3 control-label')) !!}
                                    <div class="col-md-9">
                                        {!! Form::text('withdraw-date', $value =  (Request::old('withdraw-date')?Request::old('withdraw-date'):\Carbon\Carbon::createFromFormat('Y-m-d',$kpi_details->kpi_withdraw_date)->format('d-M-Y')), $attributes = array('class' => 'form-control', 'id' => 'withdraw-date', 'placeholder' => 'Enter Withdraw Date')) !!}
                                        @if($errors->has('withdraw-date'))
                                            <p class="text-danger">{{$errors->first('withdraw-date')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('mem_id', 'Memorandum No.:', $attributes = array('class' => 'col-md-3 control-label')) !!}
                                    <div class="col-md-9">
                                        {!! Form::text('mem_id', null, $attributes = array('class' => 'form-control','placeholder'=>'Memorandum No.')) !!}
                                    </div>
                                </div>

                            </div>
                            <!-- /.box-body -->


                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                    <div>
                        <button type="submit" class="btn btn-info pull-right">Update</button>
                    </div>
                    <!-- /.box-footer -->
                    {!! Form::close() !!}

                </div>
                <!--/.col (left) -->
                <!-- right column -->

            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection