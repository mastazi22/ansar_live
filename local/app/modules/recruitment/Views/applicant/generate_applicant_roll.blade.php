@extends('template.master')
@section('title','Generate Applicant Roll')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.search') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce,notificationService,$rootScope) {

            $scope.param={};
            $scope.circular = '';
            var loadAll = function () {
                $scope.circular = '';
                $scope.allLoading = true;
                $q.all([
                    httpService.circular({status: 'running'}),
                ])
                    .then(function (response) {
                        $scope.circular = '';
                        $scope.circulars = response[0].data;
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.circular = '';
                        $scope.allLoading = false;
                    })
            }
            $scope.loadApplicant = function () {
                //alert($scope.limitList)
                $scope.allLoading = true;
                $http({
                    method:'post',
                    data:$scope.param,
                    url:'{{URL::route('recruitment.applicant.generate_roll_no')}}'
                }).then(function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('success',"Roll No generated Successfully")
                },function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('error',"An error occur while generating roll no. Please try again later")
                })
            }
            $scope.applicantsDetail = [];

            loadAll();



        })
    </script>
    <section class="content" ng-controller="applicantSearch">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="" class="control-label">Job Circular</label>
                                <select name="" ng-model="param.job_circular_id" id=""
                                        class="form-control">
                                    <option value="">--select a circular--</option>
                                    <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="" class="control-label">Select Satus</label>
                                <select name="" ng-model="param.status" id="" multiple
                                        class="form-control">
                                    <option value="applied">applied</option>
                                    <option value="selected">selected</option>
                                    <option value="accepted">accepted</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <button class="btn btn-primary" ng-click="loadApplicant()">Generate Roll</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
