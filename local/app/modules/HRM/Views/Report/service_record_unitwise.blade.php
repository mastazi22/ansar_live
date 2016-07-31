{{--User: Shreya--}}
{{--Date: 1/02/2015--}}
{{--Time: 10:00 AM--}}

@extends('template.master')
@section('title','Ansar Service Record Unit Wise')
@section('breadcrumb')
    {!! Breadcrumbs::render('service_record_unitwise_view') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http, $sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.districts = [];
            $scope.thanas = [];
            $scope.selectedDistrict = "";
            $scope.selectedThana = "";
            $scope.ansars = [];
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.report = {};
            $scope.reportType = 'eng';
            $scope.errorFound=0;
            $scope.allLoading = false;
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}')
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
                    $scope.selectedThana = "all";
                    $scope.loadingThana = false;
                    $scope.loadAnsarDetail();
                })
            }
            $scope.loadAnsarDetail = function () {
                //console.log(id)
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('service_record_unitwise_info')}}',
                    params: {
                        unit:$scope.selectedDistrict,
                        thana:$scope.selectedThana,
                    }
                }).then(function (response) {
                    $scope.errorFound=0;
                    $scope.ansarDetail = response.data
                    $scope.allLoading = false;
                },function(response){
                    $scope.errorFound=1;
                    $scope.ansarDetail = $sce.trustAsHtml("<h5 class='text-danger' style='text-align: center;'>"+response.data+"</h5>");
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
                    $scope.report = response.data;
                    $scope.allLoading = false;
                })
            }
            $scope.dateConvert=function(date){
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.loadReportData("service_record_unitwise", "eng")
            if ($scope.isAdmin == 11) {
                $scope.loadDistrict()
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.loadThana($scope.dcDistrict)
                }
            }
        })
        $(function () {
            $('body').on('click', '#print-report', function (e) {
                e.preventDefault();
                var h = '';
                $(".print-service_record_unitwise").each(function(){
                    $(this).children('table').children('caption').css('display','table-caption')
                    $(this).children('table').children('tbody').children('tr').children('td').children('a').each(function () {
                        $(this).parents('td').append('<span>'+$(this).text()+'</span>')
                    })
                    $(this).children('table').children('tbody').children('tr').children('td').children('a').css('display','none')

                    h += $(this).html()
                })
                $('body').append('<div id="print-area" class="letter">' + h + '</div>')
                window.print();
                $("#print-area").remove()
                $(".print-service_record_unitwise").each(function(){
                    $(this).children('table').children('caption').css('display','none')
                    $(this).children('table').children('tbody').children('tr').children('td').children('a').css('display','block')
                    $(this).children('table').children('tbody').children('tr').children('td').children('span').remove()
                })
                h=''
            })
            $('body').on('click','.max-min-button',function(){
                $(this).siblings('.drop-down-view').slideToggle(500)
                $(this).children('span').children('i').toggleClass('fa-plus fa-minus')
                $('.max-min-button').not(this).children('span').children('i').addClass('fa-plus').removeClass('fa-minus')
                !$('.drop-down-view').not($(this).siblings('.drop-down-view')).slideUp(500)

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
                                                                       ng-change="loadReportData('service_record_unitwise',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio"
                                             ng-change="loadReportData('service_record_unitwise',reportType)"
                                             class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                    </div><br>
                    <div class="row">
                        <div class="col-sm-4" ng-show="isAdmin==11">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a District&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingUnit">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selectedDistrict"
                                        ng-change="loadThana(selectedDistrict)">
                                    <option value="">--Select a unit--</option>
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
                                        ng-change="loadAnsarDetail(selectedThana)">
                                    <option value="">--Select a thana--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" id="print-service_record_unitwise">
                            <h3 style="text-align: center">[[report.header]]&nbsp;<a href="#" id="print-report"><span
                                            class="glyphicon glyphicon-print"></span></a></h3>

                            <div ng-show="ansarDetail.length==0">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="errorFound==1" ng-bind-html="ansarDetail"></div>
                            <div style="overflow: hidden" ng-show="ansarDetail.length>0"
                                 ng-repeat="(key,value) in ansarDetail|groupBy:'kpi'">
                                <div class="max-min-button">
                                    [[key]] &nbsp;<span><i class="fa" ng-class="{'fa-minus':$index==0,'fa-plus':$index>0}"></i></span>
                                </div>
                                <div class="table-responsive drop-down-view print-service_record_unitwise" ng-class="{'invisible-panel':$index>0}" >
                                    <table class="table table-bordered">
                                        <caption style="display: none">[[key]]</caption>
                                        <tr>
                                            <th>[[report.ansar.id]]</th>
                                            <th>[[report.ansar.rank]]</th>
                                            <th>[[report.ansar.name]]</th>
                                            <th>[[report.ansar.kpi_name]]</th>
                                            <th>[[report.ansar.district]]</th>
                                            <th>[[report.ansar.reporting_date]]</th>
                                            <th>[[report.ansar.joining_date]]</th>
                                            <th>[[report.ansar.service_ended_date]]</th>
                                        </tr>
                                        {{--<tr ng-show="ansars.length==0">--}}
                                        {{--<td colspan="10" class="warning no-ansar">--}}
                                        {{--No ansar is available to see--}}
                                        {{--</td>--}}
                                        {{--</tr>--}}
                                        <tr ng-repeat="a in value">
                                            <td>
                                                <a href="{{URL::to('/entryreport')}}/[[a.id]]">[[a.id]]</a>
                                            </td>
                                            <td>
                                                [[a.rank]]
                                            </td>
                                            <td>
                                                [[a.name]]
                                            </td>
                                            <td>
                                                [[a.kpi]]
                                            </td>
                                            <td>
                                                [[a.unit]]
                                            </td>
                                            <td>
                                                [[a.r_date]]
                                            </td>
                                            <td>
                                                [[dateConvert(a.j_date)]]
                                            </td>
                                            {{--<td>--}}
                                            {{--[[a.reason_in_bng]]--}}
                                            {{--</td>--}}
                                            <td>
                                                [[dateConvert(a.se_date)]]
                                            </td>
                                            {{--<td>--}}
                                            {{--[[calculate(a.total_service_days)]]--}}
                                            {{--</td>--}}
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop