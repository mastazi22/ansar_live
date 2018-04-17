{{--User: Shreya--}}
{{--Date: 12/3/2015--}}
{{--Time: 1:22 PM--}}

@extends('template.master')
@section('title','Entry of Union Information')
@section('breadcrumb')
    {!! Breadcrumbs::render('unit_information_entry') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('UnionEntryController', function ($scope,$http,httpService,notificationService) {
            $scope.data = {};
            httpService.range().then(function (response) {
             $scope.divisions = response;
            })
            $scope.loadUnit = function (rangeId) {
                httpService.unit(rangeId).then(function (response) {
                    $scope.units = response;
                })
            }
            $scope.loadThana = function (rangeId,unitId) {
                httpService.thana(rangeId,unitId).then(function (response) {
                    $scope.thanas = response;
                })
            }
            $scope.submitData = function () {
                $http({
                    method:'post',
                    url:'{{URL::route('HRM.union.store')}}',
                    data:angular.toJson($scope.data)
                }).then(function (response) {
                    window.location.href = '{{URL::route('HRM.union.index')}}'
                },function (response) {
                    notificationService.notify('error',response.data.message)
                })
            }
        })
    </script>
    <div>

        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-lg-6 col-centered">

                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">
                        <div class="box-body" ng-controller="UnionEntryController">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="union_name_eng" class="control-label">
                                        Union Name Eng
                                    </label>
                                    <input type="text" class="form-control" ng-model="data.union_name_eng" placeholder="Union name in english">
                                </div>
                                <div class="form-group">
                                    <label for="union_name_bng" class="control-label">
                                        Union Name Bng
                                    </label>
                                    <input type="text" class="form-control" ng-model="data.union_name_bng" placeholder="Union name in bangla">
                                </div>
                                <div class="form-group">
                                    <label for="code" class="control-label">
                                        Union Code
                                    </label>
                                    <input type="text" class="form-control" ng-model="data.code" placeholder="Union code">
                                </div>
                                <div class="form-group">
                                    <label for="division_id" class="control-label">
                                        Division
                                    </label>
                                    <select class="form-control" ng-model="data.division_id" ng-change="loadUnit(data.division_id)">
                                        <option value="">--Select a division--</option>
                                        <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="unit_id" class="control-label">
                                        Unit
                                    </label>
                                    <select class="form-control" ng-model="data.unit_id" ng-change="loadThana(data.division_id,data.unit_id)">
                                        <option value="">--Select a unit--</option>
                                        <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="thana_id" class="control-label">
                                        Thana
                                    </label>
                                    <select class="form-control" ng-model="data.thana_id">
                                        <option value="">--Select a thana--</option>
                                        <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary pull-right" ng-click="submitData()">Submit</button>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box-body -->

                    </div>

                </div>
                <!--/.col (left) -->
                <!-- right column -->

            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection