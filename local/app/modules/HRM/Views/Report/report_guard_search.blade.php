@extends('template.master')
@section('title','View Ansar in Guard Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('guard_report') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http, $sce) {
            $scope.guardDetail = [];
            $scope.ansars = [];
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.report = {};
            $scope.errorFound=0;
            $scope.reportType = 'eng';
            $scope.loadAnsar = function () {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('guard_list')}}',
                    params: {
                        kpi_id: $scope.param.kpi,
                        unit: $scope.param.unit,
                        thana: $scope.param.thana,
                        division: $scope.param.range
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

        })
        $(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    var v = $(this).text();
                    $(this).parents('td').append('<span>' + v + '</span>')
                    $(this).css('display', 'none')
                })
                $("#print-area").remove();
                $('body').append('<div id="print-area">'+$("#print-guard-in-ansar-report").html()+'</div>')
                window.print();
                $("#print-area").remove()
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
                    <filter-template
                            show-item="['range','unit','thana','kpi']"
                            type="single"
                            kpi-change="loadAnsar()"
                            start-load="range"
                            data="param"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                    >

                    </filter-template>
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
                                <caption class="table-caption" style="text-align: center;font-size: 1.5em;font-weight: bold">
                                    [[report.ansar.ansar_title]]([[ansars.length]])
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
                                        [[dateConvert(a.joining_date)]]
                                    </td>
                                    <td>
                                        [[a.transfered_date?dateConvert(a.transfered_date):'--']]
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