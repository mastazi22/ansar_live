@extends('template.master')
@section('title','Blocklist Info')
@section('breadcrumb')
    {!! Breadcrumbs::render('blocklist_view') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('BlockListReportController', function ($scope, $http, $sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $scope.total = 0;
            $scope.reportType = 'eng';
            $scope.queue = [];
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
            $scope.loadPagination = function () {
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    });
                    $scope.loadingPage[i] = false;
                }
            };
            $scope.loadPage = function (page, $event) {
                $scope.allLoading = true;
                if ($event != undefined) $event.preventDefault();
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $http({
                    url: '{{URL::route('blocklisted_ansar_info')}}',
                    method: 'get',
                    params: {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit,
                        thana: $scope.param.thana,
                        division: $scope.param.range,
                        rank: $scope.param.rank == undefined ? 'all' : $scope.param.rank,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        q: $scope.q
                    }
                }).then(function (response) {
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.ansars = response.data;
                    $scope.allLoading = false;
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            };
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if (type == 'page') $scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '{{URL::route('blocklisted_ansar_info')}}',
                    method: 'get',
                    params: {
                        offset: type == 'all' ? -1 : (page == undefined ? 0 : page.offset),
                        limit: type == 'all' ? -1 : (page == undefined ? $scope.itemPerPage : page.limit),
                        unit: $scope.param.unit,
                        thana: $scope.param.thana,
                        division: $scope.param.range,
                        q: $scope.q,
                        export: type
                    }
                }).then(function (res) {
                    $scope.export_data = res.data;
                    $scope.generating = true;
                    generateReport();
                    $scope.export_page = $scope.export_all = false;
                }, function (res) {
                    $scope.export_page = $scope.export_all = false;
                })
            };
            $scope.file_count = 1;

            function generateReport() {
                $http({
                    url: '{{URL::to('HRM/generate/file')}}/' + $scope.export_data.id,
                    method: 'post'
                }).then(function (res) {
                    if ($scope.export_data.total_file > $scope.file_count) {
                        setTimeout(generateReport, 1000);
                        if (res.data.status) $scope.file_count++;
                    } else {
                        $scope.generating = false;
                        $scope.file_count = 1;
                        window.open($scope.export_data.download_url, '_blank')
                    }
                }, function (res) {
                    if ($scope.export_data.file_count > $scope.file_count) {
                        setTimeout(generateReport, 1000)
                    }
                })
            }

            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            };
            $scope.loadReportData = function (reportName, type) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('localize_report')}}',
                    params: {name: reportName, type: type}
                }).then(function (response) {
                    $scope.report = response.data;
                    $scope.allLoading = false;
                })
            };
            $scope.loadReportData("blocklisted_ansar_report", "eng");
        });
        $(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $("#print-area").remove();
                $('body').append('<div id="print-area">' + $("#print-blocklisted-ansar-report").html() + '</div>')
                window.print();
                $("#print-area").remove()
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
                <div class="overlay" ng-if="generating">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                        <span>[[(file_count)+'/'+export_data.total_file]]</span>
                    </span>
                </div>
                <div class="box-body">
                    <div class="pull-right">
                            <span class="control-label" style="padding: 5px 8px">
                                View report in&nbsp;&nbsp;&nbsp;<input type="radio" class="radio-inline"
                                                                       style="margin: 0 !important;" value="eng"
                                                                       ng-change="loadReportData('blocklisted_ansar_report',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio"
                                             ng-change="loadReportData('blocklisted_ansar_report',reportType)"
                                             class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                    </div>
                    <Br>
                    <filter-template
                            show-item="['range','unit','thana','rank','gender']"
                            type="all"
                            range-change="loadPage()"
                            unit-change="loadPage()"
                            thana-change="loadPage()"
                            gender-change="loadPage()"
                            rank-change="loadPage()"
                            start-load="range"
                            field-width="{range:'col-sm-2',unit:'col-sm-2',thana:'col-sm-3',rank:'col-sm-2',gender:'col-sm-3'}"
                            data="param"
                            on-load="loadPage()"
                    ></filter-template>
                    <div id="print-blocklisted-ansar-report">
                        <h3 id="report-header">[[report.ansar.ansar_title]]([[total]])&nbsp;&nbsp;
                            <div class="btn-group btn-group-sm pull-right print-hide">
                                <button id="print-report" class="btn btn-default"><i
                                            class="fa fa-print"></i>&nbsp;Print
                                </button>
                                <button id="export-report" ng-disabled="export_page||export_all"
                                        ng-click="exportData('page')" class="btn btn-default ">
                                    <i ng-show="!export_page" class="fa fa-file-excel-o"></i><i ng-show="export_page"
                                                                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Export
                                    this page
                                </button>
                                <button ng-disabled="export_page||export_all" ng-click="exportData('all')"
                                        id="export-report-all" class="btn btn-default">
                                    <i ng-show="!export_all" class="fa fa-file-excel-o"></i><i ng-show="export_all"
                                                                                               class="fa fa-spinner fa-pulse"></i>&nbsp;Export
                                    all
                                </button>
                            </div>
                        </h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <caption class="print-hide">
                                    <div class="col-sm-4 col-sm-offset-8">
                                        <database-search q="q" queue="queue" on-change="loadPage()"></database-search>
                                    </div>
                                </caption>
                                <tr>
                                    <th>[[report.ansar.sl_no]]</th>
                                    <th>[[report.ansar.id]]</th>
                                    <th>[[report.ansar.name]]</th>
                                    <th>[[report.ansar.rank]]</th>
                                    <th>[[report.ansar.district]]</th>
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
                        </div>
                        <div class="row">
                            <div class="col-sm-4 print-hide">
                                <label for="item_par_page">Show :</label>
                                <select name="item_per_page" ng-change="loadPage()" id="item_par_page"
                                        ng-model="itemPerPage">
                                    <option value="20" ng-selected="true">20</option>
                                    <option value="40">40</option>
                                    <option value="60">60</option>
                                    <option value="80">80</option>
                                    <option value="100">100</option>
                                    <option value="150">150</option>
                                    <option value="200">200</option>
                                </select>
                            </div>
                            <div class="col-sm-8 print-hide">
                                <div class="table_pagination" ng-if="pages.length>1">
                                    <ul class="pagination" style="margin: 0">
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
                                            <a href="#" ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop