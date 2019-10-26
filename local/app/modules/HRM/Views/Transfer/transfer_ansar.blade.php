@extends('template.master')
@section('title','Transfer Ansars')
@section('breadcrumb')
    {!! Breadcrumbs::render('transfer') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("TransferController", function ($scope, $http, $timeout, $rootScope) {
            $scope.ansars = [];
            $scope.tsPC = 0;
            $scope.tsAPC = 0;
            $scope.tsAnsar = 0;
            $scope.params = '';
            $scope.trans = '';
            $scope.showKpiStatus = false;
            $scope.resetValue = false;
            $scope.totalKpiAnsar = {
                pc: {
                    given: 0, current: 0
                },
                apc: {
                    given: 0, current: 0
                },
                ansar: {
                    given: 0, current: 0
                }
            };
            $scope.noAnsar = true;
            $scope.loadingAnsar = false;
            $scope.ansars = [];
            $scope.allDistrict = [];
            $scope.allThana = [];
            $scope.allKPI = [];
            $scope.selectedAnsar = [];
            $scope.selectAnsar = [];
            $scope.memorandumId = "";
            $scope.joinDate = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.modalOpen = false;
            $scope.selectAll = false;
            $scope.showDialog = false;
            $scope.allLoading = false;
            $scope.result = {};
            $scope.loadAnsar = function () {
                $scope.loadingAnsar = true;
                $scope.allLoading = true;
                if (!$scope.params.kpi) {
                    $scope.selectAll = false;
                    $scope.selectedAnsar = [];
                    $scope.selectAnsar = [];
                    $scope.ansars = [];
                    $scope.loadingAnsar = false;
                    return;
                }
                $http({
                    url: "{{URL::route('get_embodied_ansar')}}",
                    method: 'get',
                    params: {kpi_id: $scope.params.kpi}
                }).then(function (response) {
                    $scope.ansars = response.data;
                    $scope.selectAnsar = new Array($scope.ansars.length);
                    $scope.loadingAnsar = false;
                    $scope.selectAll = false;
                    $scope.selectedAnsar = [];
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.loadingAnsar = false;
                })
            };
            $scope.$watch('results', function (newValue, oldValue) {
                if (newValue !== undefined && newValue.constructor === Array && newValue.length !== undefined) $scope.selectAnsar = Array.apply(null, new Array(newValue.length)).map(Boolean.prototype.valueOf, $scope.selectAll);
            }, true);
            $scope.changeSelectAnsar = function (i) {
                var index = $scope.selectedAnsar.indexOf($scope.results[i]);
                if ($scope.selectAnsar[i]) {
                    if (index == -1) {
                        $scope.selectedAnsar.push($scope.results[i])
                    }
                } else {
                    $scope.selectedAnsar.splice(index, 1);
                }
                $scope.selectAll = $scope.selectedAnsar.length == $scope.results.length;
            };
            $scope.$watch('selectAnsar', function (n, o) {
                n.forEach(function (e, i, a) {
                    $scope.changeSelectAnsar(i);
                })
            });
            $scope.changeSelectAll = function () {
                $scope.selectAnsar = Array.apply(null, new Array($scope.results.length)).map(Boolean.prototype.valueOf, $scope.selectAll);
            };
            $scope.letterOption = {
                id: $scope.memorandumId,
                unit: $scope.trans.unit
            };
            $scope.pl = false;
            $scope.confirmTransferAnsar = function () {
                var ansar_id = [];
                $scope.letterOption = {
                    id: $rootScope.user.district_id ? $rootScope.user.district_id : angular.copy($scope.memorandumId),
                    unit: angular.copy($scope.trans.unit),
                    option: 'memorandumNo',
                    type: 'TRANSFER',
                    status: false
                };
                $scope.pl = false;
                $scope.modalOpen = false;
                $scope.selectedAnsar.forEach(function (a) {
                    ansar_id.push({ansar_id: a.ansar_id, joining_date: a.transfered_date});
                });
                $scope.allLoading = true;
                var data = {
                    memorandum_id: $scope.memorandumId,
                    transfer_date: $scope.joinDate,
                    kpi_id: [$scope.params.kpi, $scope.trans.kpi],
                    transferred_ansar: ansar_id,
                    unit: $scope.trans.unit,
                    mem_date: $scope.memDate
                };
                $http({
                    url: '{{URL::route('complete_transfer_process')}}',
                    data: angular.toJson(data),
                    method: 'post'
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.result = response.data;
                    $scope.q = '';
                    if ($scope.result.data.success.count > 0) {
                        $scope.loadAnsar();
                        $scope.letterOption.status = true
                    }
                }, function (response) {
                    $scope.allLoading = false;
                    return;
                })
            };
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                };
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {
                })
            }
        });
        GlobalApp.directive('openHideModal', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {
                    $(elem).tooltip({title: "Select at least an ansar", trigger: 'manual'});
                    $(elem).on('click', function () {
                        if (scope.selectedAnsar.length <= 0) {
                            $(this).tooltip('show');
                            setTimeout(function () {
                                $(elem).tooltip('hide');
                            }, 1000);
                            return;
                        }
                        $("#transfer-option").modal("toggle");
                        $("#transfer-option").on('show.bs.modal', function () {
                            scope.resetValue = false;
                            scope.result = [];
                            scope.memorandumId = "";
                            scope.joinDate = "";
                            scope.showKpiStatus = false;
                            scope.$apply()
                        });
                        $("#transfer-option").on('hide.bs.modal', function () {
                            modalOpen = false;
                            scope.resetValue = true;
                            scope.$apply()
                        })
                    })
                }
            }
        });
        GlobalApp.directive('notificationMessage', function (notificationService) {
            return {
                restrict: 'ACE',
                link: function (scope, elem, attrs) {
                    scope.$watch('result', function (newValue, oldValue) {
                        if (Object.keys(newValue).length > 0) {
                            if (!newValue.status) {
                                notificationService.notify('error', newValue.message)
                            }
                            if (newValue.data.success.count > 0) {
                                for (i = 0; i < newValue.data.success.count; i++) {
                                    notificationService.notify(
                                        'success', "Ansar(" + newValue.data.success.data[i] + ") successfully transfered"
                                    )
                                }
                            } else {
                                scope.pl = false;
                            }
                            if (newValue.data.error.count > 0) {
                                for (i = 0; i < newValue.data.error.count; i++) {
                                    notificationService.notify(
                                        'error', newValue.data.error.data[i]
                                    )
                                }
                            }
                        }
                    })
                }
            }
        });
    </script>
    <div notification-message ng-controller="TransferController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit','thana','kpi']"
                            type="single"
                            kpi-change="loadAnsar()"
                            start-load="range"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                            data="params"></filter-template>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="pc-table">
                            <caption>
                                <table-search q="q" results="results"></table-search>
                            </caption>
                            <tr>
                                <th>SL. No</th>
                                <th>ID</th>
                                <th>Designation</th>
                                <th>Name</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>KPI Name</th>
                                <th>Embodiment Date</th>
                                <th>Last Transfer Date</th>
                                <th>
                                    <div class="styled-checkbox">
                                        <input ng-disabled="ansars.length<=0" type="checkbox" id="all"
                                               ng-change="changeSelectAll()" ng-model="selectAll">
                                        <label for="all"></label>
                                    </div>
                                </th>
                            </tr>
                            <tr class="warning" ng-if="ansars.length<=0">
                                <td colspan="9">No Ansar Found to Transfer</td>
                            </tr>
                            <tr ng-repeat="ansar in ansars|filter:q as results" ng-if="ansars.length>0">
                                <td>[[$index+1]]</td>
                                <td>[[ansar.ansar_id]]</td>
                                <td>[[ansar.name_bng]]</td>
                                <td>[[ansar.ansar_name_bng]]</td>
                                <td>[[ansar.division_name_bng]]</td>
                                <td>[[ansar.unit_name_bng]]</td>
                                <td>[[ansar.kpi_name]]</td>
                                <td>[[ansar.joining_date|dateformat:'DD-MMM-YYYY']]</td>
                                <td>[[ansar.transfered_date|dateformat:'DD-MMM-YYYY']]</td>
                                <td>
                                    <div class="styled-checkbox">
                                        <input type="checkbox" id="a_[[ansar.ansar_id]]"
                                               ng-change="changeSelectAnsar($index)"
                                               ng-model="selectAnsar[$index]">
                                        <label for="a_[[ansar.ansar_id]]"></label>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        {!! Form::open(['route'=>'print_letter','target'=>'_blank','ng-if'=>'letterOption.status','class'=>'pull-left']) !!}
                        <input type="hidden" ng-repeat="(key,value) in letterOption" name="[[key]]" value="[[value]]">
                        <button class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;Print Transfer Letter</button>
                        {!! Form::close() !!}
                        <button class="pull-right btn btn-primary" open-hide-modal ng-click="modalOpen=true">
                            <i class="fa fa-send"></i>&nbsp;&nbsp;Transfer
                        </button>
                        <div class="clearfix"></div>
                    </div>
                </div>

            </div>
            <div id="transfer-option" class="modal fade" role="dialog">
                <div class="modal-dialog"
                     style="width: 70% !important;margin: 0 auto !important;margin-top: 20px !important;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <strong>Transfer Option</strong>
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="register-box" style="margin: 0;width: auto">
                                <div class="register-box-body  margin-bottom" style="padding: 0;padding-bottom: 10px">
                                    <filter-template
                                            show-item="['range','unit','thana','kpi']"
                                            type="single"
                                            start-load="range"
                                            kpi-disabled="params.kpi"
                                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                                            data="trans"
                                    ></filter-template>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                            ng-show="isVerifying"><i class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span>
                                                    <span class="text-danger"
                                                          ng-if="isVerified">This id already taken</span></label>
                                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                                       type="text" class="form-control" name="memorandum_id"
                                                       placeholder="Enter memorandum id">
                                            </div>
                                            <div class="form-group">
                                                <datepicker-separate-fields label="Memorandum Date:"
                                                                            notify="memoInvalidDate"
                                                                            rdata="memDate"></datepicker-separate-fields>
                                                <input ng-value="memDate" type="hidden" name="mem_date">
                                            </div>
                                            <div class="form-group">
                                                <datepicker-separate-fields label="Embodiment date in transferred kpi."
                                                                            notify="joinInvalidDate"
                                                                            rdata="joinDate"></datepicker-separate-fields>
                                                <input type="hidden" ng-value="joinDate" name="memorandum_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="max-height: 200px">
                                            <tr>
                                                <th>SL. No</th>
                                                <th>ID</th>
                                                <th>Designation</th>
                                                <th>Name</th>
                                                <th>Division</th>
                                                <th>District</th>
                                                <th>KPI Name</th>
                                                <th>Embodiment Date</th>
                                            </tr>
                                            <tr class="warning" ng-if="selectedAnsar.length<=0">
                                                <td colspan="8">No Ansar Found to Transfer</td>
                                            </tr>
                                            <tr ng-repeat="ansar in selectedAnsar" ng-if="selectedAnsar.length>0">
                                                <td>[[$index+1]]</td>
                                                <td>[[ansar.ansar_id]]</td>
                                                <td>[[ansar.name_bng]]</td>
                                                <td>[[ansar.ansar_name_bng]]</td>
                                                <td>[[ansar.division_name_bng]]</td>
                                                <td>[[ansar.unit_name_bng]]</td>
                                                <td>[[ansar.kpi_name]]</td>
                                                <td>[[ansar.joining_date|dateformat:'DD-MMM-YYYY']]</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary pull-right" open-hide-modal
                                            ng-disabled="joinInvalidDate||memoInvalidDate||selectedAnsar.length<=0||!memorandumId||!joinDate||!trans.kpi||isVerified||isVerifying"
                                            ng-click="confirmTransferAnsar()">
                                        <i class="fa fa-check"></i>&nbsp;Confirm
                                    </button>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop