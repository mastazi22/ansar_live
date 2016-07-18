{{--User: Shreya--}}
{{--Date: 12/24/2015--}}
{{--Time: 12:52 PM--}}

@extends('template.master')
@section('title','KPI Information')
@section('small_title')
    <a href="{{URL::route('go_to_kpi_page')}}" class="btn btn-sm btn-info">Add New Kpi</a>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('kpi_view') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('KpiViewController', function ($scope, $http, $sce, $compile) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.rcDivision = parseInt('{{Auth::user()->division_id}}');
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.selectedDivision = "all";
            $scope.selectedDistrict = "all";
            $scope.selectedThana = "all";
            $scope.isLoading = false;
            $scope.divisions = [];
            $scope.districts = [];
            $scope.thanas = [];
            $scope.guards = [];
            $scope.kpis = [];
            $scope.itemPerPage = 20;
            $scope.currentPage = 0;
            $scope.pages = [];
            $scope.loadingDivision = true;
            $scope.loadingDistrict = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.loadingPage = [];
            $scope.verified = [];
            $scope.verifying = [];
            $scope.loadPagination = function () {
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    })
                    $scope.loadingPage[i] = false;
                }
                if ($scope.numOfPage > 0)$scope.loadPage($scope.pages[0]);
                else $scope.loadPage({pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'});
            }
            $scope.loadPage = function (page, $event) {
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page.pageNum;
                $scope.loadingPage[page.pageNum] = true;
                $http({
                    url: '{{URL::route('kpi_view_details')}}',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        division: $scope.selectedDivision,
                        unit: $scope.selectedDistrict,
                        thana: $scope.selectedThana,
                        view: 'view'
                    }
                }).then(function (response) {
                    $scope.kpis = response.data.kpis;
                    console.log($scope.kpis)
//                    $compile($scope.ansars)
                    $scope.loadingPage[page.pageNum] = false;
                })
            }
            $scope.loadTotal = function () {
                $scope.isLoading = true;
                //alert($scope.selectedDivision)
                $http({

                    url: '{{URL::route('kpi_view_details')}}',
                    method: 'get',
                    params: {
                        division: $scope.selectedDivision,
                        unit: $scope.selectedDistrict,
                        thana: $scope.selectedThana,
                        view: 'count'
                    }
                }).then(function (response) {

                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                    $scope.isLoading = false;
                    //alert($scope.total)
                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            $scope.loadDivision = function () {
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DivisionName')}}'
                }).then(function (response) {
                    $scope.divisions = response.data;
                    $scope.loadingDivision = false;
                })
            }
            $scope.loadDistrict = function (di_id) {
                $scope.loadingDistrict = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}',
                    params: {id: di_id}
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.selectedDistrict = "all";
                    $scope.selectedThana = "all";
                    $scope.loadingDistrict = false;
                    $scope.loadTotal()
                })
            }
            $scope.loadThana = function (d_id) {
                $scope.loadingThana = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: d_id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "all";
                    $scope.loadingThana = false;
                    $scope.loadTotal()
                })
            }
            $scope.verify = function (id, i) {
                $scope.verifying[i] = true;
                $http({
                    url: "{{URL::to('HRM/kpi_verify')}}/" + id,
                    params: {verified_id: id},
                    method: 'get'
                }).then(function (response) {
                    //alert(JSON.stringify(response.data));
                    $scope.verifying[parseInt(i)] = false;
                    $scope.verified[parseInt(i)] = true;
//                    $scope.verified++;
                }, function () {
                    $scope.verifying[parseInt(i)] = false;
                    $scope.verified[parseInt(i)] = false;
                })
            }
            if ($scope.isAdmin == 11) {
                $scope.loadDivision()
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.loadThana($scope.dcDistrict);
                    console.log($scope.dcDistrict);
                }
                else if(!isNaN($scope.rcDivision))
                {
                    $scope.loadDistrict($scope.rcDivision);
                }
            }
            $scope.loadTotal();
        })
    </script>
    <div ng-controller="KpiViewController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <div class="loading-report animated" ng-class="{fadeInDown:isLoading,fadeOutUp:!isLoading}">
            <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
            <h4>Loading...</h4>
        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4" ng-hide="isAdmin==66 || isAdmin==22">
                            <div class="form-group">
                                <label class="control-label">Select a division&nbsp;
                                    <img ng-show="loadingDivision" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDivision"
                                        ng-disabled="loadingDivision||loadingDistrict||loadingThana"
                                        ng-change="loadDistrict(selectedDivision)">
                                    <option value="all">All</option>
                                    <option ng-repeat="di in divisions" value="[[di.id]]">
                                        [[di.division_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4" ng-hide="isAdmin==22">
                            <div class="form-group">
                                <label class="control-label">Select a unit&nbsp;
                                    <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDistrict"
                                        ng-disabled="loadingDistrict||loadingThana"
                                        ng-change="loadThana(selectedDistrict)">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Select a Thana&nbsp;
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16">
                                </label>
                                <select class="form-control" ng-model="selectedThana"
                                        ng-change="loadTotal()" ng-disabled="loadingDistrict||loadingThana">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>KPI Name</th>
                                <th>Division</th>
                                <th>Unit</th>
                                <th>Thana</th>
                                <th>KPI Address</th>
                                <th>KPI Contact No</th>
                                <th>Action</th>
                            </tr>
                            <tbody>
                            <tr ng-if="kpis.length==0">
                                <td colspan="8" class="warning no-ansar">
                                    No kpi is available to show
                                </td>
                            </tr>
                            <tr ng-if="kpis.length>0" ng-repeat="a in kpis">
                                <td>
                                    [[((currentPage)*itemPerPage)+$index+1]]
                                </td>
                                <td>
                                    [[a.kpi_bng]]
                                </td>
                                <td>
                                    [[a.division_eng]]
                                </td>
                                <td>
                                    [[a.unit]]
                                </td>
                                <td>
                                    [[a.thana]]
                                </td>
                                <td>
                                    [[a.address]]
                                </td>
                                <td>
                                    [[a.contact]]
                                </td>
                                <td>
                                    <div class="col-xs-1">
                                        <a href="{{URL::to('HRM/kpi-edit/'.'[[a.id]]')}}"
                                           class="btn btn-primary btn-xs" title="Edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                    </div>

                                    <div class="col-xs-1"
                                         style="@if(!auth()->user()->hasKpiVerifyPermission()) display: none; @endif">
                                        {{--@if(([[a.status_of_kpi]])==0)--}}
                                        <div ng-if="a.status_of_kpi==0">
                                            <a class="btn btn-success btn-xs verification" title="verify"
                                               ng-click="verify(a.id, $index)"
                                               ng-disabled="verified[$index]"><span
                                                        class="fa fa-check"
                                                        ng-hide="verifying[$index]"></span>
                                                <i class="fa fa-spinner fa-pulse"
                                                   ng-show="verifying[$index]"></i>
                                            </a>
                                        </div>
                                        {{--@else--}}
                                        <div ng-if="a.status_of_kpi==1">
                                            <a class="btn btn-success btn-xs verification" title="verify"
                                               ng-click="verify(a.id, $index)"
                                               ng-disabled="!verified[$index]"><span
                                                        class="fa fa-check"
                                                        ng-hide="verifying[$index]"></span>
                                                <i class="fa fa-spinner fa-pulse"
                                                   ng-show="verifying[$index]"></i>
                                            </a>
                                        </div>
                                        {{--@endif--}}
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="table_pagination" ng-if="pages.length>1">
                            <ul class="pagination">
                                <li ng-class="{disabled:currentPage == 0}">
                                    <a href="#" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                </li>
                                <li ng-class="{disabled:currentPage == 0}">
                                    <a href="#" ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                </li>
                                <li ng-repeat="page in pages|filter:filterMiddlePage"
                                    ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                    <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                    <a href="#" ng-click="loadPage(page,$event)"
                                       ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                            <span ng-show="loadingPage[page.pageNum]" style="position: relative"><i
                                                        class="fa fa-spinner fa-pulse"
                                                        style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#"
                                       ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop