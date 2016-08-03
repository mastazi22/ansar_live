@extends('template.master')
@section('title','Ansar in Guard Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('guard_report') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http, $sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.districts = [];
            $scope.thanas = [];
            $scope.selectedDistrict = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.guards = [];
            $scope.guardDetail = [];
            $scope.ansars = [];
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.report = {};
            $scope.errorFound=0;
            $scope.reportType = 'eng';
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}')
            $scope.loadDistrict = function () {
                $scope.loadingUnit = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.loadingUnit = false;
                    $scope.thanas = [];
                    $scope.selectedThana = "";
                })
            }
            $scope.loadThana = function (id) {
                $scope.loadingThana = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "";
                    $scope.loadingThana = false;
                })
            }
            $scope.loadGuard = function (id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.guards = response.data;
                    $scope.selectedKpi = "";
                    $scope.loadingKpi = false;
                })
            }
            $scope.loadAnsar = function (id) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('guard_list')}}',
                    params: {
                        kpi_id: id,
                        unit: $scope.selectedDistrict,
                        thana: $scope.selectedThana
                    }
                }).then(function (response) {
                    $scope.errorFound=0;
                    $scope.allLoading = false;
                    $scope.ansars = response.data.ansars;
                    $scope.guardDetail = response.data.guard;
                },function(response){
                    $scope.errorFound=1;
                    $scope.allLoading = false;
                    $scope.guardDetail = [];
                    $scope.ansars = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    //alert($(".table").html())
                })
            }
            $scope.loadReportData = function (reportName, type) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('localize_report')}}',
                    params: {name: reportName, type: type}
                }).then(function (response) {
                    console.log(response.data)
                    $scope.report = response.data;
                    $scope.allLoading = false;
                })
            }
            $scope.dateConvert=function(date){
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.loadReportData("ansar_in_guard_report", "eng")
            if ($scope.isAdmin != 22) {
                $scope.loadDistrict()
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.loadThana($scope.dcDistrict)
                }
            }

        })
        $(function () {
            function beforePrint(){
//                console.log($("body").find("#print-body").html())
                $("#print-area").remove();
                $('body').append('<div id="print-area">'+$("#print-guard-in-ansar-report").html()+'</div>')
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
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    var v = $(this).text();
                    $(this).parents('td').append('<span>' + v + '</span>')
                    $(this).css('display', 'none')
                })
                window.print();
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    $(this).parents('td').children('span').remove()
                    $(this).css('display', 'block')
                })
            })
        })
    </script>
    <div ng-controller="ReportGuardSearchController">
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
                                                                       ng-change="loadReportData('ansar_in_guard_report',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio" ng-change="loadReportData('ansar_in_guard_report',reportType)"
                                             class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                    </div><br>
                    <div class="row">
                        <div class="col-sm-4" ng-hide="isAdmin==22">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a District&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingUnit">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selectedDistrict"
                                        ng-change="loadThana(selectedDistrict)">
                                    <option value="">--Select a District--</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a Thana&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingThana">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selectedThana"
                                        ng-change="loadGuard(selectedThana)">
                                    <option value="">--Select a Thana--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a Guard&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingKpi">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selectedKPI"
                                        ng-change="loadAnsar(selectedKPI)">
                                    <option value="">--Select a Guard--</option>
                                    <option ng-repeat="d in guards" value="[[d.id]]">[[d.kpi_name]]
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="print-guard-in-ansar-report">
                        <h3 style="text-align: center" id="report-header">[[report.report_header]]&nbsp;&nbsp;
                            <a href="#" title="print" id="print-report">
                                <span class="glyphicon glyphicon-print"></span>
                            </a></h3>

                        <div class="report-heading">
                            <div class="report-heading-body">
                                <div class="report-heading-guard">
                                    <h4>[[report.guard.kpi_title]]</h4>

                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">[[report.guard.kpi_name]]</li>
                                            <li>[[guardDetail.kpi_name]]&nbsp;</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">[[report.guard.kpi_address]]</li>
                                            <li>[[guardDetail.kpi_address]], [[guardDetail.thana_name_bng]], [[guardDetail.unit_name_bng]]&nbsp;</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">[[report.guard.kpi_type]]</li>
                                            <li>--</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">
                                                [[report.guard.kpi_ansar_given]]
                                            </li>
                                            <li>[[guardDetail.total_ansar_given]]&nbsp;</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">
                                                [[report.guard.kpi_current_ansar]]
                                            </li>
                                            <li>[[ansars.length]]&nbsp;</li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <caption class="table-caption"
                                         style="text-align: center;font-size: 1.5em;font-weight: bold">
                                    [[report.ansar.ansar_title]]
                                </caption>
                                <tr>
                                    <th>[[report.ansar.sl_no]]</th>
                                    <th>[[report.ansar.id]]</th>
                                    <th>[[report.ansar.rank]]</th>
                                    <th>[[report.ansar.name]]</th>
                                    <th>[[report.ansar.district]]</th>
                                    <th>[[report.ansar.embodiment_date]]</th>
                                    <th>[[report.ansar.join_date]]</th>
                                </tr>
                                <tr ng-show="ansars.length==0">
                                    <td colspan="8" class="warning no-ansar">
                                        No Ansar is available to show
                                    </td>
                                </tr>
                                <tbody ng-if="errorFound==1" ng-bind-html="ansars"></tbody>
                                <tr ng-show="ansars.length>0" ng-repeat="a in ansars">
                                    <td>
                                        [[$index+1]]
                                    </td>
                                    <td>
                                        <a href="{{URL::to('HRM/entryreport')}}/[[a.ansar_id]]">[[a.ansar_id]]</a>
                                    </td>
                                    <td>
                                        [[a.name_bng]]
                                    </td>
                                    <td>
                                        [[a.ansar_name_bng]]
                                    </td>
                                    <td>
                                        [[a.unit_name_bng]]
                                    </td>
                                    <td>
                                        [[dateConvert(a.reporting_date)]]
                                    </td>
                                    <td>
                                        [[dateConvert(a.joining_date)]]
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@stop