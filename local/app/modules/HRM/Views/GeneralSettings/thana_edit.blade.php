{{--User: Shreya--}}
{{--Date: 12/5/2015--}}
{{--Time: 12:23 PM--}}
@extends('template.master')
@section('title','Thana Information Edit')
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
                                        <select class="form-control" id="division_id" name="division_id" disabled>
                                            <option value="">{{$division->division_name_eng}}</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="id" class="form-control" value="{{ $thana_info->id }}">
                                <div class="form-group">
                                    {!! Form::label('unit_id', 'Unit:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8">
                                        <select name="unit_name_eng" class="form-control" id="unit_id" disabled>
                                            <option value="">{{$unit->unit_name_eng}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('thana_name_eng', 'Thana Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': thanaForm.thana_name_eng.$touched && thanaForm.thana_name_eng.$invalid }">
                                        {!! Form::text('thana_name_eng', $value = null, $attributes = array('class' => 'form-control', 'id' => 'thana_name_eng', 'placeholder' => 'Enter Thana Name in English', 'required', 'ng-model' => 'thana_name_eng')) !!}
                                        <span ng-if="thanaForm.thana_name_eng.$touched && thanaForm.thana_name_eng.$error.required"><p
                                                    class="text-danger">Thana Name in English is required.</p></span>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('thana_name_bng', 'থানার নাম:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': thanaForm.thana_name_bng.$touched && thanaForm.thana_name_bng.$invalid }">
                                        {!! Form::text('thana_name_bng', $value = Request::old('thana_name_bng'), $attributes = array('class' => 'form-control', 'id' => 'thana_name_bng', 'placeholder' => 'থানার নাম লিখুন বাংলায়', 'required', 'ng-model' => 'thana_name_bng')) !!}
                                        <span ng-if="thanaForm.thana_name_bng.$touched && thanaForm.thana_name_bng.$error.required"><p
                                                    class="text-danger">Thana Name in Bangla is required.</p></span>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('thana_code', 'Thana Code:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': thanaForm.thana_code.$touched && thanaForm.thana_code.$invalid }">
                                        {!! Form::text('thana_code', $value = Request::old('thana_code'), $attributes = array('class' => 'form-control', 'id' => 'thana_code', 'placeholder' => 'Enter Thana Code', 'required', 'ng-model' => 'thana_code')) !!}
                                        <span ng-if="thanaForm.thana_code.$touched && thanaForm.thana_code.$error.required"><p
                                                    class="text-danger">Thana Code is required.</p></span>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                    <div>
                        <button type="submit" class="btn btn-info pull-right"
                                ng-disabled="thanaForm.thana_name_eng.$error.required||thanaForm.thana_name_bng.$error.required||thanaForm.thana_code.$error.required">
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