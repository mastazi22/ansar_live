{{--User: ShreyaS--}}
{{--Date: 3/16/2016--}}
{{--Time: 12:05 PM--}}


@extends('template.master')
@section('title','Kpi Withdraw Cancel')
@section('breadcrumb')
    {!! Breadcrumbs::render('kpi_withdraw_cancel') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('KpiWithdrawCancelController', function ($scope, $http) {
            $scope.selectedUnit = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.units = [];
            $scope.thanas = [];
            $scope.kpis = {};
            $scope.loadingKpi = false;
            $scope.loadingUnit = true;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.exist = false;
            $http({
                method: 'get',
                url: '{{URL::to('HRM/DistrictName')}}'
            }).then(function (response) {
                $scope.units = response.data
                $scope.loadingUnit = false;
            })
            $scope.loadThana = function (d_id) {
                $scope.loadingThana = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: d_id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "";
                    $scope.loadingThana = false;
                })
            }
            $scope.loadKpi = function (t_id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('withdrawn_kpi_name')}}',
                    params: {id: t_id}
                }).then(function (response) {
                    $scope.kpis = response.data
                    $scope.selectedKpi = "";
                    $scope.loadingKpi = false;
                })
            }
            $scope.loadKpiDetail = function (id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_list_for_withdraw_cancel')}}',
                    params: {kpi_id: id}
                }).then(function (response) {
                    $scope.kpiDetail = response.data
//                    alert(Object.keys($scope.kpiDetail).length)
                    $scope.loadingKpi = false;
                   // console.log($scope.kpiDetail)
                })
            }
            $scope.isEmpty = function (object) {
                if(!object) return true;
                return Object.keys(object).length==0
            }
        })
    </script>
    <div ng-controller="KpiWithdrawCancelController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('withdraw_kpi') !!}--}}
        {{--</div>--}}
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
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        {!! Form::open(array('route' => 'kpi-withdraw-cancel-update', 'id' => 'kpi-withdraw-cancel-entry')) !!}
        <section class="content">
            <notify></notify>
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="e_unit" class="control-label">Select a unit&nbsp;
                                    <img ng-show="loadingUnit" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select ng-disabled="loadingUnit" id="e_unit" class="form-control"
                                        ng-model="selectedUnit" ng-change="loadThana(selectedUnit)">
                                    <option value="">--Select a unit--</option>
                                    <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_eng]]</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="e_thana" class="control-label">Select a thana&nbsp;
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select ng-disabled="loadingThana" id="e_thana" class="form-control"
                                        ng-model="selectedThana" ng-change="loadKpi(selectedThana)">
                                    <option value="">--Select a thana--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_eng]]
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="e_kpi" class="control-label">Select a KPI&nbsp;
                                    <img ng-show="loadingKpi" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select ng-disabled="loadingKpi" id="e_kpi" class="form-control"
                                        ng-model="selectedKpi" ng-change="loadKpiDetail(selectedKpi)" name="kpi_id">
                                    <option value="">--Select a KPI--</option>
                                    <option ng-repeat="k in kpis" value="[[k.id]]">[[k.kpi_name]]</option>
                                </select>
                            </div>
                            <button id="cancel-withdraw-kpi" class="btn btn-primary"
                                    ng-disabled="isEmpty(kpiDetail)">
                                Cancel Withdraw
                            </button>
                        </div>
                        <div class="col-sm-8"
                             style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="isEmpty(kpiDetail)">
                                <h3 style="text-align: center">No KPI Information Found</h3>
                            </div>
                            <div ng-if="!isEmpty(kpiDetail)">
                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-2">
                                        <h3 style="text-align: center">KPI Information</h3>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>KPI Name</th>
                                                    <td>[[kpiDetail.kpi]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Division</th>
                                                    <td>[[kpiDetail.division]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Unit</th>
                                                    <td>[[kpiDetail.unit]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Thana</th>
                                                    <td>[[kpiDetail.thana]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Ansar Request</th>
                                                    <td>[[kpiDetail.tar]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Ansar Given</th>
                                                    <td>[[kpiDetail.tag]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Weapon Number</th>
                                                    <td>[[kpiDetail.weapon]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Number of Bullets</th>
                                                    <td>[[kpiDetail.bullet]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Activation Date</th>
                                                    <td>[[kpiDetail.a_date]]</td>
                                                </tr>
                                                <tr>
                                                    <th>KPI Withdraw Date</th>
                                                    <td>[[kpiDetail.w_date]]</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {!! Form::close() !!}
    </div>
    <script>
        $("#cancel-withdraw-kpi").confirmDialog({
            message:'Are you sure to Cancel the Withdrawal of this KPI',
            ok_button_text:'Confirm',
            cancel_button_text:'Cancel',
            ok_callback: function (element) {
                $("#kpi-withdraw-cancel-entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
@stop