@extends('template.master')
@section('title','Action Log('.$user->user_name.')')
@section('breadcrumb')
    {!! Breadcrumbs::render('user_log') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ActionLogController',function ($scope, $http, $sce) {

            $scope.log = {};
            $scope.loading = false;
            $scope.loadActionLog = function () {
                console.log($scope.log);
                $scope.loading = true;
                $http({
                    url:'',
                    method:'get',
                    params:$scope.log
                }).then(function (response) {
                    $scope.data = $sce.trustAsHtml(response.data);
                    $scope.loading = false;
                },function (response) {
                    $scope.data = $sce.trustAsHtml(response.data)
                    $scope.loading = false;
                })
            }

        })
    </script>
    <div ng-controller="ActionLogController">
        <section class="content">
            <div class="box box-solid">

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">

                            <div class="form-group">
                                <label for="" class="control-label">From Date</label>
                                <input type="text"  ng-model="log.to_date" date-picker="moment().subtract(2, 'days').format('DD-MMM-YYYY')" class="form-control" name="from_date" placeholder="From Date">
                            </div>

                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">To Date</label>
                                <input type="text" ng-model="log.from_date" date-picker="moment().format('DD-MMM-YYYY')" class="form-control" name="to_date" placeholder="To Date">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label for="" class="control-label" style="width: 100%">&nbsp;</label>
                            <button ng-disabled="loading" ng-click="loadActionLog()" class="btn btn-primary">
                                <i ng-show="!loading" class="fa fa-search"></i><i ng-show="loading" class="fa fa-spinner fa-pulse"></i>&nbsp;Search</button>
                        </div>
                    </div>
                    <div ng-bind-html="data"></div>
                </div>
            </div>
        </section>
    </div>

@stop