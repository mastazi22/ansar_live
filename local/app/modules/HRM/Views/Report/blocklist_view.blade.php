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
                if($scope.numOfPage>0)$scope.loadPage($scope.pages[0]);
                else $scope.loadPage({pageNum:0,offset:0,limit:$scope.itemPerPage,view:'view'});
            }
            $scope.loadPage = function (page,$event) {
                if($event!=undefined)  $event.preventDefault();
                $scope.currentPage = page.pageNum;
                $scope.loadingPage[page.pageNum]=true;
                $http({
                    url: '{{URL::route('blocklisted_ansar_info')}}',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        unit:$scope.selectedDistrict,
                        thana:$scope.selectedThana,
                        view:'view'
                    }
                }).then(function (response) {
                    $scope.ansars = $sce.trustAsHtml(response.data);
                    $scope.loadingPage[page.pageNum]=false;
                })
            }
            $scope.loadTotal = function () {
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('blocklisted_ansar_info')}}',
                    method: 'get',
                    params: {
                        unit:$scope.selectedDistrict,
                        thana:$scope.selectedThana,
                        view:'count'
                    }
                }).then(function (response) {
                    $scope.total = response.data.total;
                    //alert($scope.total)
                    $scope.numOfPage = Math.ceil($scope.total/$scope.itemPerPage);
                    $scope.loadPagination();
                    $scope.allLoading = false;
                },function(response){
                    $scope.total = 0;
                    $scope.ansars = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    $scope.allLoading = false;
                    $scope.pages = [];
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

            $scope.loadDistrict = function () {
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.loadingDistrict = false;
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
            if ($scope.isAdmin == 11) {
                $scope.loadDistrict()
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.loadThana($scope.dcDistrict)
                }
            }
            $scope.loadTotal()
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
                    <div class="row">
                        <div class="col-sm-4" ng-show="isAdmin==11">
                            <div class="form-group">
                                <label class="control-label">Select a District&nbsp;
                                    <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDistrict"
                                        ng-change="loadThana(selectedDistrict)">
                                    <option value="all">All</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a Thana
                                </label>
                                <select class="form-control" ng-model="selectedThana"
                                        ng-change="loadTotal(selectedThana)">
                                    <option value="all">All</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
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
                                <tbody ng-bind-html="ansars"></tbody>
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