@extends('template.master')
@section('title','Download Applicant Form')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.reports.view_applicant_status_report') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ApplicantsFormDownloadController', function ($scope, $http, $q, httpService) {
            $scope.category = '';
            $scope.applicantId = '';
            $scope.circular = '';
            $scope.allLoading = false;
            $scope.jobCategories = [];
            $scope.jobCirculars = [];
            $scope.downloadable = "";
            $scope.dFileName = "";
            $scope.loadAllCategories = function () {
                $scope.allLoading = true;
                $q.all([
                    httpService.category()
                ]).then(function (response) {
                    $scope.allLoading = false;
                    $scope.circular = 'all';
                    $scope.jobCategories = response[0].data;
                }, function (response) {
                    $scope.allLoading = false;
                    $scope.jobCategories = [];
                })
            };
            $scope.loadCircular = function () {
                $scope.allLoading = true;
                $q.all([
                    httpService.circular({category_id: $scope.category})
                ]).then(function (response) {
                    $scope.allLoading = false;
                    $scope.jobCirculars = response[0].data;
                }, function (response) {
                    $scope.allLoading = false;
                    $scope.circulars = [];
                })
            };
            $scope.generateApplicantForm = function () {
                $http({
                    url: '/' + prefix + 'recruitment/reports/applicant_form_download',
                    data: {
                        applicant_id: $scope.applicantId,
                        job_circular_id: $scope.circular,
                        status: $scope.status
                    },
                    method: 'post'
                }).then(function (response) {
                    if(response.data.file){
                        $scope.allLoading = false;
                        $scope.downloadable = response.data.download;
                        $scope.dFileName = response.data.file;
                    }
                }, function (response) {
                    $scope.allLoading = false;
                    alert("Error Occurred.");
                })
            };
        })
    </script>
    <section class="content" ng-controller="ApplicantsFormDownloadController" ng-init="loadAllCategories()">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div class="row" style="margin-bottom: 5px">
                    <div class="col-sm-12">
                        <h3>Single Applicant</h3>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Applicant Id</label>
                            <input class="form-control" type="text" ng-model="applicantId">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <button style="margin-top:7%;" class="btn btn-primary" ng-click="generateApplicantForm()">
                            Generate
                        </button>
                    </div>
                    <hr width="100%"/>
                </div>
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-sm-12">
                        <h3>All Applicants</h3>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="" class="control-label">Job Category</label>
                            <select ng-model="category" class="form-control" ng-change="loadCircular()">
                                <option ng-repeat="c in jobCategories" value="[[c.id]]">[[c.category_name_eng]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select ng-model="circular" class="form-control">
                                <option ng-repeat="c in jobCirculars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label for="" class="control-label">Applicant Status</label>
                        <select ng-model="status" class="form-control">
                            <option value="applied">Applied</option>
                            <option value="accepted">Accepted</option>
                            <option value="selected">Selected</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label for="" class="control-label" style="display:block">&nbsp;</label>
                        <button class="btn btn-primary" ng-click="generateApplicantForm()">Generate All</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" ng-if="downloadable!=''">
                        <p>Download&nbsp;<a href="[[downloadable]]" download>[[dFileName]]</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
