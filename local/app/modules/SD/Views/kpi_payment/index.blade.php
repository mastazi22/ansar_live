@extends('template.master')
@section('title','Salary Management')
@section('breadcrumb')
    {!! Breadcrumbs::render('attendance.create') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("PaymentHistoryController", function ($scope, $http, $sce) {
            var view = `<div class="table-responsive">
<table class="table table-bordered table-condensed">
<caption><span style="font-size: 20px;">Total Payment()</span>
            <a href="{{URL::route('SD.kpi_payment.create')}}" class="btn btn-primary btn-xs pull-right">
                <i class="fa fa-plus"></i>&nbsp;Add new payment
            </a>
        </caption>

        <tr>
            <th>#</th>
            <th>KPI name</th>
            <th>Demand Sheet No.</th>
            <th>KPI Division</th>
            <th>KPI District</th>
            <th>KPI thana</th>
            <th>Paid Amount</th>
            <th>Uploaded date</th>
            <th>Document</th>
            <th>Action</th>

        </tr>
        <tr>
                <td colspan="10" class="bg-warning">
                    No Payment History Available
                </td>
            </tr>
</table>
</div>`
            $scope.paymentHistory = $sce.trustAsHtml(view)

            $scope.param = {}
            $scope.allLoading = false;
            $scope.loadPage = function (url) {
                console.log($scope.param)
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: url||"{{URL::route('SD.kpi_payment.index')}}",
                    params: $scope.param,
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.paymentHistory = $sce.trustAsHtml(response.data)
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
                    scope.$watch('paymentHistory', function (n) {

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
    <section class="content" ng-controller="PaymentHistoryController">
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
                            <label for="">Payment against</label>
                            <select class="form-control" ng-model="param.payment_against">
                                <option value="">--Select a type--</option>
                                <option value="demand_sheet">Demand Sheet</option>
                                <option value="salary_sheet">Salary Sheet</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3" ng-if="param.payment_against=='salary_sheet'">
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
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label style="display: block" for="">&nbsp;</label>
                            <button class="btn btn-primary" ng-click="loadPage()">
                                <i class="fa fa-download"></i>&nbsp; Load data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">


                <div ng-bind-html="paymentHistory" compile-html>

                </div>
            </div>
        </div>
        <div class="backdrop hidden">
            <div style="width: 500px;position: relative" id="img-con">
                <img class="img-responsive" src="" alt="">
                <div class="button_panel">
                    <div class="btn-group">
                        <a href="#"  class="zoom-in btn btn-primary">
                            <i class="fa fa-search-plus fa-2x"></i>
                        </a>
                        <a href="#" class="zoom-out btn btn-primary ">
                            <i class="fa fa-search-minus fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .backdrop{
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: #1111117d;
            z-index: 10000;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
        }
        .button_panel{
            text-align: center;
            overflow: hidden;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            transition: all .5s;
        }
        .button_panel a{
            color: #ffffff;
            background: rgba(204, 204, 204, 0.41) !important;
        }
    </style>
    <script>
        $(document).ready(function () {
            var zoomIn = 1;
            $(".zoom-in,.zoom-out").on('click',function (e) {
                e.stopPropagation();
                e.preventDefault();
                if($(this).hasClass("zoom-in")){
                    zoomIn+=.5
                    $(this).parents("#img-con").find('img').css({
                        transform:"scale("+zoomIn+","+zoomIn+")",
                        transition:'all .5s'
                    })
                }
                if($(this).hasClass("zoom-out")){
                    zoomIn-=.5
                    if(zoomIn<1) zoomIn = 1;
                    $(this).parents("#img-con").find('img').css({
                        transform:"scale("+zoomIn+","+zoomIn+")",
                        transition:'all .5s'
                    })
                }
            })
        })
    </script>

@endsection