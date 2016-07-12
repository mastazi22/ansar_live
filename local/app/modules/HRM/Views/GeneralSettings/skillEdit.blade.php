@extends('template/master')
@section('content')

    <div>

        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-lg-6 col-centered">
                    <div class="label-title-session-entry">
                        <h4 style="text-align:center; padding:2px">Edit Skill</h4>
                    </div>
                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">
                        <div class="box-body">

                            {!! Form::open(array('route' => 'skill_update', 'class' => 'form-horizontal')) !!}
                            <div class="box-body">
                                
                                <input type="hidden" name="id" class="form-control" value="{{ $skill_infos->id }}">
                                <div class="form-group">
                                    {!! Form::label('skill_name_eng', 'Skill Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('skill_name_eng')) has-error @endif">
                                        {!! Form::text('skill_name_eng', $value = (Request::old('skill_name_eng')) ? Request::old('skill_name_eng') : $skill_infos->skill_name_eng, $attributes = array('class' => 'form-control', 'id' => 'skill_name_eng', 'placeholder' => 'Enter Skill Name in English')) !!}
                                        @if($errors->has('skill_name_eng'))
                                            <p class="text-danger">{{$errors->first('skill_name_eng')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('skill_name_bng', 'দক্ষতার নাম:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('skill_name_bng')) has-error @endif">
                                        {!! Form::text('skill_name_bng', $value = (Request::old('skill_name_bng')) ? Request::old('skill_name_bng') : $skill_infos->skill_name_bng, $attributes = array('class' => 'form-control', 'id' => 'skill_name_bng', 'placeholder' => 'দক্ষতার নাম লিখুন বাংলায়')) !!}
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
