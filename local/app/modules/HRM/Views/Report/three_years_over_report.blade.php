{{--User: Shreya--}}
{{--Date: 12/30/2015--}}
{{--Time: 2:46 PM--}}

@extends('template.master')
@section('title','Three Years Over Ansar Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('three_year_over_report_view') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ReportThreeYearsOverList', function ($scope, $http, $sce) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.selectedDistrict = "";
            $scope.selectedRank = "";
            $scope.selectedSex= "";
            $scope.districts = [];
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingDistrict = true;
            $scope.loadingRank = false;
            $scope.loadingSex = false;
            $scope.isLoading = false;
            $scope.loadingPage = [];
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
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
                    url: '{{URL::route('three_years_over_ansar_info')}}',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        unit: $scope.selectedDistrict,
                        ansar_rank: $scope.selectedRank,
                        ansar_sex: $scope.selectedSex,
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
                    url: '{{URL::route('three_years_over_ansar_info')}}',
                    method: 'get',
                    params: {
                        unit: $scope.selectedDistrict,
                        ansar_rank: $scope.selectedRank,
                        ansar_sex: $scope.selectedSex,
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
                var minPage = $scope.currentPage-3<0?0:($scope.currentPage>array.length-4?array.length-8:$scope.currentPage-3);
                var maxPage = minPage+7;
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

            $scope.resetValues=function(){
                $scope.selectedRank = "";
                $scope.selectedSex = "";
            }

            $scope.loadReportData = function (reportName,type) {
                $http({
                    method:'get',
                    url:'{{URL::route('localize_report')}}',
                    params:{name:reportName,type:type}
                }).then(function(response){
                    console.log(response.data)
                    $scope.report = response.data;
                })
            }
            $scope.dateConvert=function(date){
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.loadReportData("three_years_over_ansar_report","eng")
            $scope.loadTotal();

        })
        $(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $('body').append('<div id="print-area">' + $("#print-three_years_over_ansar_report").html() + '</div>')
                window.print();
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="ReportThreeYearsOverList">
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
                                                                       ng-change="loadReportData('three_years_over_ansar_report',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio"
                                             ng-change="loadReportData('three_years_over_ansar_report',reportType)"
                                             class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                    </div><br>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label class="control-label">Select Unit&nbsp;
                                    <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select class="form-control" ng-model="selectedDistrict">
                                    <option value="">--Select--</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label class="control-label">
                                    Select Rank
                                </label>
                                <select name="ansar_rank" class="form-control" ng-model="selectedRank">
                                    <option value="" disabled>--Select--</option>
                                    <option value="1">Ansar</option>
                                    <option value="2">APC</option>
                                    <option value="3">PC</option>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label class="control-label">
                                    Select Sex
                                </label>
                                <select name="ansar_sex" class="form-control" ng-model="selectedSex">
                                    <option value="" disabled>--Select--</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12 col-xs-12" style="margin-top: 25px">
                            <a class="btn btn-primary pull-right" ng-click="loadTotal(selectedDistrict,selectedRank,selectedSex)">Load Result</a>
                        </div>
                    </div>
                    <div id="print-three_years_over_ansar_report">
                        <h3 style="text-align: center" id="report-header">[[report.ansar.ansar_title]]&nbsp;&nbsp;
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
                                    <th>[[report.ansar.district]]</th>
                                    <th>[[report.ansar.kpi_name]]</th>
                                    <th>[[report.ansar.reporting_date]]</th>
                                    <th>[[report.ansar.joining_date]]</th>
                                    <th>[[report.ansar.service_ended_date]]</th>
                                </tr>
                                <tbody ng-bind-html="ansars"></tbody>
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
            </div>
        </section>
    </div>
@stop