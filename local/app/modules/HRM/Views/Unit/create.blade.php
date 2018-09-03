{{--User: Shreya--}}
{{--Date: 12/3/2015--}}
{{--Time: 1:22 PM--}}

@extends('template.master')
@section('title','Entry of Unit Information')
@section('breadcrumb')
    {!! Breadcrumbs::render('unit_information_entry') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('UnitEntryController', function () {
        })
    </script>
    <div>

        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            {!! Form::model(Request::old(),array('route' => 'HRM.unit.store','method'=>'post','name' => 'unitForm', 'class' => 'form-horizontal')) !!}
            <div class="row">
                <!-- left column -->
                <div class="col-lg-6 col-centered">

                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">
                        <div class="box-body">
                            <div class="box-body">
                                <div class="form-group required">
                                    {!! Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('division_id')) has-error @endif">
                                        {!! Form::select('division_id',$range,null,['class'=>'form-control']) !!}
                                        @if($errors->has('division_id'))
                                            <p class="text-danger">{{$errors->first('division_id')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('unit_name_eng', 'Unit Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('unit_name_eng')) has-error @endif">
                                        {!! Form::text('unit_name_eng',null, $attributes = array('class' => 'form-control', 'id' => 'unit_name_eng', 'placeholder' => 'Enter Unit Name in English')) !!}
                                        @if($errors->has('unit_name_eng'))
                                            <p class="text-danger">{{$errors->first('unit_name_eng')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('unit_name_bng', 'জেলার নাম:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('unit_name_bng')) has-error @endif">
                                        {!! Form::text('unit_name_bng', null, $attributes = array('class' => 'form-control', 'id' => 'unit_name_bng', 'placeholder' => 'জেলার নাম লিখুন বাংলায়')) !!}
                                        @if($errors->has('unit_name_bng'))
                                            <p class="text-danger">{{$errors->first('unit_name_bng')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('unit_code', 'Unit Code:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('unit_code')) has-error @endif">
                                        {!! Form::text('unit_code', null, $attributes = array('class' => 'form-control', 'id' => 'unit_code', 'placeholder' => 'Enter Unit Code in English')) !!}
                                        @if($errors->has('unit_code'))
                                            <p class="text-danger">{{$errors->first('unit_code')}}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                    <div>
                        <button type="submit" class="btn btn-info pull-right">
                            Submit
                        </button>
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