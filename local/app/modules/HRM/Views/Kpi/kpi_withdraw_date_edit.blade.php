{{--User: Shreya--}}
{{--Date: 10/14/2015--}}
{{--Time: 12:00 PM--}}

@extends('template.master')
@section('content')
<script>
    $(document).ready(function () {
        $('#withdraw-date').datePicker(false);
    })
</script>
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
            <div class="row">
                <!-- left column -->
                <div class="col-lg-8 col-centered">
                    <div class="label-title-session-entry">
                        <h4 style="text-align:center; padding:2px">Date Update Form</h4>
                    </div>
                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">

                        <div class="box-body">

                            {!! Form::open(array('route' => 'withdraw-date-update', 'class' => 'form-horizontal')) !!}
                            <div class="box-body">
                                <input type="hidden" name="id" class="form-control" value="{{ $kpi_details->id }}">
                                <div class="form-group">
                                    {!! Form::label('kpi_name', 'KPI Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            {!! Form::text('kpi_name', $value = $kpi_info->kpi_name, $attributes = array('class' => 'form-control', 'id' => 'kpi_name', 'disabled')) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('withdraw-date', 'Withdraw Date:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8">
                                        <div class="input-group">

                                            {!! Form::text('withdraw-date', $value =  \Carbon\Carbon::createFromFormat('Y-m-d',$kpi_details->kpi_withdraw_date)->format('d-M-Y'), $attributes = array('class' => 'form-control', 'id' => 'withdraw-date', 'placeholder' => 'Enter Withdraw Date')) !!}
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->



                        </div><!-- /.box-body -->

                    </div><!-- /.box -->
                    <div >
                        <button type="submit" class="btn btn-info pull-right">Update</button>
                    </div><!-- /.box-footer -->
                    {!! Form::close() !!}

                </div><!--/.col (left) -->
                <!-- right column -->

            </div>   <!-- /.row -->
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection