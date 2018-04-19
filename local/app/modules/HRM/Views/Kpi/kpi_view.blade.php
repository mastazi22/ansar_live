{{--User: Shreya--}}
{{--Date: 12/24/2015--}}
{{--Time: 12:52 PM--}}

@extends('template.master')
@section('title','Active KPI Information')
@section('small_title')
    <a href="{{URL::route('go_to_kpi_page')}}" class="btn btn-sm btn-info"><span><i class="fa fa-plus"></i></span>&nbsp;&nbsp;Add New KPI</a>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('kpi_view') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('KpiViewController', function ($scope, $http, $sce, httpService) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.queue = [];
            $scope.params = ''
            $scope.allLoading = false;
            $scope.kpis = [];
            $scope.itemPerPage = parseInt('{{config('app.item_per_page')}}');
            $scope.currentPage = 0;
            $scope.pages = [];
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
            }
            $scope.loadPage = function (page, $event) {
                if ($event != undefined)  $event.preventDefault();
                $scope.exportPage = page;
                $scope.currentPage = page==undefined?0:page.pageNum;
                $scope.loadingPage[$scope.currentPage]=true;
                $http({
                    url: '{{URL::route('kpi_view_details')}}',
                    method: 'get',
                    params: {
                        offset: page==undefined?0:page.offset,
                        limit: page==undefined?$scope.itemPerPage:page.limit,
                        division: $scope.params.range,
                        unit: $scope.params.unit,
                        thana: $scope.params.thana,
                        q: $scope.q
                    }
                }).then(function (response) {
                    $scope.kpis = response.data.kpis;
                    console.log($scope.kpis)
//                    $compile($scope.ansars)
                    $scope.queue.shift();
                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    if($scope.queue.length>1) $scope.loadPage();
                    $scope.loadPagination();
                    $scope.loadingPage[$scope.currentPage] = false;
                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if(type=='page')$scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '{{URL::route('kpi_view_details')}}',
                    method: 'get',
                    params: {
                        offset: type=='all'?-1:(page == undefined ? 0 : page.offset),
                        limit: type=='all'?-1:(page == undefined ? $scope.itemPerPage : page.limit),
                        division: $scope.params.range,
                        unit: $scope.params.unit,
                        thana: $scope.params.thana,
                        q: $scope.q,
                        export:type
                    }
                }).then(function (res) {
                    $scope.export_data = res.data;
                    $scope.generating = true;
                    generateReport();
                    $scope.export_page =  $scope.export_all = false;
                },function (res) {
                    $scope.export_page =  $scope.export_all = false;
                })
            }
            $scope.file_count = 1;
            function generateReport(){
                $http({
                    url: '{{URL::to('HRM/generate/file')}}/'+$scope.export_data.id,
                    method: 'post',
                }).then(function (res) {
                    if($scope.export_data.total_file>$scope.file_count){
                        setTimeout(generateReport,1000);
                        if(res.data.status) $scope.file_count++;
                    }
                    else{
                        $scope.generating = false;
                        $scope.file_count = 1;
                        window.open($scope.export_data.download_url,'_blank')
                    }
                },function (res) {
                    if($scope.export_data.file_count>$scope.file_count){
                        setTimeout(generateReport,1000)
                    }
                })
            }
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
                    >

                    </filter-template>

                    <div class="row">
                        <div class="col-md-8"><h4>Total KPI: [[total.toLocaleString()]]</h4></div>
                        <div class="col-md-4">
                            <database-search q="q" queue="queue" on-change="loadPage()" place-holder="Search by KPI name"></database-search>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right" style="padding-bottom: 10px">
                                <button id="export-report" ng-disabled="export_page||export_all" ng-click="exportData('page')" class="btn btn-default ">
                                    <i ng-show="!export_page" class="fa fa-file-excel-o"></i><i ng-show="export_page" class="fa fa-spinner fa-pulse"></i>&nbsp;Export this page
                                </button>
                                <button  ng-disabled="export_page||export_all" ng-click="exportData('all')" id="export-report-all" class="btn btn-default">
                                    <i ng-show="!export_all" class="fa fa-file-excel-o"></i><i ng-show="export_all" class="fa fa-spinner fa-pulse"></i>&nbsp;Export all
                                </button>
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
                                <th>KPI Contact No.</th>
                                <th>Total Ansar Request</th>
                                <th>Total Ansar Given</th>
                                <th>Total Embodied Ansar</th>
                                <th>Percent</th>
                                <th>Deficiency</th>
                                <th>Surplus</th>
                                <th>Action</th>
                            </tr>
                            <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                            <tbody>
                            <tr ng-if="kpis.length==0&&errorFound==0">
                                <td colspan="12" class="warning no-ansar">
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
                                <td>[[a.total_ansar_request]]</td>
                                <td>[[a.total_ansar_given]]</td>
                                <td>[[a.total_embodied]]</td>
                                <td>[[(a.total_embodied*100)/a.total_ansar_given]]</td>
                                <td>[[a.total_ansar_request-a.total_embodied>0?((a.total_ansar_request-a.total_embodied)):0]]</td>
                                <td>[[a.total_ansar_request-a.total_embodied<0?(-(a.total_ansar_request-a.total_embodied)):0]]</td>
                                <td>
                                    <div class="col-xs-1">
                                        <a href="{{URL::to('HRM/kpi-edit/'.'[[a.id]]')}}" class="btn btn-primary btn-xs" title="Edit">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="table_pagination" ng-if="pages.length>1">
                            <ul class="pagination">
                                <li ng-class="{disabled:currentPage == 0}">
                                    <a href="#" ng-disabled="currentPage==0" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                </li>
                                <li ng-class="{disabled:currentPage == 0}">
                                    <a href="#" ng-disabled="currentPage==0" ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
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
                                    <a href="#" ng-disabled="currentPage==pages.length-1" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#" ng-disabled="currentPage==pages.length-1"
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