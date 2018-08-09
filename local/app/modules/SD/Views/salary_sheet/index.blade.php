@extends('template.master')
@section('title','Salary Management')
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
            <div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total()</span>
        </caption>

        <tr>
            <th>#</th>
            <th>KPI name</th>
            <th>KPI Division</th>
            <th>KPI District</th>
            <th>KPI thana</th>
            <th>Generated Date</th>
            <th>Generate For Month</th>
            <th>Type</th>
            <th>Disburse status</th>

        </tr>

        <tr>
                    <td colspan="9" class="bg-warning">
                        No Payment History Available
                    </td>
                </tr>

                </table>
            </div>
`)

            $scope.param = {}
            $scope.allLoading = false;
            $scope.loadData = function () {
                console.log($scope.param)
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: "{{URL::route('SD.salary_management.index')}}",
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
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-header">
                <filter-template
                        show-item="['range','unit','thana','kpi']"
                        type="all"
                        range-change="loadData()"
                        unit-change="loadData()"
                        thana-change="loadData()"
                        data="param"
                        start-load="range"
                        on-load="loadData()"
                        field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                >

                </filter-template>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="">Select Month</label>
                            <input typed-date-picker="" calender-type="month" type="text" class="form-control" placeholder="Select month & year"
                                   ng-model="param.month_year">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label style="display: block" for="">&nbsp;</label>
                            <button class="btn btn-primary" ng-click="loadData()"
                            >
                                <i class="fa fa-download"></i>&nbsp; Load data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">

                <div ng-bind-html="vdpList" compile-html>

                </div>
            </div>
        </div>
    </section>
@endsection