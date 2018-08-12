@extends('template.master')
@section('title','Generate Salary Sheet')
@section('breadcrumb')
    {!! Breadcrumbs::render('attendance.create') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("AttendanceController", function ($scope, $http, $sce) {
            $scope.vdpList = $sce.trustAsHtml(`
            <p style="font-size: 16px;font-weight: bold;text-align: center;">
                Select guard,month,year to load data
            </p>
            `)

            $scope.param = {}
            $scope.errors = null;
            $scope.allLoading = false;
            $scope.loadData = function () {
                console.log($scope.param)
                $scope.allLoading = true;
                $scope.vdpList = $sce.trustAsHtml('')
                $http({
                    method: 'get',
                    url: "{{URL::route('SD.salary_management.create')}}",
                    params: $scope.param,
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.vdpList = $sce.trustAsHtml(response.data)
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
                        $scope.vdpList = $sce.trustAsHtml(errorView)
                    }
                })
            }
            $scope.sumArray = function (d) {
                if(!d) return 0;
                var v = Object.values(d).map(x=>parseFloat(x))
                return v.reduce(function (t,n) {
                    if(isNaN(t)) t = 0;
                    if(isNaN(n)) n = 0;
                    return t+n;
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
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="">Sheet Type</label>
                            <select class="form-control" ng-model="param.sheetType">
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
                    <div class="col-sm-3" ng-if="param.sheetType=='bonus'">
                        <div class="form-group">
                            <label for="">Bonus For</label>
                            <select class="form-control" ng-model="param.bonusType">
                                <option value="">--Select a type--</option>
                                <option value="eidulfitr">Eid-ul-fitr</option>
                                <option value="eiduladah">Eid-ul-adah</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label style="display: block" for="">&nbsp;</label>
                            <button class="btn btn-primary" ng-click="loadData()"
                                    ng-disabled="!param.range||!param.unit||!param.thana||!param.kpi||!param.month_year||(!param.bonusType&&param.sheetType=='bonus')||!param.sheetType"
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