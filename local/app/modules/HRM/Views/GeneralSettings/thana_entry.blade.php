{{--User: Shreya--}}
{{--Date: 12/3/2015--}}
{{--Time: 1:23 PM--}}
@extends('template.master')
@section('title','Entry of Thana Information')
@section('breadcrumb')
    {!! Breadcrumbs::render('thana_information_entry') !!}
@endsection
@section('content')
    <script>

        GlobalApp.controller('ThanaEntryController', function ($scope, getNameService) {

            $scope.division=[];
            $scope.districtLoad = false;
            getNameService.getDivision().then(function (response) {
                $scope.division = response.data;
            });
            $scope.SelectedItemChanged = function () {
                $scope.districtLoad = true;
                getNameService.getDistric($scope.SelectedDivision).then(function (response) {
                    $scope.district = response.data;
                    $scope.districtLoad = false;
                })
            }
            @if(!is_null(Request::old('division_name_eng')))
                $scope.SelectedDivision = parseInt('{{Request::old('division_name_eng')}}');
            $scope.SelectedItemChanged();
            @endif
        });
        GlobalApp.factory('getNameService', function ($http) {
            return {
                getDivision: function () {
                    return $http.get("{{URL::to('HRM/DivisionName')}}");
                },
                getDistric: function (data) {

                    return $http.get("{{URL::to('HRM/DistrictName')}}", {params: {id: data}});
                }
            }

        });
    </script>


    <div>

        <!-- Content Header (Page header) -->
               <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-lg-6 col-centered">
                    {{--<div class="label-title-session-entry">
                        <h4 style="text-align:center; padding:2px">Thana Form</h4>
                    </div>--}}
                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">
                        <div class="box-body">
                            {{--{{var_dump(Request::old())}}--}}
                            <div class="box-body">
                                {!! Form::model(Request::old(),array('url' => 'HRM/thana_entry', 'class' => 'form-horizontal', 'name' => 'thanaForm', 'ng-controller' => 'ThanaEntryController', 'novalidate')) !!}
                                <div class="form-group required">
                                    {!! Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('division_name_eng')) has-error @endif" ng-init="SelectedDivision='{{Request::old('division_name_eng')}}'">
                                        <select name="division_name_eng" class="form-control" id="division_id"
                                                ng-model="SelectedDivision" ng-change="SelectedItemChanged()">
                                            <option value="">--Select a division--</option>
                                            <option ng-repeat="x in division" value="[[x.id]]" ng-selected="x.id=='{{Request::old('division_name_eng')}}'">[[x.division_name_eng]]</option>
                                        </select>
                                        <i class="fa fa-spinner fa-pulse" ng-show="districtLoad"></i>
                                        @if($errors->has('division_name_eng'))
                                            <p class="text-danger">{{$errors->first('division_name_eng')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('unit_id', 'Unit:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('unit_name_eng')) has-error @endif" ng-init="SelectedDistrict='{{Request::old('unit_name_eng')}}'">
                                        <select name="unit_name_eng" class="form-control" id="unit_id"
                                                ng-model="SelectedDistrict" ng-change="SelectedDistrictChanged()" required>
                                            <option value="">--Select a district--</option>
                                            <option ng-repeat="x in district" value="[[x.id]]" ng-selected="x.id=='{{Request::old('unit_name_eng')}}'">[[x.unit_name_eng]]</option>
                                        </select>
                                        @if($errors->has('unit_name_eng'))
                                            <p class="text-danger">{{$errors->first('unit_name_eng')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('thana_name_eng', 'Thana Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('thana_name_eng')) has-error @endif">
                                        {!! Form::text('thana_name_eng', null, $attributes = array('class' => 'form-control', 'id' => 'thana_name_eng', 'placeholder' => 'Enter Thana Name in English', 'required', 'ng-model' => 'thana_name_eng')) !!}
                                        @if($errors->has('thana_name_eng'))
                                            <p class="text-danger">{{$errors->first('thana_name_eng')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('thana_name_bng', 'থানার নাম:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('thana_name_bng')) has-error @endif">
                                        {!! Form::text('thana_name_bng', null, $attributes = array('class' => 'form-control', 'id' => 'thana_name_bng', 'placeholder' => 'থানার নাম লিখুন বাংলায়', 'required', 'ng-model' => 'thana_name_bng')) !!}
                                        @if($errors->has('thana_name_bng'))
                                            <p class="text-danger">{{$errors->first('thana_name_bng')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('thana_code', 'Thana Code:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8 @if($errors->has('thana_code')) has-error @endif">
                                        {!! Form::text('thana_code', null, $attributes = array('class' => 'form-control', 'id' => 'thana_code', 'placeholder' => 'Enter Thana Code', 'required', 'ng-model' => 'thana_code')) !!}
                                        @if($errors->has('thana_code'))
                                            <p class="text-danger">{{$errors->first('thana_code')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-info pull-right">
                                        Submit
                                    </button>
                                </div>
                                <!-- /.box-footer -->
                                {!! Form::close() !!}
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->


                </div>
                <!--/.col (left) -->
                <!-- right column -->

            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection