@extends('template.master')
@section('title','Circular Summery')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.circular.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('circularSummery',function ($scope, $http, $q,httpService) {
            $scope.categories = [];
            $scope.circulars = [];
            $scope.circularSummery = [];
            $scope.allStatus = {'all':'All','inactive':'Inactive','active':'Active'}
            $scope.circular = 'all';
            $scope.category = 'all';
            $scope.status = 'active';
            var loadAll = function () {
                $q.all([
                    httpService.category({status: $scope.status}),
                    httpService.circular({status: $scope.status}),
                    httpService.circularSummery({category: $scope.category,circular: $scope.circular})
                ])
                    .then(function (response) {
                        $scope.circular = 'all';
                        $scope.category = 'all';
                        $scope.categories = response[0].data;
                        $scope.circulars = response[1].data;
                        $scope.circularSummery = response[2].data;
                    }, function (response) {
                        $scope.circular = 'all';
                        $scope.category = 'all';
                        $scope.categories = [];
                        $scope.circulars = [];
                        $scope.circularSummery = [];
                        console.log(response);
                    })
            }
            $scope.loadCircular = function (id) {
                httpService.circular({status:$scope.status,category_id:id})
                    .then(function (response) {
                        $scope.circular = 'all';
                        $scope.circulars = response.data;
                    },function (response) {
                        $scope.circular = 'all';
                        $scope.circulars = [];
                        console.log(response);
                    })
            }
            $scope.loadApplicant = function (category, circular) {

            }
            $scope.statusChange = function () {
                loadAll();
            }
            loadAll();

        })
    </script>
    <section class="content" ng-controller="circularSummery">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Category</label>
                            <select name="" ng-model="category" id="" class="form-control" ng-change="loadCircular(category)">
                                <option value="all">All</option>
                                <option ng-repeat="c in categories" value="[[c.id]]">[[c.category_name_eng]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="circular" id="" class="form-control">
                                <option value="all">All</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Status</label>
                            <select ng-model="status" name="" id="" class="form-control" ng-change="statusChange()">
                                <option ng-repeat="(key,value) in allStatus" value="[[key]]">[[value]]</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
