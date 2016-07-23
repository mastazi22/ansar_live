{{--User: Shreya--}}
{{--Date: 12/5/2015--}}
{{--Time: 12:23 PM--}}
@extends('template.master')
@section('title','Edit Thana Information')
@section('breadcrumb')
    {!! Breadcrumbs::render('thana_information_edit',$id) !!}
@endsection
@section('content')
    <script>

        GlobalApp.controller('ThanaController', function ($scope) {
            $scope.thana_name_eng = '{{$thana_info->thana_name_eng}}';
            $scope.thana_name_bng = '{{$thana_info->thana_name_bng}}';
            $scope.thana_code = '{{$thana_info->thana_code}}';
        });
    </script>

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
            <div class="col-lg-6 col-centered">
                {{--<div class="label-title-session-entry">
                    <h4 style="text-align:center; padding:2px">Edit Thana Form</h4>
                </div>--}}
                <div class="box box-info">
                    <div class="box-body">

                        {!! Form::open(array('route' => 'thana_update', 'class' => 'form-horizontal', 'name' => 'thanaForm', 'ng-controller' => 'ThanaController', 'novalidate')) !!}
                        <div class="box-body">
                            <div class="form-group">
                                {!! Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('thana_name_eng', $value = $division->division_name_eng, $attributes = array('class' => 'form-control', 'disabled')) !!}
                                </div>
                            </div>
                            <input type="hidden" name="id" class="form-control" value="{{ $thana_info->id }}">
                            <div class="form-group">
                                {!! Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('thana_name_eng', $value = $unit->unit_name_eng, $attributes = array('class' => 'form-control', 'disabled')) !!}
                                </div>
                            </div>
                            <div class="form-group required">
                                {!! Form::label('thana_name_eng', 'Thana Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                <div class="col-sm-8 @if($errors->has('thana_name_eng')) has-error @endif">
                                    {!! Form::text('thana_name_eng', $value = null, $attributes = array('class' => 'form-control', 'id' => 'thana_name_eng', 'placeholder' => 'Enter Thana Name in English')) !!}
                                    @if($errors->has('thana_name_eng'))
                                        <p class="text-danger">{{$errors->first('thana_name_eng')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group required">
                                {!! Form::label('thana_name_bng', 'থানার নাম:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                <div class="col-sm-8 @if($errors->has('thana_name_bng')) has-error @endif">
                                    {!! Form::text('thana_name_bng', $value = Request::old('thana_name_bng'), $attributes = array('class' => 'form-control', 'id' => 'thana_name_bng', 'placeholder' => 'থানার নাম লিখুন বাংলায়')) !!}
                                    @if($errors->has('thana_name_bng'))
                                        <p class="text-danger">{{$errors->first('thana_name_bng')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group required">
                                {!! Form::label('thana_code', 'Thana Code:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                <div class="col-sm-8 @if($errors->has('thana_code')) has-error @endif">
                                    {!! Form::text('thana_code', $value = Request::old('thana_code'), $attributes = array('class' => 'form-control', 'id' => 'thana_code', 'placeholder' => 'Enter Thana Code')) !!}
                                    @if($errors->has('thana_code'))
                                        <p class="text-danger">{{$errors->first('thana_code')}}</p>
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
                        Update
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
@endsection