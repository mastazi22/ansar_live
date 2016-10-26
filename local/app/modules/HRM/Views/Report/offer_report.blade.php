@extends('template.master')
@section('title','Offer Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('offer_report') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http, $sce) {
            $scope.isDc = parseInt('{{Auth::user()->type}}') == 22 ? true : false
            $scope.districts = [];
            $scope.unit = {
                selectedDistrict: "",
                custom: "",
                type:"1"
            };
            $scope.ansars = [];
            $scope.onr = [];
            $scope.or = [];
            $scope.orj = [];
            $scope.loadingUnit = false;
            $scope.report = {};
            $scope.selectedDate = "2"
            $scope.reportType = 'eng';
            $scope.errorFind = 0;
            $scope.allLoading = false;
            if ($scope.isDc) {
                $scope.unit.selectedDistrict = parseInt('{{Auth::user()->district_id}}')
            }
            $scope.loadDistrict = function () {
                $scope.loadingUnit = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.loadingUnit = false;
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
            $scope.loadAnsar = function () {
                $scope.allLoading = true;
                var data = {};
                if ($scope.selectedDate == 0) {
                    data = {
                        unit: $scope.unit.selectedDistrict,
                        report_past: isNaN(parseInt($scope.unit.custom)) ? 0 : $scope.unit.custom,
                        type: $scope.unit.type
                    }
                }
                else {
                    data = {
                        unit: $scope.unit.selectedDistrict,
                        report_past: $scope.selectedDate,
                        type: 0
                    }
                }
                $http({
                    method: 'get',
                    url: '{{URL::route('get_offered_ansar')}}',
                    params: data
                }).then(function (response) {
                    $scope.errorFind = 0;
                    $scope.onr = response.data.onr
                    $scope.or = response.data.or
                    $scope.orj = response.data.orj
                    $scope.allLoading = false;
                },function(response){
                    $scope.errorFind = 1;
                    $scope.onr = []
                    $scope.or = []
                    $scope.orj = []
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    $scope.allLoading = false;
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
            $scope.dateConvert = function (date) {
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.loadReportData("ansar_in_guard_report", "eng")
            if (!$scope.isDc) {
                $scope.loadDistrict()
            }
            else {
                $scope.loadThana($scope.selectedDistrict)
            }

        })
        $(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    var v = $(this).text();
                    $(this).parents('td').append('<span>' + v + '</span>')
                    $(this).css('display', 'none')
                })
                $('body').append('<div id="print-area">' + $("#print-guard-in-ansar-report").html() + '</div>')
                window.print();
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    $(this).parents('td').children('span').remove()
                    $(this).css('display', 'block')
                })
                $("#print-area").remove()
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
                        <div class="col-sm-4" ng-if="!isDc">
                            <div class="form-group">
                                <label class="control-label">
                                    @lang('title.unit')&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingUnit">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit"
                                        ng-model="unit.selectedDistrict">
                                    <option value="">--@lang('title.unit')--</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Select an Option&nbsp;&nbsp;
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit"
                                        ng-model="selectedDate">
                                    <option value="2">Past 2 days</option>
                                    <option value="3">Past 3 days</option>
                                    <option value="5">Past 5 days</option>
                                    <option value="7">Past 7 days</option>
                                    <option value="0">Custom</option>
                                </select>
                            </div>
                            <div class="form-group row" ng-if="selectedDate==0">
                                <div class="col-xs-7">
                                    <input type="text" class="form-control" ng-model="unit.custom"
                                           placeholder="No of day,month or year">
                                </div>
                                <div class="col-xs-5" style="padding-left: 0;">
                                    <select class="form-control" ng-model="unit.type">
                                        <option value="1">Days</option>
                                        <option value="2">Months</option>
                                        <option value="3">Years</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="control-label">&nbsp;</label>

                            <div class="form-control" style="padding: 0;border:none;">
                                <button class="btn btn-primary" ng-click="loadAnsar()"><i
                                            class="fa fa-download"></i>&nbsp;View Offer Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="print-guard-in-ansar-report" style="margin-top: 10px">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a data-toggle="tab" href="#offer_not_respond">Offer Not Respond</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#offer_send">Offer Accepted</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#offer_reject">Offer Reject</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="offer_not_respond" class="tab-pane active">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>SL. No</th>
                                                <th>Ansar ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Offered Date</th>
                                            </tr>
                                            <tr ng-if="onr.length<=0&&errorFind==0">
                                                <th class="warning" colspan="5">No Ansar Found</th>
                                            </tr>
                                            <tbody ng-if="errorFind==1&&onr.length<=0" ng-bind-html="errorMessage"></tbody>
                                            <tr ng-if="onr.length>0&&errorFind==0" ng-repeat="a in onr">
                                                <td>[[$index+1]]</td>
                                                <td>[[a.ansar_id]]</td>
                                                <td>[[a.ansar_name_eng]]</td>
                                                <td>[[a.code]]</td>
                                                <td>[[a.sms_send_datetime]]</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div id="offer_send" class="tab-pane">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>SL. No</th>
                                                <th>Ansar Id</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Offer Accepted Date</th>
                                            </tr>
                                            <tr ng-if="or.length<=0&&errorFind==0">
                                                <th class="warning" colspan="5">No Ansar Found</th>
                                            </tr>
                                            <tbody ng-if="errorFind==1&&or.length<=0" ng-bind-html="errorMessage"></tbody>
                                            <tr ng-if="or.length>0&&errorFind==0" ng-repeat="a in or">
                                                <td>[[$index+1]]</td>
                                                <td>[[a.ansar_id]]</td>
                                                <td>[[a.ansar_name_eng]]</td>
                                                <td>[[a.code]]</td>
                                                <td>[[a.sms_received_datetime]]</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div id="offer_reject" class="tab-pane">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>SL. No</th>
                                                <th>Ansar Id</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Reject Date</th>
                                            </tr>
                                            <tr ng-if="orj.length<=0&&errorFind==0">
                                                <th class="warning" colspan="5">No Ansar Found</th>
                                            </tr>
                                            <tbody ng-if="errorFind==1&&orj.length<=0" ng-bind-html="errorMessage"></tbody>
                                            <tr ng-if="orj.length>0&&errorFind==0" ng-repeat="a in orj">
                                                <td>[[$index+1]]</td>
                                                <td>[[a.ansar_id]]</td>
                                                <td>[[a.ansar_name_eng]]</td>
                                                <td>[[a.code]]</td>
                                                <td>[[a.reject_date]]</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@stop