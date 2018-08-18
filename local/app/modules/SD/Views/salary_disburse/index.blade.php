@extends('template.master')
@section('title','Disburse Salary History')
@section('breadcrumb')
    {!! Breadcrumbs::render('attendance.create') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("SalaryDisburseController", function ($scope, $http, $sce) {
            $scope.disburseHistory = $sce.trustAsHtml(`
            <table class="table table-bordered table-condensed">
                <caption>
                    <span style="font-size: 20px;">Total(0)</span>
                </caption>

                <tr>
                    <th>#</th>
                    <th>KPI name</th>
                    <th>KPI Address</th>
                    <th>Disburse Type</th>
                    <th>Disburse For Month</th>
                    <th>Total Ansar</th>
                    <th>Total Salary</th>
                    <th>Total AVUB Share</th>
                    <th>Total Welfare</th>
                    <th>Total Regimental</th>
                    <th>Total Stamp</th>
                    <th>Total 15%/20%</th>
                    <!--<th>Action</th>-->

                </tr>
                <tr>
                     <td colspan="12" class="bg-warning">
                            No disburse history available
                     </td>
                </tr>
            </table>
`)
            $scope.param = {}
            $scope.errors = null;
            $scope.allLoading = false;
            $scope.query = {
                string:''
            };
            $scope.loadData = function () {
                console.log($scope.param)
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: "{{URL::route('SD.salary_disburse.index')}}",
                    params: $scope.param,
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.disburseHistory = $sce.trustAsHtml(response.data)
                    console.log(response.data)
                }, function (response) {
                    $scope.allLoading = false;
                    console.log(response.data)
                    if(response.status==422){
                        $scope.errors = response.data;
                        var errorView = `
                            <div class="container-fluid">
                                <fieldset>
                                    <legend>Error Encounter</legend>
                                    <p class="label label-danger" style="font-size: 14px;display: block;text-align: left" ng-repeat="(key,error) in errors">
                                        [[error[0] ]]
                                    </p>
                                </fieldset>
                            </div>
                        `;
                        $scope.disburseHistory = $sce.trustAsHtml(errorView)
                    }
                })
            }
            $scope.appendParam = function () {
                alert(1)
                var queryString = ""
                Object.keys($scope.param).forEach(function (key) {
                    queryString += "&"+key+"="+$scope.param[key]
                })
                return queryString
            }
            $scope.$watch('param',function (n,o) {
                var queryString = ""
                Object.keys($scope.param).forEach(function (key) {
                    queryString += "&"+key+"="+$scope.param[key]
                })
                $scope.query.string = queryString;
            },true)
        })

        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('disburseHistory', function (n) {

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
    <section class="content" ng-controller="SalaryDisburseController">
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
            <div class="overlay" ng-if="allLoading||param.loading">
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
                            <label for="">Disburse Type</label>
                            <select class="form-control" ng-model="param.disburseType">
                                <option value="">--Select a type--</option>
                                <option value="salary">Salary</option>
                                <option value="bonus">Bonus</option>
                            </select>
                        </div>
                    </div>
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
                            <button class="btn btn-primary" ng-click="loadData()">
                                <i class="fa fa-download"></i>&nbsp; Load data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">


                <div ng-bind-html="disburseHistory" compile-html>

                </div>
            </div>
        </div>
    </section>
@endsection