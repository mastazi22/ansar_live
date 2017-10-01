{{--User: Shreya--}}
{{--Date: 12/24/2015--}}
{{--Time: 5:43 PM--}}
@extends('template.master')
@section('title','Total number of Ansars who will reach 50 years of age within the next 3 months')
{{--@section('small_title','Total number of Ansars who will reach 50 years of age within next 3 months')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('dashboard_menu_50_year',$total) !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarFiftyYearsReachedListController', function ($scope, $http,$sce,$compile) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.param = {}
            $scope.queue = [];
            $scope.itemPerPage = 20
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingPage = [];
            $scope.allLoading = true;
            $scope.errorFound=0;
            $scope.loadPagination = function(){
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    })
                    $scope.loadingPage[i]=false;
                }
            }
            $scope.loadPage = function (page,$event) {
                if($event!=undefined)  $event.preventDefault();
                $scope.exportPage = page;
                $scope.currentPage = page==undefined?0:page.pageNum;
                $scope.loadingPage[$scope.currentPage]=true;
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('ansar_reached_fifty_details')}}',
                    method: 'get',
                    params: {
                        offset: page==undefined?0:page.offset,
                        limit: page==undefined?$scope.itemPerPage:page.limit,
                        unit:$scope.param.unit,
                        thana:$scope.param.thana,
                        division:$scope.param.range,
                        q:$scope.q
                    }
                }).then(function (response) {
                    $scope.ansars = response.data;
                    $scope.queue.shift()
                    $scope.loadingPage[$scope.currentPage]=false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total
                    if($scope.queue.length>1) $scope.loadPage()
                    $scope.numOfPage = Math.ceil($scope.total/$scope.itemPerPage);
                    $scope.loadPagination();
                })
            }
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if(type=='page')$scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '{{URL::route('ansar_reached_fifty_details')}}',
                    method: 'get',
                    params: {
                        offset: type=='all'?-1:(page == undefined ? 0 : page.offset),
                        limit: type=='all'?-1:(page == undefined ? $scope.itemPerPage : page.limit),
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        thana: $scope.param.thana == undefined ? 'all' : $scope.param.thana,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        q: $scope.q,
                        export:type
                    }
                }).then(function (res) {
                    $scope.export_page =  $scope.export_all = false;
                },function (res) {
                    $scope.export_page =  $scope.export_all = false;
                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage-3<0?0:($scope.currentPage>array.length-4?array.length-8:$scope.currentPage-3);
                var maxPage = minPage+7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            function sum(t){
                var s = 0;
                for(var i in t){
                    s += parseInt(t[i])
                }
                return s;
            }
        })
    </script>
    <div ng-controller="AnsarFiftyYearsReachedListController">
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
                            start-load="range"
                            data="param"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                    >

                    </filter-template>
                    <div class="row">
                        {{--<div class="col-sm-12" style="margin-bottom: 5px">
                            <div class="btn-group btn-group-sm pull-right">
                                --}}{{--<button id="print-report" class="btn btn-default"><i--}}{{--
                                --}}{{--class="fa fa-print"></i>&nbsp;Print--}}{{--
                                --}}{{--</button>--}}{{--
                                <button id="export-report" ng-disabled="export_page||export_all" ng-click="exportData('page')" class="btn btn-default ">
                                    <i ng-show="!export_page" class="fa fa-file-excel-o"></i><i ng-show="export_page" class="fa fa-spinner fa-pulse"></i>&nbsp;Export this page
                                </button>
                                <button  ng-disabled="export_page||export_all" ng-click="exportData('all')" id="export-report-all" class="btn btn-default">
                                    <i ng-show="!export_all" class="fa fa-file-excel-o"></i><i ng-show="export_all" class="fa fa-spinner fa-pulse"></i>&nbsp;Export all
                                </button>
                            </div>
                        </div>--}}
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text text-bold">Total Ansars :PC([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])&nbsp;APC([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])&nbsp;Ansar([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</h4>
                        </div>
                        <div class="col-md-4">
                            <database-search q="q" queue="queue" on-change="loadPage()"></database-search>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <template-list data="ansars" key="selected_ansar_fifty_age"></template-list>
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
                                    <a href="#" ng-click="loadPage(page,$event)" ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                    <span ng-show="loadingPage[page.pageNum]"  style="position: relative"><i class="fa fa-spinner fa-pulse" style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#" ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop