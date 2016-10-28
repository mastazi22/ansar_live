{{--User: Shreya--}}
{{--Date: 12/24/2015--}}
{{--Time: 12:52 PM--}}

@extends('template.master')
@section('title','Withdraw Kpi')
@section('breadcrumb')
    {!! Breadcrumbs::render('withdraw_kpi') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('KpiViewController', function ($scope, $http, $sce, httpService,$location,$timeout) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.rcDivision = parseInt('{{Auth::user()->division_id}}');
            $scope.total = 0;
            $scope.showLoadingScreen = true;
            $scope.numOfPage = 0;
            $scope.selectedDivision = "all";
            $scope.selectedDistrict = "all";
            $scope.selectedThana = "all";
            $scope.allLoading = false;
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
                $scope.allLoading = true;
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
                httpService.range().then(function (data) {
                    $scope.divisions = data;
                    $scope.loadingDivision = false;
                })
            }
            $scope.loadDistrict = function (d_id) {
                $scope.loadingDistrict = true;
                httpService.unit(d_id).then(function (data) {
                    $scope.districts = data;
                    $scope.selectedDistrict = "all";
                    $scope.selectedThana = "all";
                    $scope.loadingDistrict = false;
                    $scope.loadTotal()
                })
            }
            $scope.loadThana = function (d_id) {
                $scope.loadingThana = true;
                httpService.thana(d_id).then(function (data) {
                    $scope.thanas = data;
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
            $scope.$on('$routeChangeStart', function (event, current, previous) {
                //alert("start")
                if(current.$$route!=undefined) {
                    $("#withdraw-modal").modal('show');

                }
                else if($('#withdraw-modal').hasClass('in')){
                    $("#withdraw-modal").modal('hide');
                }
            })
            $scope.$on('$routeChangeSuccess', function (event, current, previous) {
//                alert('sdsddsd')
                $scope.showLoadingScreen = false
            })
            $scope.changeLocation = function () {
//                alert('ssdsadad')
                $location.path('/')
                $timeout(function () {
                    $scope.$apply();
                })
            }
            $scope.ppp = function () {
                $scope.showLoadingScreen = true;
            }
            $scope.$watch('showLoadingScreen', function (n,o) {
                //alert(n)
            })
        })
    </script>
    <div ng-controller="KpiViewController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4" ng-hide="isAdmin==66 || isAdmin==22">
                            <div class="form-group">
                                <label class="control-label">@lang('title.range')&nbsp;
                                    <img ng-show="loadingDivision" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDivision"
                                        ng-disabled="loadingDivision||loadingDistrict||loadingThana"
                                        ng-change="loadDistrict(selectedDivision)">
                                    <option value="all">All</option>
                                    <option ng-repeat="di in divisions" value="[[di.id]]">
                                        [[di.division_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4" ng-hide="isAdmin==22">
                            <div class="form-group">
                                <label class="control-label">@lang('title.unit')&nbsp;
                                    <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDistrict"
                                        ng-disabled="loadingDistrict||loadingThana"
                                        ng-change="loadThana(selectedDistrict)">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
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
                                        ng-change="loadTotal()" ng-disabled="loadingDistrict||loadingThana">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]
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
                                <th>KPI Address</th>
                                <th>KPI Contact No.</th>
                                <th>Action</th>
                            </tr>
                            <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                            <tbody>
                            <tr ng-if="kpis.length==0&&errorFound==0">
                                <td colspan="8" class="warning no-ansar">
                                    No KPI is available to show.
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
                                    <a href="#/withdraw/[[a.id]]" ng-click="ppp()" class="btn btn-info">Withdraw</a>
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
            <div class="modal modal-default fade" role="dialog" id="withdraw-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Withdraw Kpi</h4>

                        </div>
                        <div class="modal-body">
                            <div style="width: 100%;height: 200px;" ng-if="showLoadingScreen">
                                <div style="margin: auto;text-align:center;position: relative;top:50%;transform: translateY(-50%)">
                                    <i class="fa fa-spinner fa-pulse" style="vertical-align: middle;"></i>&nbsp;<span class="text text-bold">Please Wait...</span>
                                </div>
                            </div>
                            <ng-view  ng-if="!showLoadingScreen">
                                {{--ng route--}}
                            </ng-view>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#withdraw-modal").on('hide.bs.modal', function () {
//                alert('adadsadad');
                angular.element($(".content")).scope().changeLocation();
            })
        })
    </script>
@stop