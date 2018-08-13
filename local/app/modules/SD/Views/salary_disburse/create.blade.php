@extends('template.master')
@section('title','Disburse Salary')
@section('breadcrumb')
    {!! Breadcrumbs::render('attendance.create') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("AttendanceController", function ($scope, $http, $sce) {
            $scope.sheetList = $sce.trustAsHtml(`
            <table class="table table-bordered table-condensed">
                <caption>
                    <span style="font-size: 20px;">Total(0)</span>
                </caption>

                <tr>
                    <th>#</th>
                    <th>KPI name</th>
                    <th>KPI Division</th>
                    <th>KPI District</th>
                    <th>KPI thana</th>
                    <th>Generate For Month</th>
                    <th>Total Ansar</th>
                    <th>Amount need to pay</th>
                    <th>Deposit status</th>
                    <th>Action</th>

                </tr>
                <tr>
                     <td colspan="10" class="bg-warning">
                            No Salary/Bonus sheet available
                     </td>
                </tr>
            </table>
`)
            $scope.param = {}
            $scope.errors = null;
            $scope.allLoading = false;
            $scope.loadData = function () {
                console.log($scope.param)
                $scope.allLoading = true;
//                $scope.sheetList = $sce.trustAsHtml('')
                $http({
                    method: 'get',
                    url: "{{URL::route('SD.salary_disburse.create')}}",
                    params: $scope.param,
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.sheetList = $sce.trustAsHtml(response.data)
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
                        $scope.sheetList = $sce.trustAsHtml(errorView)
                    }
                })
            }
            $scope.viewDetails = function (url) {
                $("#details_view").modal('show')
                $scope.detailsView = $sce.trustAsHtml(`
                    <div style="min-height: 200px;display: flex;justify-content: center;align-items: center">
                        <i class="fa fa-spinner fa-pulse fa-4x"></i>
                    </div>

                `)
                $http({
                    method: 'get',
                    url: url,
                }).then(function (response) {
//                    $scope.allLoading = false;
                    $scope.detailsView = $sce.trustAsHtml(response.data)
                }, function (response) {
//                    $scope.allLoading = false;
                })
            }
            $scope.beforeSubmit = function(){
//                alert(1);
                $("#details_view").modal('hide')
            }
            $scope.afterSubmit = function(response){
                console.log(response)
                window.location.href = response.url;
                $scope.loadData();
            }
        })

        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('sheetList', function (n) {

                        if (attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
        GlobalApp.directive('compileHtml1',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('detailsView', function (n) {

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


                <div ng-bind-html="sheetList" compile-html>

                </div>
            </div>
        </div>
            <div class="modal" id="details_view" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">
                                Salary Disburse Details
                            </h4>
                        </div>
                        <div class="modal-body" ng-bind-html="detailsView" compile-html1>

                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection