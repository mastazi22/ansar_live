{{--User: Shreya--}}
{{--Date: 12/22/2015--}}
{{--Time: 11:40 AM--}}

@extends('template.master')
@section('title','Disembodied Ansar Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('disembodiment_report_view') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#from_date').datePicker(false);
            $("#to_date").datePicker(true);

        })
        GlobalApp.controller('ReportAnsarDisembodiment', function ($scope, $http, $sce) {
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
            $scope.isLoading = false;
            $scope.loadingDistrict = true;
            $scope.loadingThana = false;
            $scope.loadingPage = [];
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.from_date = moment().subtract(1, 'years').format("D-MMM-YYYY");
            $scope.to_date = moment().format("D-MMM-YYYY");

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
                    url: '{{URL::route('disemboded_ansar_info')}}',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        unit_id: $scope.selectedDistrict,
                        thana_id: $scope.selectedThana,
                        from_date: $scope.from_date,
                        to_date: $scope.to_date,
                        view: 'view'
                    }
                }).then(function (response) {
                    $scope.ansars = $sce.trustAsHtml(response.data);
                    $scope.loadingPage[page.pageNum] = false;
                })
            }
            $scope.loadTotal = function () {
                $scope.isLoading = true;
                //alert('here');
                //alert($scope.selectedDistrict+" "+$scope.selectedRank+" "+$scope.selectedSex)
                $http({
                    url: '{{URL::route('disemboded_ansar_info')}}',
                    method: 'get',
                    params: {
                        unit_id: $scope.selectedDistrict,
                        thana_id: $scope.selectedThana,
                        from_date: $scope.from_date,
                        to_date: $scope.to_date,
                        view: 'count'
                    }
                }).then(function (response) {
                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                    //alert($scope.total);
//                    $scope.selectedDistrict = [];
//                    $scope.selectedRank = [];
//                    $scope.selectedSex = '';
                    $scope.isLoading = false;
                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }

            $http({
                method: 'get',
                url: '{{URL::to('HRM/DistrictName')}}'
            }).then(function (response) {
                $scope.districts = response.data;
                $scope.loadingDistrict = false;
                //$scope.loadTotal();

            })
            $scope.loadThana = function (id) {
                $scope.loadingThana = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "all";
                    $scope.loadingThana = false;
                })
            }

            $scope.loadReportData = function (reportName, type) {
                $http({
                    method: 'get',
                    url: '{{URL::route('localize_report')}}',
                    params: {name: reportName, type: type}
                }).then(function (response) {
                    //console.log(response.data)
                    $scope.report = response.data;
                })
            }
            $scope.resetValues = function () {
                $scope.selectedDistrict = "all";
                $scope.selectedThana = "all";
            }
            $scope.loadReportData("ansar_disembodiment_report", "eng")
            $scope.loadTotal();

        })
        $(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $('body').append('<div id="print-area">' + $("#print_ansar_disembodiment_report").html() + '</div>')
                window.print();
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="ReportAnsarDisembodiment">
        <div class="loading-report animated" ng-show="isLoading" ng-class="{'fadeInDown visible':isLoading,fadeOutUp:!isLoading}">
            <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
            <h4>Loading...</h4>
        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="pull-right">
                            <span class="control-label" style="padding: 5px 8px">
                                View report in&nbsp;&nbsp;&nbsp;<input type="radio" class="radio-inline"
                                                                       style="margin: 0 !important;" value="eng"
                                                                       ng-change="loadReportData('ansar_disembodiment_report',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio"
                                             ng-change="loadReportData('ansar_disembodiment_report',reportType)"
                                             class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-4 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Select Date Range
                                </label></br>
                                <div class="col-md-5 col-sm-12 col-xs-12" style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" class="form-control"
                                           placeholder="From Date" ng-model="from_date" ng-change="resetValues()">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12" style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    {{--<button class="btn pull-left" style="border: none; background: #ffffff;">to</button><br>--}}
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-5 col-sm-12 col-xs-12" style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date" ng-change="resetValues()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Select Unit
                                    <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                </label>
                                <select name="unit_id" class="form-control" ng-model="selectedDistrict"
                                        ng-change="loadThana(selectedDistrict)">
                                    {{--<option value="" disabled>--Select a Unit--</option>--}}
                                    <option value="all">All</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Select Thana
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16">
                                </label>
                                <select name="thana_id" class="form-control" ng-model="selectedThana">
                                    {{--<option value="" disabled>--Select a Thana--</option>--}}
                                    <option value="all">All</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12" style="margin-top: 25px">
                            <a class="btn btn-primary pull-right" ng-click="loadTotal(selectedDistrict,selectedThana,to_date,from_date)">Load Result</a>
                        </div>
                    </div>
                    <div id="print_ansar_disembodiment_report">
                        <h3 style="text-align: center" id="report-header">[[report.header]]&nbsp;&nbsp;
                            <a href="#" title="print" id="print-report">
                                <span class="glyphicon glyphicon-print"></span>
                            </a></h3>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>[[report.ansar.sl_no]]</th>
                                    <th>[[report.ansar.id]]</th>
                                    <th>[[report.ansar.rank]]</th>
                                    <th>[[report.ansar.name]]</th>
                                    <th>[[report.ansar.kpi_name]]</th>
                                    <th>[[report.ansar.district]]</th>
                                    <th>[[report.ansar.reporting_date]]</th>
                                    <th>[[report.ansar.joining_date]]</th>
                                    <th>[[report.ansar.disembodiment_reason]]</th>
                                    <th>[[report.ansar.disembodiment_date]]</th>
                                </tr>
                                <tbody ng-bind-html="ansars"></tbody>
                            </table>

                        </div>
                    </div>
                    <div class="table_pagination" ng-if="pages.length>1">
                        <ul class="pagination" style="margin-bottom: 20px">
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
        </section>
    </div>
@stop