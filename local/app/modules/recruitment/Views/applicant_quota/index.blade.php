@extends('template.master')
@section('title','Applicant Quota')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.quota.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantQuota', function ($scope, $http, $q, httpService,notificationService) {
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
                    httpService.range(),
                    httpService.unit(),
                    httpService.applicantQuota({division: $scope.division, district: $scope.district}),
                ]).then(function (response) {
                    $scope.editing = [];
                    $scope.divisions = response[0];
                    $scope.districts = response[1];
                    console.log(response)
                    $scope.applicantQuota = response[2].data;
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.loadDistrict = function (id) {
                $scope.allLoading = true;
                $scope.district = 'all';
                $q.all([
                    httpService.unit(id),
                    httpService.applicantQuota({division: $scope.division, district: $scope.district}),
                ]).then(function (response) {
                    $scope.editing = [];
                    $scope.districts = response[0];
                    $scope.applicantQuota = response[1].data;
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.submitData = function (index,male,female) {
                $scope.allLoading = true;
                $http({
                    method:'post',
                    data:{district:$scope.applicantQuota[index].id,male:male,female:female},
                    url:'{{URL::route('recruitment.quota.update')}}'
                }).then(function (response) {
                    $scope.allLoading = false;
                    console.log(response.data)
                    if(response.data.status){
                        notificationService.notify('success',response.data.message)
                        if(!$scope.applicantQuota[index].applicant_quota){
                            $scope.applicantQuota[index].applicant_quota = {};
                        }
                        $scope.applicantQuota[index].applicant_quota['male'] = male;
                        $scope.applicantQuota[index].applicant_quota['female'] = female;
                        $scope.editing[index] = false;
                    }
                    else{
                        notificationService.notify('error',response.data.message)
                    }
                },function (response) {
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
                            <label for="" class="control-label">Select Division</label>
                            <select name="" ng-model="division" id="" class="form-control" ng-change="loadDistrict(division)">
                                <option value="all">All</option>
                                <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Select District</label>
                            <select name="" ng-model="district" id="" class="form-control">
                                <option value="all">All</option>
                                <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]</option>
                                {{--<option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>--}}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl. No</th>
                            <th>District Name</th>
                            <th>Male Quota</th>
                            <th>Female Quota</th>
                            <th>Action</th>
                        </tr>
                        <tr ng-repeat="a in applicantQuota">
                            <td>[[$index+1]]</td>
                            <td>[[a.unit_name_bng]]</td>
                            <td ng-if="!editing[$index]">[[a.applicant_quota?a.applicant_quota.male:0]]</td>
                            <td ng-if="editing[$index]">
                                <input type="text" placeholder="male" ng-model="male[$index]">
                            </td>
                            <td ng-if="!editing[$index]">[[a.applicant_quota?a.applicant_quota.female:0]]</td>
                            <td ng-if="editing[$index]">
                                <input type="text" placeholder="female" ng-model="female[$index]">
                            </td>
                            <td ng-if="!editing[$index]">
                                <a href="#" onclick="return false" class="btn btn-primary btn-xs" ng-click="editing[$index]=true">
                                    <i class="fa fa-edit"></i>&nbsp; Edit
                                </a>
                            </td>
                            <td ng-if="editing[$index]">
                                <a href="#" onclick="return false" ng-click="submitData($index,male[$index],female[$index])" class="btn btn-primary btn-xs">
                                    <i class="fa fa-save"></i>&nbsp; Save
                                </a>
                                <a href="#" onclick="return false" class="btn btn-danger btn-xs" ng-click="editing[$index]=false">
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