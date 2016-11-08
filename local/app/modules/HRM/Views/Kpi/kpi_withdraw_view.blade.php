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
            $scope.params = ''
            $scope.total = 0;
            $scope.showLoadingScreen = true;
            $scope.numOfPage = 0;
            $scope.allLoading = false;
            $scope.guards = [];
            $scope.kpis = [];
            $scope.itemPerPage = parseInt('{{config('app.item_per_page')}}');
            $scope.currentPage = 0;
            $scope.pages = [];
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
            }
            $scope.loadPage = function (page, $event) {
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page==undefined?0:page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $http({
                    url: '{{URL::route('kpi_view_details')}}',
                    method: 'get',
                    params: {
                        offset: page==undefined?0:page.offset,
                        limit: page==undefined?$scope.itemPerPage:page.limit,
                        division: $scope.params.range,
                        unit: $scope.params.unit,
                        thana: $scope.params.thana,
                        view: 'view'
                    }
                }).then(function (response) {
                    $scope.kpis = response.data.kpis;
                    console.log($scope.kpis)
//                    $compile($scope.ansars)
                    $scope.loadingPage[$scope.currentPage ] = false;
                    $scope.errorFound = 0;
                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
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
                    <filter-template
                            show-item="['range','unit','thana']"
                            type="all"
                            range-change="loadPage()"
                            unit-change="loadPage()"
                            thana-change="loadPage()"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                            data = "params"
                            on-load="loadPage()"
                    ></filter-template>
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