@extends('template.master')
@section('title','Ansar Service Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('ansar_service_report_view') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('serviceRecordController', function ($scope, $http) {
            $scope.ansarId = "";
            $scope.reportType = 'eng';
            $scope.currentServiceDate = 0;
            $scope.current = "";
            $scope.isLoading = true;
            $scope.past = [];
            $scope.ansar = ""
            $scope.pi = false;
            $scope.loadAnsarServiceRecord = function () {
                $scope.isLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('ansar_service_report')}}',
                    params: {ansar_id: $scope.ansarId}
                }).then(function (response) {
                    $scope.current = response.data.current;
                    $scope.past = response.data.past;
                    $scope.ansar = response.data.ansar;
                    $scope.pi = response.data.pi;
                    console.log(response.data)
                    $scope.isLoading = false;
                    $scope.currentServiceDate = getCurrentServiceDate(new Date($scope.current.joining_date), new Date())
                })
            }
            function getCurrentServiceDate(c, d) {
                var _MS_PER_DAY = 1000 * 60 * 60 * 24;
                var cud = Date.UTC(c.getFullYear(), c.getMonth(), c.getDate());
                var dd = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate())
                return Math.floor((dd - cud) / _MS_PER_DAY);
            }

            $scope.getServiceDate = function (c, d) {
                var _MS_PER_DAY = 1000 * 60 * 60 * 24;
                c = new Date(c);
                d = new Date(d);
                var cud = Date.UTC(c.getFullYear(), c.getMonth(), c.getDate());
                var dd = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate())
                return Math.abs(Math.floor((dd - cud) / _MS_PER_DAY));
            }
            $scope.loadReportData = function (reportName, type) {
                $scope.isLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('localize_report')}}',
                    params: {name: reportName, type: type}
                }).then(function (response) {
                    console.log(response.data)
                    $scope.report = response.data;
                    $scope.isLoading = false;
                })
            }
            $scope.dateConvert = function (date) {
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.loadReportData("ansar_service_report", "eng")
        })
        $(function () {
            $('body').on('click', '#print-report', function (e) {
                e.preventDefault();
                $('body').append('<div id="print-area" class="letter">' + $("#report-ansar-service").html() + '</div>')
                window.print();
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="serviceRecordController">
        <div class="loading-report animated" ng-class="{fadeInDown:isLoading,fadeOutUp:!isLoading}">
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
                                                                       ng-change="loadReportData('ansar_service_report',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio" ng-change="loadReportData('ansar_service_report',reportType)"
                                             class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-sm-offset-4 col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Enter a ansar id</label>
                                <input type="text" class="form-control" ng-model="ansarId" placeholder="Ansar id">
                                <button class="btn btn-default" style="margin-top: 10px"
                                        ng-click="loadAnsarServiceRecord()">
                                    View Ansar Service Record
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="report-ansar-service">
                        <h3 style="text-align:center">[[report.header]]&nbsp;&nbsp; <a href="#" id="print-report"><span
                                        class="glyphicon glyphicon-print"></span></a></h3>

                        <div class="table-responsive" align="center">
                            <table class="table " style="width: auto">
                                <tr>
                                    <td>[[report.ansar_detail.name]]</td>
                                    <td ng-if="!ansar">--</td>
                                    <td ng-if="ansar">[[ansar.ansar_name_bng]]</td>
                                    <td rowspan="4" align="center" valign="middle">
                                        <img ng-if="pi" src="{{asset('')}}[[ansar.profile_pic]]" class="img-thumbnail"
                                             style="width:120px;height: auto">
                                        <img ng-if="!pi" src="{{asset('')}}dist/img/nimage.png" class="img-thumbnail"
                                             style="width:120px;height: auto">
                                    </td>
                                </tr>
                                <tr>
                                    <td>[[report.ansar_detail.rank]]</td>
                                    <td ng-if="!ansar">--</td>
                                    <td ng-if="ansar">[[ansar.name_bng]]</td>
                                </tr>
                                <tr>
                                    <td>[[report.ansar_detail.bg]]</td>
                                    <td ng-if="!ansar">--</td>
                                    <td ng-if="ansar">[[ansar.blood_group_name_bng]]</td>
                                </tr>
                                <tr>
                                    <td>[[report.ansar_detail.district]]</td>
                                    <td ng-if="!ansar">--</td>
                                    <td ng-if="ansar">[[ansar.unit_name_bng]]</td>
                                </tr>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <h4 style="text-align: center;text-decoration: underline">[[report.current.header]]</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>[[report.current.jd]]</th>
                                    <th>[[report.current.rdmi]]</th>
                                    <th>[[report.current.dn]]</th>
                                    <th>[[report.current.kn]]</th>
                                    <th>[[report.current.dd]]</th>
                                    <th>[[report.current.tsd]]</th>
                                    <th>[[report.current.dr]]</th>
                                </tr>
                                <tr ng-if="!current">
                                    <td colspan="7">No information Found</td>
                                </tr>
                                <tr ng-if="current">
                                    <td>[[dateConvert(current.joining_date)]]</td>
                                    <td>[[current.memorandum_id]] & [[dateConvert(current.reporting_date)]]</td>
                                    <td>[[current.unit_name_bng]]</td>
                                    <td>[[current.kpi_name]]</td>
                                    <td>[[dateConvert(current.service_ended_date)]]</td>
                                    <td>[[currentServiceDate]]</td>
                                    <td>--</td>

                                </tr>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <h4 style="text-align: center;text-decoration: underline">[[report.past.header]]</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>[[report.past.sl_no]]</th>
                                    <th>[[report.past.jd]]</th>
                                    <th>[[report.past.rdmi]]</th>
                                    <th>[[report.past.dn]]</th>
                                    <th>[[report.past.kn]]</th>
                                    <th>[[report.past.dd]]</th>
                                    <th>[[report.past.tsd]]</th>
                                    <th>[[report.past.dr]]</th>
                                </tr>
                                <tr ng-if="past.length<=0">
                                    <td colspan="8">No Information found</td>
                                </tr>
                                <tr ng-if="past.length>0" ng-repeat="p in past">
                                    <td>[[$index+1]]</td>
                                    <td>[[dateConvert(p.joining_date)]]</td>
                                    <td>[[p.old_memorandum_id]] & [[dateConvert(p.reporting_date)]]</td>
                                    <td>[[p.unit_name_bng]]</td>
                                    <td>[[p.kpi_name]]</td>
                                    <td>[[dateConvert(p.release_date)]]</td>
                                    <td>[[getServiceDate(p.release_date,dateConvert(p.joining_date))]]</td>
                                    <td>[[p.reason_in_bng]]</td>

                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop