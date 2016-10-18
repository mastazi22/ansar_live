{{--User: Shreya--}}
{{--Date: 11/16/2015--}}
{{--Time: 2:42 PM--}}

@extends('template.master')
@section('title','Ansar List Before Reduction')
@section('breadcrumb')
    {!! Breadcrumbs::render('ansar_before_reduce_list') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('GuardBeforeWithdrawController', function ($scope, $http, $sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.districts = [];
            $scope.thanas = [];
            $scope.selectedDivision = 'all'
            $scope.selectedDistrict = "all";
            $scope.selectedThana = "all";
            $scope.selectedKpi = "all";
            $scope.allLoading = false;
            $scope.loadingUnit = false;
            $scope.loadingDiv = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.ansars="";
            $scope.errorFound = 0;
            $scope.errorMessage='';
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}')
            $scope.loadDivision = function () {
                $scope.loadingDiv = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DivisionName')}}'
                }).then(function (response) {
                    $scope.loadingDiv = false;
                    $scope.divisions = response.data;
                    $scope.loadingDiv = false;
                })
            }
            $scope.loadDistrict = function () {
                $scope.loadingUnit = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.loadingUnit = false;
                    $scope.thanas = [];
                    $scope.guards = [];
                    $scope.selectedThana = "all";
                    $scope.selectedKpi = "all";
                    $scope.selectedDistrict = "all";
                    $scope.loadAnsar();
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
                    $scope.selectedThana = "all";
                    $scope.loadingThana = false;
                    $scope.guards = [];
                    $scope.selectedKpi = "all";
                    $scope.loadAnsar();
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
                    $scope.selectedKpi = "all";
                    $scope.loadingKpi = false;
                    $scope.loadAnsar();
                })
            }
            $scope.loadAnsar = function () {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('load_ansar_before_reduce')}}',
                    params: {
                        kpi_id: $scope.selectedKpi,
                        division_id:$scope.selectedDivision,
                        unit_id:$scope.selectedDistrict,
                        thana_id:$scope.selectedThana
                    }
                }).then(function (response) {
                    $scope.errorFound = 0;
                    $scope.ansars = response.data;
                    $scope.allLoading = false;
                },function(response){
                    $scope.errorFound = 1;
                    $scope.ansars = [];
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    $scope.allLoading = false;
                })
            }
            if ($scope.isAdmin == 11||$scope.isAdmin == 33) {
                $scope.loadDivision()
            }
            else if ($scope.isAdmin == 66) {
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
            $scope.loadAnsar()
        })

    </script>
    <div ng-controller="GuardBeforeWithdrawController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('ansar_before_reduce_list') !!}--}}
        {{--</div>--}}
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3" ng-show="isAdmin==11||isAdmin==33">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a Range&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingDiv">
                                </label>
                                <select class="form-control" ng-disabled="loadingDiv||loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selectedDivision"
                                        ng-change="loadDistrict()" name="division_id">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3" ng-show="isAdmin==11||isAdmin==33||isAdmin==66">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a District&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingUnit">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selectedDistrict"
                                        ng-change="loadThana(selectedDistrict)">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a Thana&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingThana">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selectedThana"
                                        ng-change="loadGuard(selectedThana)">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a Guard&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingKpi">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selectedKpi"
                                        ng-change="loadAnsar()">
                                    <option value="all">All</option>
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
                            <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                            <tr ng-show="ansars.length==0&&errorFound==0">
                                <td colspan="10" class="warning no-ansar">
                                    No Ansar is available to show
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
        </section>
    </div>

@stop