{{--User: Shreya--}}
{{--Date: 12/28/2015--}}
{{--Time: 10:23 AM--}}

@extends('template.master')
@section('title','Blocklisted Ansar Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('blocklist_view') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('BlockListReportController', function ($scope, $http,$sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $scope.total = 0;
            $scope.reportType = 'eng'
            $scope.numOfPage = 0;
            $scope.selectedDistrict = "all";
            $scope.selectedThana = "all";
            $scope.districts = [];
            $scope.thanas = [];
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.allLoading = false;
            $scope.loadingDistrict = true;
            $scope.loadingThana = false;
            $scope.loadingPage = [];
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
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
                $scope.allLoading = true;
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page==undefined?0:page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $http({
                    url: '{{URL::route('blocklisted_ansar_info')}}',
                    method: 'get',
                    params: {
                        offset: page==undefined?0:page.offset,
                        limit: page==undefined?$scope.itemPerPage:page.limit,
                        unit:$scope.param.unit,
                        thana:$scope.param.thana,
                        division:$scope.param.range
                    }
                }).then(function (response) {
                    $scope.ansars = response.data;
                    // $scope.queue.shift();
                    $scope.allLoading = false;
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    //if($scope.queue.length>1) $scope.loadPage();
                    $scope.loadPagination();
                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                if ($scope.currentPage < 6 && value.pageNum > 1 && value.pageNum < 8&&array.length>2) {
                    return true;
                }
                if ($scope.currentPage > array.length - 9 && value.pageNum > array.length - 9 && value.pageNum < array.length - 3&&array.length>2) {
                    return true;
                }
                if ($scope.currentPage > 5 && (value.pageNum >= $scope.currentPage - 3 && value.pageNum <= $scope.currentPage + 3&&array.length>2)) {
                    if ($scope.currentPage <= array.length - 6)
                        return true;
                }
            }
            $scope.filterFirstPage = function (value, index, array) {
                if (value.pageNum < 2) {
                    return true;
                }
            }
            $scope.filterLastPage = function (value, index, array) {
                if (value.pageNum > array.length - 3 && array.length>8) {
                    return true;
                }
            }
            $scope.loadReportData = function (reportName,type) {
                $scope.allLoading = true;
                $http({
                    method:'get',
                    url:'{{URL::route('localize_report')}}',
                    params:{name:reportName,type:type}
                }).then(function(response){
                    $scope.report = response.data;
                    $scope.allLoading = false;
                })
            }
            $scope.loadReportData("blocklisted_ansar_report","eng")
        })
        $(function () {
            function beforePrint(){
                $("#print-area").remove();
//                console.log($("body").find("#print-body").html())
                $('body').append('<div id="print-area">'+$("#print-blocklisted-ansar-report").html()+'</div>')
            }
            function afterPrint(){
                $("#print-area").remove()
            }
            if(window.matchMedia){
                var mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (mql.matches) {
                        beforePrint();
                    } else {
                        afterPrint();
                    }
                });
            }
            window.onbeforeprint = beforePrint;
            window.onafterprint = afterPrint;
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                window.print();
            })
        })
    </script>
    <div ng-controller="BlockListReportController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="pull-right">
                            <span class="control-label" style="padding: 5px 8px">
                                View report in&nbsp;&nbsp;&nbsp;<input type="radio" class="radio-inline"
                                                                       style="margin: 0 !important;" value="eng"
                                                                       ng-change="loadReportData('blocklisted_ansar_report',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio" ng-change="loadReportData('blocklisted_ansar_report',reportType)"
                                             class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                    </div><Br>
                    <filter-template
                            show-item="['range','unit','thana']"
                            type="all"
                            range-change="loadPage()"
                            unit-change="loadPage()"
                            thana-change="loadPage()"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                            data="param"
                            on-load="loadPage()"
                    ></filter-template>
                    <div id="print-blocklisted-ansar-report">
                        <h3 style="text-align: center" id="report-header">[[report.ansar.ansar_title]]([[total]])&nbsp;&nbsp;
                            <a href="#" title="print" id="print-report">
                                <span class="glyphicon glyphicon-print"></span>
                            </a></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>[[report.ansar.sl_no]]</th>
                                    <th>[[report.ansar.id]]</th>
                                    <th>[[report.ansar.name]]</th>
                                    <th>[[report.ansar.rank]]</th>
                                    <th>[[report.ansar.district]]</th>
                                    {{--<th>[[report.ansar.thana]]</th>--}}
                                    <th>[[report.ansar.date_of_birth]]</th>
                                    <th>[[report.ansar.sex]]</th>
                                    <th>[[report.ansar.blocklisted_from_where]]</th>
                                    <th>[[report.ansar.blocked_reason]]</th>
                                    <th>[[report.ansar.blocked_date]]</th>
                                </tr>
                                <tr ng-repeat="a in ansars.ansars">
                                    <td>[[ansars.index+$index]]</td>
                                    <td>[[a.id]]</td>
                                    <td>[[a.name]]</td>
                                    <td>[[a.rank]]</td>
                                    <td>[[a.unit]]</td>
                                    <td>[[a.birth_date|dateformat:'DD-MMM-YYYY']]</td>
                                    <td>[[a.sex]]</td>
                                    <td>[[a.block_list_from]]</td>
                                    <td>[[a.comment_for_block]]</td>
                                    <td>[[a.date_for_block|dateformat:'DD-MMM-YYYY']]</td>
                                </tr>
                                <tr ng-if="ansars.ansars==undefined||ansars.ansars.length<=0">
                                    <td colspan="10" class="warning">
                                        No Ansar available
                                    </td>
                                </tr>
                            </table>
                            <div class="table_pagination" ng-if="pages.length>1">
                                <ul class="pagination">
                                    <li ng-class="{disabled:currentPage == 0}">
                                        <a href="#" ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                    </li>
                                    <li ng-repeat="page in pages|filter:filterFirstPage"
                                        ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                        <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                        <a href="#" ng-click="loadPage(page,$event)" ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                        <span ng-show="loadingPage[page.pageNum]"  style="position: relative"><i class="fa fa-spinner fa-pulse" style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                    </li>
                                    <li ng-class="{disabled:currentPage >5}" ng-show="currentPage >5&&pages.length>8">
                                        <span>...</span>
                                        {{--<a href="#" ng-click="loadAnsar(currentPage - 1)" ng-hide="currentPage == 0">&laquo;</a>--}}
                                    </li>
                                    <li ng-repeat="page in pages|filter:filterMiddlePage"
                                        ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                        <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                        <a href="#" ng-click="loadPage(page,$event)" ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                        <span ng-show="loadingPage[page.pageNum]"  style="position: relative"><i class="fa fa-spinner fa-pulse" style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                    </li>
                                    <li ng-class="{disabled:currentPage <pages.length-6}"
                                        ng-show="pages.length>8&&currentPage<pages.length-6">
                                        <span>...</span>
                                        {{--<a href="#" ng-click="loadAnsar(currentPage - 1)" ng-hide="currentPage == 0">&laquo;</a>--}}
                                    </li>
                                    <li ng-repeat="page in pages|filter:filterLastPage"
                                        ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                        <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                        <a href="#" ng-click="loadPage(page,$event)" ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                        <span ng-show="loadingPage[page.pageNum]"  style="position: relative"><i class="fa fa-spinner fa-pulse" style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                    </li>
                                    <li ng-class="{disabled:currentPage==pages.length-1}">
                                        <a href="#" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop