{{--User: Shreya--}}
{{--Date: 2/29/2016--}}
{{--Time: 12:28 PM--}}

@extends('template.master')
@section('title','Total number of Ansars who are not interested to join after 10 or more reminders')
{{--@section('small_title','Total number of Ansars who are not interested to join after more than 10 reminders')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('dashboard_menu_not_interested',$total) !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarNotInterestedListController', function ($scope, $http, $sce) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.selectedDistrict = "all";
            $scope.queue = [];
            $scope.selectedThana = "all"
            $scope.districts = [];
            $scope.thanas = [];
            $scope.itemPerPage = 20
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingDistrict = true;
            $scope.loadingThana = false;
            $scope.loadingPage = [];
            $scope.allLoading = true;
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
                $scope.allLoading = true;
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('not_interested_info_details')}}',
                    method: 'get',
                    params: {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit,
                        division: $scope.param.range,
                        thana: $scope.param.thana,
                        q: $scope.q
                    }
                }).then(function (response) {
                    $scope.ansars = response.data;
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total

                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            }
            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }

            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
        })
    </script>
    <div ng-controller="AnsarNotInterestedListController">
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
                            on-load="loadPage()"
                            data="param"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                    >

                    </filter-template>
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text text-bold">Total Ansars
                                :PC([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])&nbsp;APC([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])&nbsp;Ansar([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</h4>
                        </div>
                        <div class="col-md-4">
                            <database-search q="q" queue="queue" on-change="loadPage()"></database-search>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>Ansar id</th>
                                <th>Name</th>
                                <th>Rank</th>
                                <th>Unit</th>
                                <th>Thana</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                            </tr>
                            <tr ng-repeat="a in ansars.ansars">
                                <td>[[ansars.index+$index]]</td>
                                <td>[[a.id]]</td>
                                <td>[[a.name]]</td>
                                <td>[[a.rank]]</td>
                                <td>[[a.unit]]</td>
                                <td>[[a.thana]]</td>
                                <td>[[a.birth_date|dateformat:'DD-MMM-YYYY']]</td>
                                <td>[[a.sex]]</td>
                            </tr>
                            <tr ng-if="ansars.ansars.length<=0||ansars.ansars==undefined">
                                <td class="warning" colspan="8">No Ansar Found</td>
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