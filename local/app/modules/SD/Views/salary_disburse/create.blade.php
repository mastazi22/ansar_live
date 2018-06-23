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
                console.log($scope.param)
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: "{{URL::route('SD.attendance.create')}}",
                    params: $scope.param,
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.vdpList = $sce.trustAsHtml(response.data)
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
            $scope.initCalenderDate = function (dates,index) {
                $scope.disabledDates[index] = {present:[],leave:[]};
                $scope.selectedDates[index] = {present:[],leave:[]};
                var d = dates.split(',');
                if(d.length>=0){
                    var da = moment(d[0]);
                    for(var i=1;i<=da.endOf('month').date();i++){
                        var dt = da.date(i).format('YYYY-MM-DD')
                        if(d.indexOf(dt)<0) {
                            $scope.disabledDates[index].present.push(dt)
                            $scope.disabledDates[index].leave.push(dt)
                        }
                    }
                }
            }
        })

        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('vdpList', function (n) {

                        if (attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
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
                            <label for="">Select Day</label>
                            <select class="form-control" ng-model="param.day">
                                <option ng-repeat="(k,v) in dates" value="[[v]]">[[k]]</option>
                            </select>
                        </div>
                    </div>
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
                                    ng-disabled="(!param.range||!param.unit||!param.thana||!param.kpi||!param.month||!param.year)&&(!param.ansar_id||!param.month||!param.year)"
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

                <div ng-bind-html="vdpList" compile-html>

                </div>
            </div>
        </div>
    </section>
@endsection