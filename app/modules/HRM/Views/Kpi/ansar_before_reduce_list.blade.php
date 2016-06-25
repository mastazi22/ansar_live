{{--User: Shreya--}}
{{--Date: 11/16/2015--}}
{{--Time: 2:42 PM--}}

@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller('GuardBeforeWithdrawController', function ($scope, $http) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.districts = [];
            $scope.thanas = [];
            $scope.selectedDistrict = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.isLoading = false;
            $scope.loadingUnit = false;
            $scope.ansars="";
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}')
            $scope.loadDistrict = function () {
                $scope.loadingUnit = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.loadingUnit = false;
                    $scope.thanas = [];
                    $scope.selectedThana = "";
                })
            }
            $scope.loadThana = function (id) {
                $scope.loadingThana = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "";
                    $scope.loadingThana = false;
                })
            }
            $scope.loadGuard = function (id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.guards = response.data;
                    $scope.selectedKpi = "";
                    $scope.loadingKpi = false;
                })
            }
            $scope.loadAnsar = function (id) {
                $scope.isLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('load_ansar_before_reduce')}}',
                    params: {kpi_id: id}
                }).then(function (response) {
                    $scope.ansars = response.data;
                    console.log($scope.ansars)
                    $scope.isLoading = false;
                })
            }
            if ($scope.isAdmin == 11) {
                $scope.loadDistrict()
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.loadThana($scope.dcDistrict)
                }
            }
            $scope.dateConvert=function(date){
                return (moment(date).format('DD-MMM-Y'));
            }
        })

    </script>
    <div ng-controller="GuardBeforeWithdrawController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('ansar_before_reduce_list') !!}--}}
        {{--</div>--}}
        <div class="loading-report animated" ng-class="{fadeInDown:isLoading,fadeOutUp:!isLoading}">
            <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
            <h4>Loading...</h4>
        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="nav-tabs-custom" style="background-color: transparent">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#">List of Ansar before Reduction</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-4" ng-show="isAdmin==11">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Select a District&nbsp;&nbsp;
                                            <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                 ng-show="loadingUnit">
                                        </label>
                                        <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                ng-model="selectedDistrict"
                                                ng-change="loadThana(selectedDistrict)">
                                            <option value="">--Select a District--</option>
                                            <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Select a Thana&nbsp;&nbsp;
                                            <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                 ng-show="loadingThana">
                                        </label>
                                        <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                ng-model="selectedThana"
                                                ng-change="loadGuard(selectedThana)">
                                            <option value="">--Select a Thana--</option>
                                            <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Select a Guard&nbsp;&nbsp;
                                            <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                 ng-show="loadingKpi">
                                        </label>
                                        <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                ng-model="selectedKPI"
                                                ng-change="loadAnsar(selectedKPI)">
                                            <option value="">--Select a Guard--</option>
                                            <option ng-repeat="d in guards" value="[[d.id]]">[[d.kpi_name]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Ansar ID</th>
                                        <th>Ansar Name</th>
                                        <th>Rank</th>
                                        <th>Own District</th>
                                        <th>Own Thana</th>
                                        <th>Ansar Reporting Date</th>
                                        <th>Ansar Joining Date</th>
                                        <th>Reduce Reason</th>
                                        <th>Reduce Date</th>
                                    </tr>
                                    <tr ng-show="ansars.length==0">
                                        <td colspan="10" class="warning no-ansar">
                                            No available available to see
                                        </td>
                                    </tr>
                                    <tr ng-show="ansars.length>0" ng-repeat="a in ansars">
                                        <td>
                                            [[$index+1]]
                                        </td>
                                        <td>
                                            [[a.id]]
                                        </td>
                                        <td>
                                            [[a.name]]
                                        </td>
                                        <td>
                                            [[a.rank]]
                                        </td>
                                        <td>
                                            [[a.unit]]
                                        </td>
                                        <td>
                                            [[a.thana]]
                                        </td>
                                        <td>
                                            [[dateConvert(a.r_date)]]
                                        </td>
                                        <td>
                                            [[dateConvert(a.j_date)]]
                                        </td>
                                        <td>
                                            [[a.reason]]
                                        </td>
                                        <td>
                                            [[dateConvert(a.date)]]
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@stop