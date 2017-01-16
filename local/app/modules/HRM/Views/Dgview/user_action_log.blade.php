@extends('template.master')
@section('title','User Action Log')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('user_action_log') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('UserActionLog', function ($scope, $http, $sce) {
            $scope.data = $sce.trustAsHtml("")
            $scope.formData = {};
            $scope.loading = false;
            $scope.loadLog = function () {
                $scope.loading = true;
                $http({
                    url:'{{URL::route('user_action_log')}}',
                    data:angular.toJson($scope.formData),
                    method:'post'
                }).then(function (response) {
                    console.log(response.data)
                    $scope.data = $sce.trustAsHtml(response.data)
                    $scope.loading = false;
                }, function (response) {
                    $scope.loading = false;
                })
            }
        })
    </script>
    <section class="content">
        <div class="box box-solid" ng-controller="UserActionLog">
            <div class="box-body">
                <form>
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="user_name">User Name</label>
                                <input type="text" ng-model="formData.user_name" class="form-control" name="user_name" placeholder="Enter User Name">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="user_name">From Date</label>
                                <input type="text" ng-model="formData.from_date"  date-picker class="form-control" name="from_date"
                                       placeholder="From Date">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="user_name">To Date</label>
                                <input type="text" ng-model="formData.to_date"   date-picker class="form-control" name="to_date"
                                       placeholder="To Date">
                            </div>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary pull-right" ng-click="loadLog()" ng-disabled="loading"><i ng-if="loading" class="fa fa-spinner fa-pulse"></i>&nbsp;View Action Log</button>
                        <div class="clearfix"></div>

                    </div>
                </form>
                <div style="padding-top: 20px">
                    <div ng-bind-html="data">

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection