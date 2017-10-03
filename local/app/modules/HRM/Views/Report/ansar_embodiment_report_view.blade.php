{{--User: Shreya--}}
{{--Date: 12/30/2015--}}
{{--Time: 11:54 AM--}}

@extends('template.master')
@section('title','Embodied Ansar Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('embodiment_report_view') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#from_date').datepicker({                dateFormat:'dd-M-yy'            })({
                defaultValue:false
            });
            $("#to_date").datepicker({                dateFormat:'dd-M-yy'            })({
                defaultValue:false
            });

        })
        GlobalApp.controller('ReportAnsarEmbodiment', function ($scope, $http, $sce) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.reportType = 'eng'
            $scope.selectedDistrict = "all";
            $scope.selectedThana = "all";
            $scope.districts = [];
            $scope.thanas= [];
            $scope.allLoading = false;
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansars = []

            $scope.pages = [];
            $scope.loadingDistrict = true;
            $scope.loadingThana=false;
            $scope.loadingPage = [];
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');

           // var a = new Date();
            $scope.from_date=moment().subtract(1,'years').format("D-MMM-YYYY");
            $scope.to_date=moment().format("D-MMM-YYYY");
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
                $scope.allLoading = true;
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page==undefined?0:page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $http({
                    url: '{{URL::route('emboded_ansar_info')}}',
                    method: 'get',
                    params: {
                        offset: page==undefined?0:page.offset,
                        limit: page==undefined?$scope.itemPerPage:page.limit,
                        unit_id:$scope.param.unit,
                        division_id:$scope.param.range,
                        thana_id: $scope.param.thana,
                        from_date:$scope.from_date,
                        to_date:$scope.to_date,
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
                var minPage = $scope.currentPage-3<0?0:($scope.currentPage>array.length-4?array.length-8:$scope.currentPage-3);
                var maxPage = minPage+7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
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
            $scope.loadReportData("ansar_embodiment_report","eng")

        })
        $(function () {
            function beforePrint(){
//                console.log($("body").find("#print-body").html())
                $("#print-area").remove()
                $('body').append('<div id="print-area">'+$("#print_ansar_embodiment_report").html()+'</div>')
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
    <div ng-controller="ReportAnsarEmbodiment">
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
                                                                       ng-change="loadReportData('ansar_embodiment_report',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio"
                                             ng-change="loadReportData('ansar_embodiment_report',reportType)"
                                             class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                    </div><br>
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
                    >

                    </filter-template>
                    <div class="row">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Select Date Range
                                </label></br>
                                <div class="col-md-5 col-sm-12 col-xs-12" style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" class="form-control"
                                           placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12" style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    {{--<button class="btn pull-left" style="border: none; background: #ffffff;">to</button><br>--}}
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-5 col-sm-12 col-xs-12" style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12" style="margin-top: 25px">
                            <a class="btn btn-primary pull-right" ng-click="loadPage()">Load Result</a>
                        </div>
                    </div>
                    <div id="print_ansar_embodiment_report">
                        <h3 style="text-align: center" id="report-header">[[report.header]]([[total]])&nbsp;&nbsp;
                            <a href="#" title="print" id="print-report">
                                <span class="glyphicon glyphicon-print"></span>
                            </a></h3>

                        <div class="table-responsive">
                            <table class="table table-bordered full">
                                <tr>
                                    <th>[[report.ansar.sl_no]]</th>
                                    <th>[[report.ansar.id]]</th>
                                    <th>[[report.ansar.rank]]</th>
                                    <th>[[report.ansar.name]]</th>
                                    <th>[[report.ansar.kpi_name]]</th>
                                    <th>[[report.ansar.district]]</th>
                                    <th>[[report.ansar.reporting_date]]</th>
                                    <th>[[report.ansar.joining_date]]</th>
                                    <th>[[report.ansar.service_ended_date]]</th>
                                </tr>
                                <tr ng-repeat="a in ansars.ansars">
                                    <td>[[ansars.index+$index]]</td>
                                    <td>[[a.id]]</td>
                                    <td>[[a.name]]</td>
                                    <td>[[a.rank]]</td>
                                    <td>[[a.kpi]]</td>
                                    <td>[[a.unit]]</td>
                                    <td>[[a.r_date|dateformat:'DD-MMM-YYYY']]</td>
                                    <td>[[a.j_date|dateformat:'DD-MMM-YYYY']]</td>
                                    <td>[[a.se_date|dateformat:'DD-MMM-YYYY']]</td>
                                </tr>
                                <tr ng-if="ansars.ansars==undefined||ansars.ansars.length<=0">
                                    <td colspan="9" class="warning">
                                        No Ansar available
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
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
        </section>
    </div>
@stop