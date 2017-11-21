@extends('template.master')
@section('title',$pageTitle)
{{--@section('small_title',ucfirst(implode(' ',explode('_',$type))))--}}
{{--@section('small_title', $pageTitle)--}}
@section('breadcrumb')
    {{--    {!! Breadcrumbs::render('dashboard_menu',ucwords(implode(' ',explode('_',$type))),$type) !!}--}}
    {!! Breadcrumbs::render('dashboard_menu', $pageTitle, $type) !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarListController', function ($scope, $http, $sce, $parse) {
            $scope.ansarType = '{{$type}}';
            $scope.rank = 'all'
            $scope.queue = [];
            $scope.exportPage = ''
            var p = $scope.ansarType.split('_');
            $scope.pageTitle = '';
            for (var i = 0; i < p.length; i++) {
                $scope.pageTitle += capitalizeLetter(p[i]);
                if (i < p.length - 1)$scope.pageTitle += " ";
            }
            $scope.defaultPage = {pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'}
            $scope.total = 0
            $scope.param = {};
            $scope.numOfPage = 0
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingPage = []
            $scope.allLoading = true;
//Start pagination
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
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $scope.allLoading = true;
                $http({
                    url: '{{URL::to('HRM/get_ansar_list')}}',
                    method: 'get',
                    params: {
                        type: $scope.ansarType,
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        thana: $scope.param.thana == undefined ? 'all' : $scope.param.thana,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        q: $scope.q,
                        rank: $scope.rank,
                    }
                }).then(function (response) {
                    console.log(response.data);
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.ansars = response.data;
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
                    console.log($scope.total)
                    $scope.gCount = response.data.total
//                    sum($scope.total)
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            }
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if(type=='page')$scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '{{URL::to('HRM/get_ansar_list')}}',
                    method: 'get',
                    params: {
                        type: $scope.ansarType,
                        offset: type=='all'?-1:(page == undefined ? 0 : page.offset),
                        limit: type=='all'?-1:(page == undefined ? $scope.itemPerPage : page.limit),
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        thana: $scope.param.thana == undefined ? 'all' : $scope.param.thana,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        q: $scope.q,
                        rank: $scope.rank,
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
            $scope.search = function () {


            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
//End pagination
            $scope.changeRank = function (i) {
                $scope.rank = i;
                $scope.loadPage()
            }
//            $scope.loadTotal()
            function capitalizeLetter(s) {
                return s.charAt(0).toUpperCase() + s.slice(1);
            }

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }
        })
        $(function () {
            $("#print-report").on('click', function (e) {
                $("#print-area").remove();
                $("#print_table table").removeClass('table table-bordered')
                $('body').append('<div id="print-area">' + $("#print_table").html() + '</div>')
                window.print();
                $("#print_table table").addClass('table table-bordered')
                $("#print-area").remove()
            })
        })
    </script>

    <div ng-controller="AnsarListController" style="position: relative;">
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
                    @if($pageTitle=="Total Paneled Ansars")
                        <filter-template
                                show-item="['range','unit','thana','gender']"
                                type="all"
                                range-change="loadPage()"
                                unit-change="loadPage()"
                                thana-change="loadPage()"
                                gender-change="loadPage()"
                                on-load="loadPage()"
                                data="param"
                                start-load="range"
                                field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',gender:'col-sm-3'}"
                        ></filter-template>
                            @else
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
                                    @endif
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="btn-group btn-group-sm pull-right">
                                            <button id="print-report" class="btn btn-default"><i
                                                        class="fa fa-print"></i>&nbsp;Print
                                            </button>
                                            <button id="export-report" ng-disabled="export_page||export_all" ng-click="exportData('page')" class="btn btn-default ">
                                                <i ng-show="!export_page" class="fa fa-file-excel-o"></i><i ng-show="export_page" class="fa fa-spinner fa-pulse"></i>&nbsp;Export this page
                                            </button>
                                            <button  ng-disabled="export_page||export_all" ng-click="exportData('all')" id="export-report-all" class="btn btn-default">
                                                <i ng-show="!export_all" class="fa fa-file-excel-o"></i><i ng-show="export_all" class="fa fa-spinner fa-pulse"></i>&nbsp;Export all
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 col-sm-12">
                                        <h4 class="text text-bold">
                                            <a class="btn btn-primary text-bold" href="#" ng-click="changeRank('all')">Total
                                                Ansars ([[total]])</a>&nbsp;
                                            <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(3)">PC
                                                ([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])</a>&nbsp;
                                            <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(2)">APC
                                                ([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])</a>&nbsp;
                                            <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(1)">Ansar
                                                ([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</a>
                                        </h4>
                                    </div>
                                    <div class="col-md-4 col-sm-12" style="margin-top: 10px">
                                        <database-search q="q" queue="queue" on-change="loadPage()"></database-search>
                                    </div>
                                </div>
                                <div id="print_table">
                                    <div class="table-responsive">
                                        <div>
                                            <h4 class="text text-center print-open">{{$pageTitle}}</h4>
                                            <template-list data="ansars" key="{{$type}}" call-back="loadPage()"></template-list>
                                        </div>


                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
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
                                    <div class="col-sm-8">
                                        <div class="table_pagination" ng-if="pages.length>1">
                                            <ul class="pagination" style="margin: 0">
                                                <li ng-class="{disabled:currentPage == 0}">
                                                    <a href="#" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                                </li>
                                                <li ng-class="{disabled:currentPage == 0}">
                                                    <a href="#"
                                                       ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                                </li>
                                                <li ng-repeat="page in pages|filter:filterMiddlePage"
                                                    ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                                    <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                                    <a href="#" ng-click="loadPage(page,$event)"
                                                       ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                                    <span ng-show="loadingPage[page.pageNum]"
                                                          style="position: relative"><i class="fa fa-spinner fa-pulse"
                                                                                        style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                                </li>
                                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                                    <a href="#"
                                                       ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
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
            </div>
        </section>
    </div>
@stop