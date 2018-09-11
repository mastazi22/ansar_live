@extends('template.master')
@section('title','Take Attendance')
@section('breadcrumb')
    {!! Breadcrumbs::render('attendance.create') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("AttendanceController", function ($scope, $http, $sce) {
            var currentYear = $scope.currentMonth = parseInt(moment().format('YYYY'));
            var currentMonth = $scope.currentYear = parseInt(moment().format('M'));
            $scope.selectedDates = [];
            $scope.calenderDatesDates = [];
            $scope.disabledDates = [];
            $scope.dates = [];
            $scope.vdpList = $sce.trustAsHtml(`
            <p style="font-size: 16px;font-weight: bold;text-align: center;">
                Select guard,month,year to load data
            </p>
            `)

            $scope.param = {}
            $scope.months = {
                "--Select a month--": '',
                January: "01",
                February: "02",
                March: "03",
                April: "04",
                May: "05",
                June: "06",
                July: "07",
                Augest: "08",
                September: "09",
                October: "10",
                November: "11",
                December: "12"
            }

            $scope.years = {"--Select a year--": ''};
            $scope.dates = {"--Select a day--": ''};
            for (var i = currentYear - 5; i <= currentYear; i++) {
                $scope.years[i] = i;
            }
            for (var i = 1; i <= 31; i++) {
                $scope.dates[i] = i;
            }
            $scope.allLoading = false;
            $scope.loadData = function () {
                $scope.selectedDates = [];
                $scope.calenderDatesDates = [];
                $scope.attData = '';
                console.log($scope.param)
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: "{{URL::route('SD.attendance.create')}}",
                    params: $scope.param,
                }).then(function (response) {
                    $scope.allLoading = false;
//                    $scope.vdpList = $sce.trustAsHtml(response.data)
                    $scope.attData = response.data
                    console.log(response.data)
                }, function (response) {
                    $scope.allLoading = false;
                    console.log(response.data)
                })
            }
            $scope.init = function () {
//                alert(1)
                $scope.param.month = currentMonth;
                $scope.param.year = currentYear;
                console.log($scope.param)
            }
            $scope.initCalenderDate = function (dates, index) {
//                console.log(dates[0])
//                $scope.disabledDates[index] = {present:[],leave:[]};
                $scope.selectedDates[index] = {present: [], leave: []};
                $scope.dates[index] = [];
                for (var i = 0; i < dates.length; i++) {
                    $scope.dates[index].push({
                        day: dates[i].day,
                        month: dates[i].month - 1,
                        year: dates[i].year,
                    })
                }
            }
            $scope.parseDate = function (y, m, d) {
                console.log(y + " " + m + " " + d)
                return moment({year: parseInt(y), month: parseInt(m) - 1, date: d}).format("MMMM, YYYY")
            }
            $scope.isAvailable = function () {
                return $scope.attData&&Object.keys($scope.attData.data.attendance).length>0;
            }

        })

        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('vdpList', function (n) {

                        if (attr.ngBindHtml) {
                            if (newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
    </script>
    <section class="content" ng-controller="AttendanceController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <div class="box box-solid">
            <div class="box-header">
                <filter-template
                        show-item="['range','unit','thana','kpi']"
                        type="single"
                        range-change="loadPage()"
                        unit-change="loadPage()"
                        thana-change="loadPage()"
                        data="param"
                        start-load="range"
                        on-load="loadPage()"
                        field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                >

                </filter-template>
                <div class="row" ng-init="init()">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Select Month</label>
                            <select class="form-control" ng-model="param.month">
                                <option ng-repeat="(k,v) in months" value="[[v]]">[[k]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Select Year</label>
                            <select class="form-control" ng-model="param.year">
                                <option ng-repeat="(k,v) in years" value="[[v]]">[[k]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="">Search by Ansar ID</label>
                            <input type="text" class="form-control" placeholder="Search by Ansar ID"
                                   ng-model="param.ansar_id">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label style="display: block" for="">&nbsp;</label>
                            <button class="btn btn-primary" ng-click="loadData()"
                                    ng-disabled="!param.range||!param.unit||!param.thana||!param.kpi||!param.month||!param.year"
                            >
                                <i class="fa fa-download"></i>&nbsp; Load data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="container-fluid" ng-if="attData">
                    {!! Form::open(['route'=>'SD.attendance.store']) !!}
                    {{--<input type="hidden" name="type" value="[[attData.type]]">--}}
                    <input type="hidden" name="month" value="[[attData.date.month]]">
                    <input type="hidden" name="year" value="[[attData.date.year]]">
                    <input type="hidden" name="kpi_id" value="[[attData.data.id]]">
                    <div style="padding: 0 10px;margin-bottom: 20px">
                        <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                            Attendance of "[[attData.data.kpi_name]]"
                            <br>[[parseDate(attData.date["year"],attData.date["month"],1)]]
                        </h4>
                    </div>
                    <div style="padding: 0 10px" [[attData.data.attendance.length]] ng-if="isAvailable()">
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default" ng-repeat="(k,att) in attData.data.attendance"
                                 ng-init="i=$index">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#kpi_[[k]]">
                                            <strong>ID:[[att.details.ansar_id]]</strong><br>
                                            <strong>Name:[[att.details.ansar_name_bng]]</strong><br>
                                            <strong>Designation:[[att.details.designation.name_bng]]</strong>
                                        </a>
                                    </h4>
                                </div>
                                <div id="kpi_[[k]]" class="panel-collapse collapse" ng-class="{'in':$index==0}">
                                    <div class="panel-body">
                                        <p class="text-danger text-bold text-center">
                                            Please select date for PRESENT or LEAVE. For ABSENT don`t select any date
                                        </p>
                                        <div class="row" ng-repeat="(kk,v) in att.data" ng-init="initCalenderDate(v,i)">
                                            <div class="col-sm-6" >
                                                <h4>Present Dates</h4>
                                                <calender enabled-dates="dates[i]" disabled-dates="selectedDates[i].leave"  selected-dates="selectedDates[i].present" show-only-current-year="true" show-only-month="[[kk-1]]"></calender>
                                            </div>
                                            <div class="col-sm-6">
                                                <h4>Leave Dates</h4>
                                                <calender enabled-dates="dates[i]" disabled-dates="selectedDates[i].present"  selected-dates="selectedDates[i].leave" show-only-current-year="true" show-only-month="[[kk-1]]"></calender>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="[[ 'attendance_data['+i+'][ansar_id]' ]]"value="[[att.details.ansar_id]]">
                                    <div style="display: none" ng-repeat="selectedDate in selectedDates">
                                        <input type="hidden" ng-repeat="d in selectedDate.present" name="[['attendance_data['+$parent.$index+'][present_dates]['+$index+'][day]' ]]" value="[[d.day]]">
                                        <input type="hidden" ng-repeat="d in selectedDate.present" name="[['attendance_data['+$parent.$index+'][present_dates]['+$index+'][month]' ]]" value="[[d.month]]">
                                        <input type="hidden" ng-repeat="d in selectedDate.present" name="[['attendance_data['+$parent.$index+'][present_dates]['+$index+'][year]' ]]" value="[[d.year]]">

                                        <input type="hidden" ng-repeat="d in selectedDate.leave" name="[['attendance_data['+$parent.$index+'][leave_dates]['+$index+'][day]' ]]" value="[[d.day]]">
                                        <input type="hidden" ng-repeat="d in selectedDate.leave" name="[['attendance_data['+$parent.$index+'][leave_dates]['+$index+'][month]' ]]" value="[[d.month]]">
                                        <input type="hidden" ng-repeat="d in selectedDate.leave" name="[['attendance_data['+$parent.$index+'][leave_dates]['+$index+'][year]' ]]" value="[[d.year]]">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">
                            Submit Attendance
                        </button>
                    </div>
                    <div style="padding: 10px" class="bg-danger"  ng-if="!isAvailable()">
                        <strong>Attendance already taken or not generated for this month</strong>
                    </div>
                    {!! Form::close() !!}
                </div>
                {{--<div ng-bind-html="vdpList" compile-html>

                </div>--}}
            </div>
        </div>
    </section>
@endsection