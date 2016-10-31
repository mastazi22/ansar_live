{{--User: Shreya--}}
{{--Date: 1/5/2015--}}
{{--Time: 12:49 PM--}}

@extends('template.master')
@section('title','Reduce Ansar In Guard Strength')
@section('breadcrumb')
    {!! Breadcrumbs::render('reduce_guard_strength') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#reduce_guard_strength_date').datePicker(true);
        })
        GlobalApp.controller('AnsarReduceController', function ($scope, $http, notificationService, $filter) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.districts = [];
            $scope.thanas = [];
            $scope.freezeData = {}
            $scope.transData = {}
            $scope.selected = {
                unit: '',
                thana: '',
                kpi: ''
            }
            $scope.checked = []
            $scope.checkedAll = false
            $scope.trans = {
                unit: '',
                thana: '',
                kpi: '',
                open: false
            }
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.memorandumId = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.allLoading = false;
            var f = "Freeze Ansar for Guard's Strength Reduction"

//
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.verifyMemorandumId = function (id) {
                var data = {
                    memorandum_id: id
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{URL::to('verify_memorandum_id')}}', data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }
            $scope.loadDistrict = function () {
                $scope.loadingUnit = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.ddistricts = response.data;
                    $scope.loadingUnit = false;
                    if (!$scope.trans.open) {
                        $scope.thanas = [];
                    }
                    else {
                        $scope.tthanas = [];
                    }
                    $scope.selected.thana = "";
                })
            }
            $scope.loadThana = function (id) {
                $scope.loadingThana = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: id}
                }).then(function (response) {
                    if (!$scope.trans.open) {
                        $scope.thanas = response.data;
                        $scope.selected.thana = "";
                        $scope.guards = [];
                        $scope.selected.kpi = "";
                    }
                    else {
                        $scope.tthanas = response.data;
                        $scope.trans.thana = "";
                        $scope.gguards = [];
                        $scope.trans.kpi = "";
                    }
                    $scope.loadingThana = false;
                })
            }
            $scope.loadGuard = function (id) {
                $scope.loadingKpi = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: id}
                }).then(function (response) {
                    if (!$scope.trans.open) {
                        $scope.guards = response.data;
                        $scope.selected.kpi = "";
                    }
                    else {
                        $scope.gguards = response.data;
                        $scope.trans.kpi = "";
                    }
                    $scope.loadingKpi = false;
                })
            }
            $scope.loadAnsar = function (id) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('ansar_list_for_reduce')}}',
                    params: $scope.selected
                }).then(function (response) {
                    $scope.ansars = response.data;
                    $scope.checked = Array.apply(null, Array($scope.ansars.length)).map(Boolean.prototype.valueOf, false)
                    $scope.q = ''
                    $scope.allLoading = false;
                })
            }
            $scope.openSingleFreezeModal = function (i) {
                $scope.freezeData['ansarId'] = [$scope.ansars[i].ansar_id];
                $scope.freezeData['kpiId'] = $scope.selected.kpi;
                $scope.freezeData['reduce_reason'] = f;
                $("#single-freeze").modal('show')
            }
            $scope.openSingleTransModal = function (i) {
                $scope.transData['transferred_ansar'] = [{
                    ansar_id: $scope.ansars[i].ansar_id,
                    joining_date: $filter('dateformat')($scope.ansars[i].joining_date, 'DD-MMM-YYYY')
                }];
                $scope.transData['kpi_id'] = [$scope.selected.kpi];
                $scope.trans.open = true;
                $("#single-trans").modal('show')
            }
            $scope.openMulFreezeModal = function (i) {
                $scope.freezeData['ansarId'] = [];
                $scope.checked.forEach(function (v) {
                    if (v !== false) $scope.freezeData['ansarId'].push($scope.ansars[v].ansar_id)
                })
                $scope.freezeData['kpiId'] = $scope.selected.kpi;
                $scope.freezeData['reduce_reason'] = f;
                $("#multi-freeze").modal('show')
            }
            $scope.openMulTransModal = function () {
                $scope.transData['transferred_ansar'] = [];
                $scope.checked.forEach(function (v) {
                    if (v !== false) $scope.transData['transferred_ansar'].push({
                        ansar_id: $scope.ansars[v].ansar_id,
                        joining_date: $filter('dateformat')($scope.ansars[v].joining_date, 'DD-MMM-YYYY')
                    });
                })
                $scope.transData['kpi_id'] = [$scope.selected.kpi];
                $scope.trans.open = true;
                console.log($scope.transData)
                $("#multi-trans").modal('show')
            }
            $scope.$watch('checked', function (n, o) {
                if (n.length <= 0) return;
                var r = n.every(function (i) {
                    return i !== false;
                })
                $scope.checkedAll = r;
            }, true)
            $scope.checkAll = function () {
                if ($scope.checkedAll) {
                    $scope.checked = Array.apply(null, Array($scope.ansars.length)).map(Number.call, Number);
                }
                else $scope.checked = Array.apply(null, Array($scope.ansars.length)).map(Boolean.prototype.valueOf, false);
            }
            $scope.submitFreezeData = function () {
                console.log($scope.freezeData);
//                return;
                $scope.submitting = true;
                $http({
                    url: '{{URL::route('ansar-reduce-update')}}',
                    method: 'post',
                    data: angular.toJson($scope.freezeData)
                }).then(function (response) {
                    $scope.submitting = false;
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#single-freeze,#multi-freeze").modal('hide')
                        $scope.freezeData = {};
                        $scope.loadAnsar();
                    }
                    else {
                        notificationService.notify('error', response.data.message)
                    }
                }, function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', 'An Unknown error occur. Error Code : ' + response.status)
                })
            }
            $scope.submitTransData = function () {
                $scope.transData['kpi_id'].push($scope.trans.kpi)
                console.log($scope.transData);
                $scope.submitting = true;
                $http({
                    url: '{{URL::route('complete_transfer_process')}}',
                    method: 'post',
                    data: angular.toJson($scope.transData)
                }).then(function (response) {
                    console.log(response.data)
                    $scope.submitting = false;
                    if (response.data.status) {
                        notificationService.notify('success', "Transfer complete");
                        $("#single-trans,#multi-trans").modal('hide')
                        $scope.transData = {};
                        $scope.loadAnsar();
                    }
                    else {
                        notificationService.notify('error', "Invalid Request")
                    }
                }, function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', 'An Unknown error occur. Error Code : ' + response.status)
                })
            }
            $scope.actualValue = function (value, index, array) {

                return value !== false;

            }
            $scope.resetForm = function () {
                $scope.trans.open = false;
                $scope.transData = {};
                $scope.trans.unit = ''
                $scope.trans.thana = ''
                $scope.trans.kpi = ''
                $scope.tthanas = []
                $scope.gguards = []
            }
            if ($scope.isAdmin == 11) {
                $scope.loadDistrict()
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.loadThana($scope.dcDistrict)
                }
            }

        })
    </script>
    <div ng-controller="AnsarReduceController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('reduce_guard_strength') !!}--}}
        {{--</div>--}}
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4" ng-show="isAdmin==11">
                            <div class="form-group">
                                <label class="control-label">
                                    @lang('title.unit')&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingUnit">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selected.unit"
                                        ng-change="loadThana(selected.unit)">
                                    <option value="">--@lang('title.unit')--</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">
                                    @lang('title.thana')&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingThana">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selected.thana"
                                        ng-change="loadGuard(selected.thana)">
                                    <option value="">--@lang('title.thana')--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">
                                    @lang('title.kpi')&nbsp;&nbsp;
                                    <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                         ng-show="loadingKpi">
                                </label>
                                <select class="form-control" ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                        ng-model="selected.kpi" ng-change="loadAnsar()"
                                        id="kpi_name_list_for_reduce" name="kpi_name_list_for_reduce">
                                    <option value="">--@lang('title.kpi')--</option>
                                    <option ng-repeat="d in guards" value="[[d.id]]">[[d.kpi_name]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="pc-table">
                                    <caption>
                                        <span class="text text-bold" style="font-size: 1.3em;color: #000000">Total Ansar : [[results.length]]</span>
                                        <input type="text" ng-model="q" placeholder="search in this table"
                                               class="pull-right">

                                    </caption>
                                    <tr>
                                        <th>
                                            <input type="checkbox" ng-model="checkedAll" ng-change="checkAll()" ng-disabled="results.length<=0||ansars.length<=0||ansars==undefined">
                                        </th>
                                        <th>Ansar ID</th>
                                        <th>Ansar Name</th>
                                        <th>Ansar Designation</th>
                                        <th>Ansar Gender</th>
                                        <th>KPI Name</th>
                                        <th>KPI Unit</th>
                                        <th>KPI Thana</th>
                                        <th>Reporting Date</th>
                                        <th>Embodiment Date</th>
                                        <th>Select From Here</th>
                                    </tr>
                                    <tr ng-repeat="a in ansars|filter:q as results">
                                        <th>
                                            <input type="checkbox" ng-model="checked[$index]" ng-true-value="[[$index]]"
                                                   ng-false-value="false">
                                        </th>
                                        <td>[[a.ansar_id]]</td>
                                        <td>[[a.ansar_name_eng]]</td>
                                        <td>[[a.name_eng]]</td>
                                        <td>[[a.sex]]</td>
                                        <td>[[a.kpi_name]]</td>
                                        <td>[[a.unit_name_eng]]</td>
                                        <td>[[a.thana_name_eng]]</td>
                                        <td>[[a.reporting_date|dateformat:'DD-MMM-YYYY']]</td>
                                        <td>[[a.joining_date|dateformat:'DD-MMM-YYYY']]</td>
                                        <td>
                                            <div class="col-xs-1">
                                                <a class="btn btn-primary btn-xs" title="Freeze"
                                                   ng-click="openSingleFreezeModal($index)">
                                                    <i class="fa fa-cube"></i>
                                                </a>
                                            </div>
                                            <div class="col-xs-1">
                                                <a class="btn btn-primary btn-xs"
                                                   ng-click="openSingleTransModal($index)" title="Transfer">
                                                    <i class="fa fa-envelope-o"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr ng-if="ansars==undefined||ansars.length<=0||results.length<=0"
                                        class="warning" id="not-find-info">
                                        <td colspan="11">No Ansar is available for Reduction</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default"><i class="fa fa-cog"></i>&nbsp;Select a action
                        </button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#" ng-click="openMulFreezeModal()">Freeze</a></li>
                            <li><a href="#" ng-click="openMulTransModal()">Transfer</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--Modal Open-->
            <div class="modal fade" id="single-freeze" role="dialog">
                <div class="modal-dialog">
                    <form method="post" id="kpiReduceForm" novalidate ng-submit="submitFreezeData()">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Freeze Ansar</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-7 col-centered">
                                        <div class="form-group">
                                            <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                        ng-show="isVerifying"><i
                                                            class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&!memorandumId">Memorandum ID is required.</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                            <input ng-blur="verifyMemorandumId(freezeData.memorandumId)"
                                                   ng-model="freezeData.memorandumId"
                                                   type="text" class="form-control" name="memorandum_id"
                                                   placeholder="Enter memorandum id">
                                        </div>
                                    </div>
                                    <div class="col-sm-7 col-centered"
                                         ng-class="{ 'has-error': kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$invalid }">
                                        <div class="form-group">
                                            <label class="control-label">Date of
                                                Withdrawal:&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                   ng-if="kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$error.required">Date is required.</span></label>
                                            <input type="text" date-picker name="reduce_guard_strength_date"
                                                   ng-model="freezeData.reduce_guard_strength_date"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-7 col-centered"
                                         ng-class="{ 'has-error': kpiReduceForm.reduce_reason.$touched && kpiReduceForm.reduce_reason.$invalid }">
                                        <div class="form-group">
                                            <label class="control-label">Reason of
                                                Withdrawal:&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                   ng-if="kpiReduceForm.reduce_reason.$touched && kpiReduceForm.reduce_reason.$error.required">Reason is required.</span></label>
                                            <input type="text" class="form-control" name="reduce_reason"
                                                   ng-model="freezeData.reduce_reason" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right" ng-disabled="submitting">
                                    <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>Freeze
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" modal hide="resetForm()" id="single-trans" role="dialog">
                <div class="modal-dialog">
                    <form method="post" id="kpiReduceForm" novalidate ng-submit="submitTransData()">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Transfer Ansar</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-7 col-centered" ng-show="isAdmin==11">
                                        <div class="form-group">
                                            <label class="control-label">
                                                @lang('title.unit')&nbsp;&nbsp;
                                                <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                     ng-show="loadingUnit">
                                            </label>
                                            <select class="form-control"
                                                    ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                    ng-model="trans.unit"
                                                    ng-change="loadThana(trans.unit)">
                                                <option value="">--@lang('title.unit')--</option>
                                                <option ng-repeat="d in ddistricts" value="[[d.id]]">[[d.unit_name_bng]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-7 col-centered">
                                        <div class="form-group">
                                            <label class="control-label">
                                                @lang('title.thana')&nbsp;&nbsp;
                                                <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                     ng-show="loadingThana">
                                            </label>
                                            <select class="form-control"
                                                    ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                    ng-model="trans.thana"
                                                    ng-change="loadGuard(trans.thana)">
                                                <option value="">--@lang('title.thana')--</option>
                                                <option ng-repeat="t in tthanas" value="[[t.id]]">[[t.thana_name_bng]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-7 col-centered">
                                        <div class="form-group">
                                            <label class="control-label">
                                                @lang('title.kpi')&nbsp;&nbsp;
                                                <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                     ng-show="loadingKpi">
                                            </label>
                                            <select class="form-control"
                                                    ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                    ng-model="trans.kpi"
                                                    id="kpi_name_list_for_reduce" name="kpi_name_list_for_reduce">
                                                <option value="">--@lang('title.kpi')--</option>
                                                <option ng-repeat="d in gguards" value="[[d.id]]">[[d.kpi_name]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-7 col-centered">
                                        <div class="form-group">
                                            <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                        ng-show="isVerifying"><i
                                                            class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&!transData.memorandum_id">Memorandum ID is required.</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&transData.memorandum_id">This id already taken.</span></label>
                                            <input ng-blur="verifyMemorandumId(transData.memorandum_id)"
                                                   ng-model="transData.memorandum_id"
                                                   type="text" class="form-control" name="memorandum_id"
                                                   placeholder="Enter memorandum id">
                                        </div>
                                    </div>
                                    <div class="col-sm-7 col-centered"
                                         ng-class="{ 'has-error': kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$invalid }">
                                        <div class="form-group">
                                            <label class="control-label">Date of
                                                Transfer:&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                 ng-if="kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$error.required">Date is required.</span></label>
                                            <input type="text" date-picker name="reduce_guard_strength_date"
                                                   ng-model="transData.transfer_date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right" ng-disabled="submitting">
                                    <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>Transfer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="multi-trans" modal hide="resetForm()" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <form method="post" id="kpiReduceForm" novalidate ng-submit="submitTransData()">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Transfer Ansar</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-4" ng-show="isAdmin==11">
                                        <div class="form-group">
                                            <label class="control-label">
                                                @lang('title.unit')&nbsp;&nbsp;
                                                <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                     ng-show="loadingUnit">
                                            </label>
                                            <select class="form-control"
                                                    ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                    ng-model="trans.unit"
                                                    ng-change="loadThana(trans.unit)">
                                                <option value="">--@lang('title.unit')--</option>
                                                <option ng-repeat="d in ddistricts" value="[[d.id]]">[[d.unit_name_bng]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                @lang('title.thana')&nbsp;&nbsp;
                                                <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                     ng-show="loadingThana">
                                            </label>
                                            <select class="form-control"
                                                    ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                    ng-model="trans.thana"
                                                    ng-change="loadGuard(trans.thana)">
                                                <option value="">--@lang('title.thana')--</option>
                                                <option ng-repeat="t in tthanas" value="[[t.id]]">[[t.thana_name_bng]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                @lang('title.kpi')&nbsp;&nbsp;
                                                <img src="{{asset('dist/img/facebook.gif')}}" style="width: 16px;"
                                                     ng-show="loadingKpi">
                                            </label>
                                            <select class="form-control"
                                                    ng-disabled="loadingUnit||loadingThana||loadingKpi"
                                                    ng-model="trans.kpi"
                                                    id="kpi_name_list_for_reduce" name="kpi_name_list_for_reduce">
                                                <option value="">--@lang('title.kpi')--</option>
                                                <option ng-repeat="d in gguards" value="[[d.id]]">[[d.kpi_name]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                        ng-show="isVerifying"><i
                                                            class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&!transData.memorandum_id">Memorandum ID is required.</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&transData.memorandum_id">This id already taken.</span></label>
                                            <input ng-blur="verifyMemorandumId(transData.memorandum_id)"
                                                   ng-model="transData.memorandum_id"
                                                   type="text" class="form-control" name="memorandum_id"
                                                   placeholder="Enter memorandum id">
                                        </div>
                                    </div>
                                    <div class="col-sm-4"
                                         ng-class="{ 'has-error': kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$invalid }">
                                        <div class="form-group">
                                            <label class="control-label">Date of
                                                Transfer:&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                 ng-if="kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$error.required">Date is required.</span></label>
                                            <input type="text" date-picker name="reduce_guard_strength_date"
                                                   ng-model="transData.transfer_date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Ansar ID</th>
                                            <th>Ansar Name</th>
                                            <th>Ansar Designation</th>
                                            <th>Ansar Gender</th>
                                            <th>KPI Name</th>
                                            <th>KPI Unit</th>
                                            <th>KPI Thana</th>
                                            <th>Reporting Date</th>
                                            <th>Embodiment Date</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr ng-repeat="a in checked|filter:actualValue as p">
                                            <td>[[ansars[a].ansar_id]]</td>
                                            <td>[[ansars[a].ansar_name_eng]]</td>
                                            <td>[[ansars[a].name_eng]]</td>
                                            <td>[[ansars[a].sex]]</td>
                                            <td>[[ansars[a].kpi_name]]</td>
                                            <td>[[ansars[a].unit_name_eng]]</td>
                                            <td>[[ansars[a].thana_name_eng]]</td>
                                            <td>[[ansars[a].reporting_date|dateformat:'DD-MMM-YYYY']]</td>
                                            <td>[[ansars[a].joining_date|dateformat:'DD-MMM-YYYY']]</td>
                                            <td>
                                                <div class="col-xs-1">
                                                    <a class="btn btn-danger btn-xs" title="Freeze"
                                                       ng-click="checked[checked.indexOf(a)]=false">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr colspan="10" ng-if="ansars==undefined||ansars.length<=0||p.length<=0"
                                            class="warning" id="not-find-info">
                                            <td colspan="10">No Ansar is available for Reduction</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right" ng-disabled="submitting">
                                    <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>Transfer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="multi-freeze" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <form method="post" id="kpiReduceForm" novalidate ng-submit="submitFreezeData()">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Freeze Ansar</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                        ng-show="isVerifying"><i
                                                            class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&!memorandumId">Memorandum ID is required.</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                            <input ng-blur="verifyMemorandumId(freezeData.memorandumId)"
                                                   ng-model="freezeData.memorandumId"
                                                   type="text" class="form-control" name="memorandum_id"
                                                   placeholder="Enter memorandum id">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">Date of
                                                Withdrawal:&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                   ng-if="kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$error.required">Date is required.</span></label>
                                            <input type="text" date-picker name="reduce_guard_strength_date"
                                                   ng-model="freezeData.reduce_guard_strength_date"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-4"
                                         ng-class="{ 'has-error': kpiReduceForm.reduce_guard_strength_date.$touched && kpiReduceForm.reduce_guard_strength_date.$invalid }">
                                        <div class="form-group">
                                            <label class="control-label">Reason of
                                                Withdrawal:&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                   ng-if="kpiReduceForm.reduce_reason.$touched && kpiReduceForm.reduce_reason.$error.required">Reason is required.</span></label>
                                            <input type="text" class="form-control" name="reduce_reason"
                                                   ng-model="freezeData.reduce_reason" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Ansar ID</th>
                                            <th>Ansar Name</th>
                                            <th>Ansar Designation</th>
                                            <th>Ansar Gender</th>
                                            <th>KPI Name</th>
                                            <th>KPI Unit</th>
                                            <th>KPI Thana</th>
                                            <th>Reporting Date</th>
                                            <th>Embodiment Date</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr ng-repeat="a in checked|filter:actualValue as p">
                                            <td>[[ansars[a].ansar_id]]</td>
                                            <td>[[ansars[a].ansar_name_eng]]</td>
                                            <td>[[ansars[a].name_eng]]</td>
                                            <td>[[ansars[a].sex]]</td>
                                            <td>[[ansars[a].kpi_name]]</td>
                                            <td>[[ansars[a].unit_name_eng]]</td>
                                            <td>[[ansars[a].thana_name_eng]]</td>
                                            <td>[[ansars[a].reporting_date|dateformat:'DD-MMM-YYYY']]</td>
                                            <td>[[ansars[a].joining_date|dateformat:'DD-MMM-YYYY']]</td>
                                            <td>
                                                <div class="col-xs-1">
                                                    <a class="btn btn-danger btn-xs" title="Freeze"
                                                       ng-click="checked[checked.indexOf(a)]=false">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr colspan="10" ng-if="ansars==undefined||ansars.length<=0||p.length<=0"
                                            class="warning" id="not-find-info">
                                            <td colspan="10">No Ansar is available for Reduction</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right" ng-disabled="submitting">
                                    <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>freeze
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--Modal Close-->
            <!-- /.row -->
        </section>
    </div>
@stop