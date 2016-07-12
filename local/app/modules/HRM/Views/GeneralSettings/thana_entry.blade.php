{{--User: Shreya--}}
{{--Date: 12/3/2015--}}
{{--Time: 1:23 PM--}}
@extends('template.master')
@section('title','Thana Information Entry')
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
        {!! Form::open(array('url' => 'HRM/thana_entry', 'class' => 'form-horizontal', 'name' => 'thanaForm', 'ng-controller' => 'ThanaEntryController', 'novalidate')) !!}
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
                            <div class="box-body">
                                <div class="form-group required">
                                    {!! Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': thanaForm.division_name_eng.$touched && thanaForm.division_name_eng.$invalid }">
                                        <select name="division_name_eng" class="form-control" id="division_id"
                                                ng-model="SelectedDivision" ng-change="SelectedItemChanged()" required>
                                            <option value="">--Select a division--</option>
                                            <option ng-repeat="x in division" value="[[x.id]]">[[x.division_name_eng]]
                                            </option>
                                        </select>
                                        <i class="fa fa-spinner fa-pulse" ng-show="districtLoad"></i>
                                        <span ng-if="thanaForm.division_name_eng.$touched && thanaForm.division_name_eng.$error.required"><p
                                                    class="text-danger">Division is required.</p></span>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('unit_id', 'Unit:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': thanaForm.unit_name_eng.$touched && thanaForm.unit_name_eng.$invalid }">
                                        <select name="unit_name_eng" class="form-control" id="unit_id"
                                                ng-model="SelectedDistrict" ng-change="SelectedDistrictChanged()" required>
                                            <option value="">--Select a district--</option>
                                            <option ng-repeat="x in district" value="[[x.id]]">[[ x.unit_name_eng ]]
                                            </option>
                                        </select>
                                        <span ng-if="thanaForm.unit_name_eng.$touched && thanaForm.unit_name_eng.$error.required"><p
                                                    class="text-danger">District is required.</p></span>
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
                                        {!! Form::text('thana_name_bng', $value = null, $attributes = array('class' => 'form-control', 'id' => 'thana_name_bng', 'placeholder' => 'থানার নাম লিখুন বাংলায়', 'required', 'ng-model' => 'thana_name_bng')) !!}
                                        <span ng-if="thanaForm.thana_name_bng.$touched && thanaForm.thana_name_bng.$error.required"><p
                                                    class="text-danger">Thana Name in Bangla is required.</p></span>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    {!! Form::label('thana_code', 'Thana Code:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                    <div class="col-sm-8" ng-class="{ 'has-error': thanaForm.thana_code.$touched && thanaForm.thana_code.$invalid }">
                                        {!! Form::text('thana_code', $value = null, $attributes = array('class' => 'form-control', 'id' => 'thana_code', 'placeholder' => 'Enter Thana Code', 'required', 'ng-model' => 'thana_code')) !!}
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
                                ng-disabled="thanaForm.division_name_eng.$error.required||thanaForm.unit_name_eng.$error.required||thanaForm.thana_name_eng.$error.required||thanaForm.thana_name_bng.$error.required||thanaForm.thana_code.$error.required">
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