{{--User: ShreyaS--}}
{{--Date: 1/16/2016--}}
{{--Time: 1:48 PM--}}


@extends('template.master')
@section('title','Withdraw KPI')
@section('breadcrumb')
    {!! Breadcrumbs::render('withdraw_kpi') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#withdraw_date').datePicker(false);
        })
        GlobalApp.controller('KpiWithdrawController', function ($scope, $http) {
            $scope.selectedUnit = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.units = [];
            $scope.thanas = [];
            $scope.kpis = {};
            $scope.loadingKpi = false;
            $scope.loadingUnit = true;
            $scope.loadingThana = false;
            $scope.exist = false;
            $http({
                method: 'get',
                url: '{{URL::to('HRM/DistrictName')}}'
            }).then(function (response) {
                {{--if ('{{Request::old('unit_id')}}') {--}}
                {{--$scope.selectedUnit = '{{Request::old('unit_id')}}'--}}
                {{--}--}}
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
                    $scope.selectedThana = '{{Request::old('thana_id')}}'
                })
            }
            $scope.loadKpi = function (t_id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: t_id}
                }).then(function (response) {
                    $scope.kpis = response.data
                    $scope.selectedKpi = "";
                    $scope.loadingKpi = false;
                    $scope.selectedKpi = "{{Request::old('kpi_id')}}";
                })
            }
            $scope.loadKpiDetail = function (id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_list_for_withdraw')}}',
                    params: {kpi_id: id}
                }).then(function (response) {
                    $scope.kpiDetail = response.data
                    $scope.loadingKpi = false;
                })
            }
            $scope.$watch('selectedUnit', function (n, o) {
                if (n)
                // alert(o)
                    $scope.loadThana(n);

            })
            $scope.$watch('selectedThana', function (n, o) {
                if (n)
                    $scope.loadKpi(n);
            })
            $scope.$watch('selectedKpi', function (n, o) {
                if (n)
                    $scope.loadKpiDetail(n);
            })
        })
    </script>
    <div ng-controller="KpiWithdrawController">
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
                    <a href="#" class="close" data-dismiss="alert"
                       aria-label="close">&times;</a>{{Session::get('error_message')}}
                </div>
            </div>
        @endif
        {!! Form::open(array('route' => 'kpi_withdraw_update', 'id' => 'kpi_withdraw_entry')) !!}
        <section class="content">
            <notify></notify>
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group required"
                                 ng-init="selectedUnit='{{Request::old('unit_id')}}'">
                                <label for="e_unit" class="control-label">Select a Unit&nbsp;
                                    <img ng-show="loadingUnit" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select ng-disabled="loadingUnit" id="e_unit" class="form-control"
                                        ng-model="selectedUnit" name="unit_id">
                                    <option value="">--Select a Unit--</option>
                                    <option ng-repeat="u in units" value="[[u.id]]"
                                            ng-selected="x.id=='{{Request::old('unit_id')}}'">[[u.unit_name_eng]]
                                    </option>
                                </select>
                                @if($errors->has('unit_id'))
                                    <p class="text-danger">{{$errors->first('unit_id')}}</p>
                                @endif
                            </div>
                            <div class="form-group required"
                                 ng-init="selectedThana='{{Request::old('thana_id')}}'">
                                <label for="e_thana" class="control-label">Select a Thana&nbsp;
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select ng-disabled="loadingThana" id="e_thana" class="form-control"
                                        ng-model="selectedThana" name="thana_id">
                                    <option value="">--Select a Thana--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]"
                                            ng-selected="x.id=='{{Request::old('thana_id')}}'">[[t.thana_name_eng]]
                                    </option>
                                </select>
                                @if($errors->has('thana_id'))
                                    <p class="text-danger">{{$errors->first('thana_id')}}</p>
                                @endif
                            </div>
                            <div class="form-group required"
                                 ng-init="selectedKpi='{{Request::old('kpi_id')}}'">
                                <label for="e_kpi" class="control-label">Select a KPI&nbsp;
                                    <img ng-show="loadingKpi" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select ng-disabled="loadingKpi" id="e_kpi" class="form-control"
                                        ng-model="selectedKpi" ng-change="loadKpiDetail(selectedKpi)" name="kpi_id">
                                    <option value="">--Select a KPI--</option>
                                    <option ng-repeat="k in kpis" value="[[k.id]]">[[k.kpi_name]]</option>
                                </select>
                                @if($errors->has('kpi_id'))
                                    <p class="text-danger">{{$errors->first('kpi_id')}}</p>
                                @endif
                            </div>
                            <div class="form-group required" ng-init="withdraw_date='{{Request::old('withdraw_date')}}'">
                                <label for="withdraw_date" class="control-label">Withdraw Date</label>
                                <input type="text" name="withdraw_date" id="withdraw_date" class="form-control"
                                       ng-model="withdraw_date" value="{{Request::old('withdraw_date')}}">
                                @if($errors->has('withdraw_date'))
                                    <p class="text-danger">{{$errors->first('withdraw_date')}}</p>
                                @endif
                            </div>
                            <button id="withdraw-kpi" class="btn btn-primary">
                                Withdraw
                            </button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2"
                             style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                                <img src="{{asset('dist/img/facebook.gif')}}"
                                     width="20">
                            </div>
                            <div ng-if="!kpiDetail.kpi">
                                <input type="hidden" name="kpiExist" value="0">

                                <h3 style="text-align: center">No KPI Information Found</h3>
                            </div>
                            <div ng-if="kpiDetail.kpi">
                                <input type="hidden" name="kpiExist" value="1">

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
                                                    <td>[[kpiDetail.bullet?kpiDetail.bullet:"N/A"]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Activation Date</th>
                                                    <td>[[kpiDetail.a_date]]</td>
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
        $("#withdraw-kpi").confirmDialog({
            message: 'Are you sure to Withdraw this KPI',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#kpi_withdraw_entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
@stop