{{--User: Shreya--}}
{{--Date: 12/24/2015--}}
{{--Time: 12:52 PM--}}

@extends('template.master')
@section('title','KPI Withdrawal Date Update')
@section('breadcrumb')
    {!! Breadcrumbs::render('withdrawn_kpi_list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('KpiWithdrawDateController', function ($scope, $http, $sce, $compile) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}')
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.selectedDistrict = "all";
            $scope.loadingDiv = false;
            $scope.selectedThana = "all";
            $scope.selectedDivision = "all";
            $scope.allLoading = false;
            $scope.loadingDiv = false;
            $scope.districts = [];
            $scope.thanas = [];
            $scope.guards = [];
            $scope.kpis = [];
            $scope.itemPerPage = 10;
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingDistrict = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.loadingPage = [];
            $scope.errorMessage = '';
            $scope.errorFound = 0;
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
                    url: '{{URL::route('withdrawn_kpi_list')}}',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        unit: $scope.selectedDistrict,
                        thana: $scope.selectedThana,
                        division: $scope.selectedDivision,
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
                $scope.allLoading = true;
                //alert($scope.selectedDivision)
                $http({

                    url: '{{URL::route('withdrawn_kpi_list')}}',
                    method: 'get',
                    params: {
                        unit: $scope.selectedDistrict,
                        thana: $scope.selectedThana,
                        division: $scope.selectedDivision,
                        view: 'count'
                    }
                }).then(function (response) {
                    $scope.errorFound = 0;
                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                    $scope.allLoading = false;
                    //alert($scope.total)
                },function(response){
                    $scope.errorFound = 1;
                    $scope.total = 0;
                    $scope.kpis = [];
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    $scope.pages = [];
                    $scope.allLoading = false;
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
                $scope.loadingDistrict = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}',
                    params:{id:$scope.selectedDivision}
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.loadingDistrict = false;
                    $scope.thanas = [];
                    $scope.selectedThana = "all";
                    $scope.selectedDistrict = "all";
                    $scope.loadTotal();
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
            $scope.dateConvert = function (date) {
                return (moment(date).format('DD-MMM-Y'));
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
            $scope.loadTotal();
        })
    </script>
    <div ng-controller="KpiWithdrawDateController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-exclamation-sign"></span>{{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4" ng-show="isAdmin==11||isAdmin==33">
                            <div class="form-group">
                                <label class="control-label">
                                    @lang('title.range')&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingDiv">
                                </label>
                                <select class="form-control" ng-disabled="loadingDiv||loadingDistrict||loadingThana"
                                        ng-model="selectedDivision"
                                        ng-change="loadDistrict()" name="division_id">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4" ng-show="isAdmin==11||isAdmin==33||isAdmin==66">
                            <div class="form-group">
                                <label class="control-label">@lang('title.unit')&nbsp;
                                    <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDistrict"
                                        ng-disabled="loadingDiv||loadingDistrict||loadingThana"
                                        ng-change="loadThana(selectedDistrict)">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">@lang('title.thana')&nbsp;
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16">
                                </label>
                                <select class="form-control" ng-model="selectedThana"
                                        ng-change="loadTotal()" ng-disabled="loadingDiv||loadingDistrict||loadingThana">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <h4>Total KPI: [[total.toLocaleString()]]</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>KPI Name</th>
                                <th>Division</th>
                                <th>Unit</th>
                                <th>Thana</th>
                                <th>Withdrawn Date</th>
                                <th>Action</th>
                            </tr>
                            <tbody>
                            <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                            <tr ng-if="kpis.length==0&&errorFound==0">
                                <td colspan="8" class="warning no-ansar">
                                    No KPI is available to show
                                </td>
                            </tr>
                            <tr ng-if="kpis.length>0" ng-repeat="a in kpis">
                                <td>
                                    [[$index+1]]
                                </td>
                                {{--<td>--}}
                                {{--<a href="{{URL::to('/entryreport')}}/[[a.ansar_id]]">[[a.ansar_id]]</a>--}}
                                {{--</td>--}}
                                <td>
                                    [[a.kpi]]
                                </td>
                                <td>
                                    [[a.division]]
                                </td>
                                <td>
                                    [[a.unit]]
                                </td>
                                <td>
                                    [[a.thana]]
                                </td>
                                <td>
                                    [[dateConvert(a.date)]]
                                </td>
                                <td>
                                    <div class="col-xs-1">
                                        <a href="{{URL::to('HRM/withdraw-date-edit/'.'[[a.id]]')}}"
                                           class="btn btn-primary btn-xs" title="Edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
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