@extends('template.master')
@section('title','Applicant Quota')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.quota.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantQuota', function ($scope, $http, $q, httpService, notificationService) {
            $scope.applicantQuota = [];
            $scope.division = 'all';
            $scope.district = 'all';
            $scope.divisions = [];
            $scope.districts = [];
            $scope.editing = [];
            $scope.male = [];
            $scope.female = [];
            var loadAll = function () {
                $scope.allLoading = true;
                $q.all([
                    httpService.circular({circular_status: 'running'}),
                ]).then(function (response) {
                    $scope.editing = [];
                    $scope.circulars = response[0].data;
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.loadQuota = function () {
                $scope.allLoading = true;
                httpService.applicantQuota({job_circular_id: $scope.circular, type: $scope.type})
                    .then(function (result) {
                        $scope.applicantQuota = result.data.quota;
                        $scope.cq = result.data.cq;
                        $scope.allLoading = false;
                    }, function (error) {
                        $scope.allLoading = false;
                    })
            }
            $scope.submitData = function (index, male, female) {
                $scope.allLoading = true;
                var data = {};
                if($scope.type=="range"){
                    data = {type:$scope.type,range_id: $scope.applicantQuota[index].id, male: male, female: female,job_circular_quota_id:$scope.cq}
                } else{
                    data = {type:$scope.type,district: $scope.applicantQuota[index].id, male: male, female: female,job_circular_quota_id:$scope.cq}
                }
                $http({
                    method: 'post',
                    data: data,
                    url: '{{URL::route('recruitment.quota.update')}}'
                }).then(function (response) {
                    $scope.allLoading = false;
                    console.log(response.data)
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message)
                        if (!$scope.applicantQuota[index].applicant_quota) {
                            $scope.applicantQuota[index].applicant_quota = {};
                        }
                        $scope.applicantQuota[index].applicant_quota['male'] = male;
                        $scope.applicantQuota[index].applicant_quota['female'] = female;
                        $scope.editing[index] = false;
                    }
                    else {
                        notificationService.notify('error', response.data.message)
                    }
                }, function (response) {
                    $scope.allLoading = false;
                })
            }
            loadAll();
        })
    </script>
    <section class="content" ng-controller="applicantQuota">
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
                            <label for="" class="control-label">Select Circular</label>
                            <select name="" ng-model="circular" id="" class="form-control">
                                <option value="">--Select a circular--</option>
                                <option ng-repeat="d in circulars" value="[[d.id]]">[[d.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Select Type</label>
                            <select name="" ng-model="type" id="" class="form-control">
                                <option value="">--Select a type--</option>
                                <option value="range">Range Wise</option>
                                <option value="unit">Unit Wise</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label" style="display: block">&nbsp;</label>
                            <button class="btn btn-primary" ng-click="loadQuota()">
                                Load Quota
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl. No</th>
                            <th>District/Range Name</th>
                            <th>Male Quota</th>
                            <th>Female Quota</th>
                            <th>Action</th>
                        </tr>
                        <tr ng-repeat="a in applicantQuota">
                            <td>[[$index+1]]</td>
                            <td>[[type=="unit"?a.unit_name_bng:a.division_name_bng]]</td>
                            <td ng-if="!editing[$index]">[[a.applicant_quota?a.applicant_quota.male:0]]</td>
                            <td ng-if="editing[$index]">
                                <input type="text" placeholder="male" ng-model="male[$index]">
                            </td>
                            <td ng-if="!editing[$index]">[[a.applicant_quota?a.applicant_quota.female:0]]</td>
                            <td ng-if="editing[$index]">
                                <input type="text" placeholder="female" ng-model="female[$index]">
                            </td>
                            <td ng-if="!editing[$index]">
                                <a href="#" onclick="return false" class="btn btn-primary btn-xs"
                                   ng-click="editing[$index]=true">
                                    <i class="fa fa-edit"></i>&nbsp; Edit
                                </a>
                            </td>
                            <td ng-if="editing[$index]">
                                <a href="#" onclick="return false"
                                   ng-click="submitData($index,male[$index],female[$index])"
                                   class="btn btn-primary btn-xs">
                                    <i class="fa fa-save"></i>&nbsp; Save
                                </a>
                                <a href="#" onclick="return false" class="btn btn-danger btn-xs"
                                   ng-click="editing[$index]=false">
                                    <i class="fa fa-times"></i>&nbsp; close
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="applicantQuota.length<=0">
                            <td class="bg-warning" colspan="5">No data available</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection