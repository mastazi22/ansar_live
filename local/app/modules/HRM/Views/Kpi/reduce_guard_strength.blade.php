@extends('template.master')
@section('title','Reduce Ansar In Guard Strength')
@section('breadcrumb')
    {!! Breadcrumbs::render('reduce_guard_strength') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarReduceController', function ($scope, $http, notificationService, $filter) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $scope.districts = [];
            $scope.thanas = [];
            $scope.freezeData = {};
            $scope.transData = {};
            $scope.selected = {
                range: '',
                unit: '',
                thana: '',
                kpi: ''
            };
            $scope.checked = [];
            $scope.checkedAll = false;
            $scope.trans = {
                range: '',
                unit: '',
                thana: '',
                kpi: '',
                open: false
            };
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.memorandumId = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.allLoading = false;
            $scope.gCount = {};
            var f = "Freeze Ansar for Guard's Strength Reduction";
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.verifyMemorandumId = function (id) {
                var data = {
                    memorandum_id: id
                };
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{URL::to('verify_memorandum_id')}}', data).then(function (response) {
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {
                })
            };
            $scope.loadAnsar = function (id) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('ansar_list_for_reduce')}}',
                    params: $scope.selected
                }).then(function (response) {
                    $scope.gCount = response.data.tCount;
                    $scope.gCount['total'] = sum(response.data.tCount);
                    $scope.ansars = response.data.list;
                    $scope.checked = Array.apply(null, Array($scope.ansars.length)).map(Boolean.prototype.valueOf, false);
                    $scope.q = '';
                    $scope.allLoading = false;
                })
            };
            $scope.openSingleFreezeModal = function (i) {
                $scope.freezeData['ansarId'] = [$scope.ansars[i].ansar_id];
                $scope.freezeData['kpiId'] = $scope.selected.kpi;
                $scope.freezeData['reduce_reason'] = f;
                $("#single-freeze").modal('show')
            };
            $scope.openSingleTransModal = function (i) {
                $scope.transData['transferred_ansar'] = [{
                    ansar_id: $scope.ansars[i].ansar_id,
                    joining_date: $filter('dateformat')($scope.ansars[i].joining_date, 'DD-MMM-YYYY')
                }];
                $scope.transData['kpi_id'] = [$scope.selected.kpi];
                $scope.trans.open = true;
                $("#single-trans").modal('show')
            };
            $scope.openMulFreezeModal = function (i) {
                $scope.freezeData['ansarId'] = [];
                $scope.checked.forEach(function (v) {
                    if (v !== false) $scope.freezeData['ansarId'].push($scope.ansars[v].ansar_id)
                });
                $scope.freezeData['kpiId'] = $scope.selected.kpi;
                $scope.freezeData['reduce_reason'] = f;
                $("#multi-freeze").modal('show')
            };
            $scope.openMulTransModal = function () {
                $scope.transData['transferred_ansar'] = [];
                $scope.checked.forEach(function (v) {
                    if (v !== false) $scope.transData['transferred_ansar'].push({
                        ansar_id: $scope.ansars[v].ansar_id,
                        joining_date: $filter('dateformat')($scope.ansars[v].joining_date, 'DD-MMM-YYYY')
                    });
                });
                $scope.transData['kpi_id'] = [$scope.selected.kpi];
                $scope.trans.open = true;
                $("#multi-trans").modal('show')
            };
            $scope.$watch('checked', function (n, o) {
                if (n.length <= 0) return;
                var r = n.every(function (i) {
                    return i !== false;
                });
                $scope.checkedAll = r;
            }, true);
            $scope.checkAll = function () {
                if ($scope.checkedAll) {
                    $scope.checked = Array.apply(null, Array($scope.ansars.length)).map(Number.call, Number);
                } else $scope.checked = Array.apply(null, Array($scope.ansars.length)).map(Boolean.prototype.valueOf, false);
            };
            $scope.submitFreezeData = function () {
                $scope.submitting = true;
                $http({
                    url: '{{URL::route('ansar-reduce-update')}}',
                    method: 'post',
                    data: angular.toJson($scope.freezeData)
                }).then(function (response) {
                    $scope.submitting = false;
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#single-freeze,#multi-freeze").modal('hide');
                        $scope.freezeData = {};
                        $scope.loadAnsar();
                    } else {
                        notificationService.notify('error', response.data.message)
                    }
                }, function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', 'An Unknown error occur. Error Code : ' + response.status)
                })
            };
            $scope.submitTransData = function () {
                $scope.transData['kpi_id'].push($scope.trans.kpi);
                $scope.submitting = true;
                $http({
                    url: '{{URL::route('complete_transfer_process')}}',
                    method: 'post',
                    data: angular.toJson($scope.transData)
                }).then(function (response) {
                    console.log(response.data);
                    $scope.submitting = false;
                    if (response.data.status) {
                        notificationService.notify('success', "Transfer complete");
                        $("#single-trans,#multi-trans").modal('hide');
                        $scope.transData = {};
                        $scope.loadAnsar();
                    } else {
                        notificationService.notify('error', "Invalid Request")
                    }
                }, function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', 'An Unknown error occur. Error Code : ' + response.status)
                })
            };
            $scope.resetForm = function () {
                $scope.trans.open = false;
                $scope.transData = {};
                $scope.trans.unit = '';
                $scope.trans.range = '';
                $scope.trans.thana = '';
                $scope.trans.kpi = '';
                $scope.tthanas = [];
                $scope.gguards = [];
            };
            $scope.actualValue = function (value, index, array) {
                return value !== false;
            };

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }

            $scope.changeRank = function (i) {
                $scope.selected["rank"] = i;
                $scope.loadAnsar()
            };
        })
    </script>
    <style>
        caption h5.text {
            display: none;
        }
    </style>
    <div ng-controller="AnsarReduceController">
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
                    <filter-template
                            show-item="['range','unit','thana','kpi','gender']"
                            type="single"
                            kpi-change="loadAnsar()"
                            gender-change="loadAnsar()"
                            start-load="range"
                            field-width="{range:'col-sm-2',unit:'col-sm-2',thana:'col-sm-2',gender:'col-sm-3',kpi:'col-sm-3'}"
                            data="selected"></filter-template>
                    <div class="row">
                        <div class="col-md-8" style="position: absolute;z-index: 100;">
                            <h4 class="text text-bold">
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank('all')">Total
                                    Ansars ([[gCount.total!=undefined?gCount.total.toLocaleString():0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(3)">PC
                                    ([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(2)">APC
                                    ([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(1)">Ansar
                                    ([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</a>
                            </h4>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="pc-table">
                                    <caption>
                                        <table-search q="q" results="results"></table-search>
                                    </caption>
                                    <tr>
                                        <th>
                                            <input type="checkbox" ng-model="checkedAll" ng-change="checkAll()"
                                                   ng-disabled="results.length<=0||ansars.length<=0||ansars==undefined">
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
                                        <th style="width:80px">Action</th>
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
                                            <a class="btn btn-primary btn-xs" title="Freeze"
                                               ng-click="openSingleFreezeModal($index)">
                                                <i class="fa fa-cube"></i>
                                            </a>
                                            <a class="btn btn-primary btn-xs"
                                               ng-click="openSingleTransModal($index)" title="Transfer">
                                                <i class="fa fa-envelope-o"></i>
                                            </a>
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
                                                        ng-if="isVerified&&!memorandumId">Memorandum no. is required.</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&memorandumId">This is already taken.</span></label>
                                            <input ng-blur="verifyMemorandumId(freezeData.memorandumId)" type="text"
                                                   ng-model="freezeData.memorandumId" placeholder="Enter memorandum id"
                                                   class="form-control" name="memorandum_id">
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
                                    <div class="col-sm-10 col-centered">
                                        <datepicker-separate-fields label="Date of Withdrawal:"
                                                                    notify="singleFreezeKPIReduceDateInvalid"
                                                                    rdata="freezeData.reduce_guard_strength_date"></datepicker-separate-fields>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right"
                                        ng-disabled="submitting || singleFreezeKPIReduceDateInvalid || freezeData.memorandumId==''">
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
                                <filter-template
                                        show-item="['range','unit','thana','kpi']"
                                        type="single"
                                        start-load="range"
                                        field-width="{range:'col-sm-7 col-centered',unit:'col-sm-7 col-centered',thana:'col-sm-7 col-centered',kpi:'col-sm-7 col-centered'}"
                                        data="trans">
                                </filter-template>
                                <div class="row">
                                    <div class="col-sm-7 col-centered">
                                        <div class="form-group">
                                            <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                        ng-show="isVerifying"><i
                                                            class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&!transData.memorandum_id">Memorandum no. is required.</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&transData.memorandum_id">This is already taken.</span></label>
                                            <input ng-blur="verifyMemorandumId(transData.memorandum_id)"
                                                   ng-model="transData.memorandum_id" type="text" class="form-control"
                                                   name="memorandum_id" placeholder="Enter memorandum id">
                                        </div>
                                    </div>
                                    <div class="col-sm-10 col-centered">
                                        <datepicker-separate-fields label="Date of Transfer:"
                                                                    notify="singleTransKPIReduceDateInvalid"
                                                                    rdata="transData.transfer_date"></datepicker-separate-fields>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right"
                                        ng-disabled="submitting || singleTransKPIReduceDateInvalid">
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
                                <filter-template
                                        show-item="['range','unit','thana','kpi']"
                                        type="single"
                                        start-load="range"
                                        field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                                        data="trans">
                                </filter-template>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                        ng-show="isVerifying"><i
                                                            class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&!transData.memorandum_id">Memorandum no. is required.</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&transData.memorandum_id">This is already taken.</span></label>
                                            <input ng-blur="verifyMemorandumId(transData.memorandum_id)"
                                                   ng-model="transData.memorandum_id"
                                                   type="text" class="form-control" name="memorandum_id"
                                                   placeholder="Enter memorandum id">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <datepicker-separate-fields label="Date of Transfer:"
                                                                    notify="multiTransKPIReduceDateInvalid"
                                                                    rdata="transData.transfer_date"></datepicker-separate-fields>
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
                                        <tr ng-if="ansars==undefined||ansars.length<=0||p.length<=0"
                                            class="warning" id="not-find-info">
                                            <td colspan="10">No Ansar is available for Reduction</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right"
                                        ng-disabled="submitting || multiTransKPIReduceDateInvalid">
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
                                                        ng-if="isVerified&&!memorandumId">Memorandum no. is required.</span><span
                                                        class="text-danger"
                                                        ng-if="isVerified&&memorandumId">This is already taken.</span></label>
                                            <input ng-blur="verifyMemorandumId(freezeData.memorandumId)"
                                                   ng-model="freezeData.memorandumId"
                                                   type="text" class="form-control" name="memorandum_id"
                                                   placeholder="Enter memorandum id">
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
                                <div class="row">
                                    <div class="col-md-8">
                                        <datepicker-separate-fields label="Date of Withdrawal:"
                                                                    notify="multiFreezeKPIReduceDateInvalid"
                                                                    rdata="freezeData.reduce_guard_strength_date"></datepicker-separate-fields>
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
                                        <tr ng-if="ansars==undefined||ansars.length<=0||p.length<=0"
                                            class="warning" id="not-find-info">
                                            <td colspan="10">No Ansar is available for Reduction</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary pull-right"
                                        ng-disabled="submitting || multiFreezeKPIReduceDateInvalid">
                                    <i class="fa fa-pulse fa-spinner" ng-if="submitting"></i>freeze
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@stop