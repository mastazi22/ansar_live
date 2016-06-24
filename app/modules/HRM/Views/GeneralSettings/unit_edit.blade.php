{{--User: Shreya--}}
{{--Date: 12/5/2015--}}
{{--Time: 12:23 PM--}}

@extends('template/master')
@section('content')
    <script>
        GlobalApp.controller('UnitEditController', function ($scope) {
            $scope.unit_name_eng = '{{$unit_info->unit_name_eng}}';
            $scope.unit_name_bng = '{{$unit_info->unit_name_bng}}';
            $scope.unit_code = '{{$unit_info->unit_code}}';
        })
    </script>
    <div>
        {!! Form::open(array('url' => 'unit_update', 'class' => 'form-horizontal', 'ng-controller' => 'UnitEditController','name' => 'unitForm',)) !!}
                <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-lg-6 col-centered">
                    <div class="label-title-session-entry">
                        <h4 style="text-align:center; padding:2px">Edit Unit Form</h4>
                    </div>
                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">
                        <div class="box-body">
                            <div class="box-body">
                                <div class="form-group">
                                    {!! Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8">
                                        <select class="form-control" id="division_id"
                                                name="division_id" disabled>
                                            <option value="">{{$division->division_name_eng}}</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="id" class="form-control" value="{{ $unit_info->id }}">
                                <div class="form-group required">
                                    {!! Form::label('unit_name_eng', 'Unit Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': unitForm.unit_name_eng.$touched && unitForm.unit_name_eng.$invalid }">
                                        {!! Form::text('unit_name_eng', $value = null, $attributes = array('class' => 'form-control', 'id' => 'unit_name_eng', 'placeholder' => 'Enter Unit Name in English', 'required', 'ng-model' => 'unit_name_eng')) !!}
                                        <span ng-if="unitForm.unit_name_eng.$touched && unitForm.unit_name_eng.$error.required"><p
                                                    class="text-danger">Unit Name in English is required.</p></span>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('unit_name_bng', 'জেলার নাম:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': unitForm.unit_name_bng.$touched && unitForm.unit_name_bng.$invalid }">
                                        {!! Form::text('unit_name_bng', $value = null, $attributes = array('class' => 'form-control', 'id' => 'unit_name_bng', 'placeholder' => 'জেলার নাম লিখুন বাংলায়', 'required', 'ng-model' => 'unit_name_bng')) !!}
                                        <span ng-if="unitForm.unit_name_bng.$touched && unitForm.unit_name_bng.$error.required"><p
                                                    class="text-danger">Unit Name in Bangla is required.</p></span>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('unit_code', 'Unit Code:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': unitForm.unit_code.$touched && unitForm.unit_code.$invalid }">
                                        {!! Form::text('unit_code', $value = null, $attributes = array('class' => 'form-control', 'id' => 'unit_code', 'placeholder' => 'Enter Unit Code in English', 'required', 'ng-model' => 'unit_code')) !!}
                                        <span ng-if="unitForm.unit_code.$touched && unitForm.unit_code.$error.required"><p
                                                    class="text-danger">Unit Code is required.</p></span>
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
                                ng-disabled="unitForm.unit_name_eng.$error.required||unitForm.unit_name_bng.$error.required||unitForm.unit_code.$error.required">
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
    </div><!-- /.content-wrapper -->
@endsection