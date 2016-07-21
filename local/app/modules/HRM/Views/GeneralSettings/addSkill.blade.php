@extends('template.master')
@section('title','Entry of Skill Information')
@section('breadcrumb')
    {!! Breadcrumbs::render('skill_information_entry') !!}
@endsection
@section('content')
    <div>

        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-lg-8 col-centered">
                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">
                        <div class="box-body">

                            {!! Form::open(array('url' => 'HRM/skill_entry', 'class' => 'form-horizontal','method' => 'post')) !!}
                            <div class="box-body">
                                <div class="form-group required">
                                    {!! Form::label('skill_name_eng', 'Skill Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('skill_name_eng')) has-error @endif">
                                        {!! Form::text('skill_name_eng', $value = Request::old('skill_name_eng'), $attributes = array('class' => 'form-control', 'id' => 'skill_name_eng', 'placeholder' => 'Enter Skill Name in English')) !!}
                                        @if($errors->has('skill_name_eng'))
                                            <p class="text-danger">{{$errors->first('skill_name_eng')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('skill_name_bng', 'দক্ষতার নাম:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('skill_name_bng')) has-error @endif">
                                        {!! Form::text('skill_name_bng', $value = Request::old('skill_name_bng'), $attributes = array('class' => 'form-control', 'id' => 'skill_name_bng', 'placeholder' => 'দক্ষতার নাম লিখুন বাংলায়')) !!}
                                        @if($errors->has('skill_name_bng'))
                                            <p class="text-danger">{{$errors->first('skill_name_bng')}}</p>
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
                        <button type="submit" class="btn btn-info pull-right">Submit</button>
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