{{--Ansar Transfer Complete--}}

@extends('template.master')
@section('title','Transfer Ansars')
@section('breadcrumb')
    {!! Breadcrumbs::render('transfer') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("TransferController", function ($scope, $http, $timeout) {
            $scope.ansars = [];
            $scope.tsPC = 0;
            $scope.tsAPC = 0;
            $scope.tsAnsar = 0;
            $scope.showKpiStatus = false;
            $scope.totalKpiAnsar = {
                pc:{
                    given:0,current:0
                },
                apc:{
                    given:0,current:0
                },
                ansar:{
                    given:0,current:0
                }
            }
            $scope.noAnsar = true;
            $scope.selectedDistrict = ["", ""];
            $scope.selectedThana = ["", ""];
            $scope.selectedKPI = ["", ""];
            $scope.loadingThana = [false, false];
            $scope.loadingKPI = [false, false];
            $scope.loadingAnsar = false;
            $scope.ansars = [];
            $scope.allDistrict = []
            $scope.allThana = []
            $scope.allKPI = []
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
            var userType = parseInt("{{Auth::user()->type}}");
            $scope.loadDistrict = function () {
                $scope.showKpiStatus = false
                $http({
                    url: "{{URL::to('HRM/DistrictName')}}",
                    method: 'get'
                }).then(function (response) {
                    if ($scope.modalOpen)$scope.allDistrict[1] = response.data;
                    else $scope.allDistrict[0] = response.data;
                }, function (response) {

                })
            }
            $scope.loadThana = function () {
                $scope.showKpiStatus = false
                if ($scope.modalOpen) {
                    $scope.loadingThana[1] = true;
                    if (!$scope.selectedDistrict[1]) {
                        $scope.allThana[1] = []
                        $scope.allKPI[1] = []
                        $scope.selectedThana[1] = "";
                        $scope.selectedKPI[1] = "";
                        $scope.loadingThana[1] = false;
                        return;
                    }
                }
                else {
                    $scope.loadingThana[0] = true;
                    if (!$scope.selectedDistrict[0]) {
                        $scope.allThana[0] = []
                        $scope.allKPI[0] = []
                        $scope.selectedThana[0] = "";
                        $scope.selectedKPI[0] = "";
                        $scope.loadingThana[0] = false;
                        return;
                    }
                }
                $http({
                    url: "{{URL::to('HRM/ThanaName')}}",
                    method: 'get',
                    params: {id: $scope.modalOpen ? $scope.selectedDistrict[1] : $scope.selectedDistrict[0]}
                }).then(function (response) {
                    if ($scope.modalOpen) {
                        $scope.allThana[1] = response.data;
                        $scope.loadingThana[1] = false;
                        $scope.selectedThana[1] = "";
                        $scope.selectedKPI[1] = "";
                    }
                    else {
                        $scope.allThana[0] = response.data;
                        $scope.loadingThana[0] = false;
                        $scope.selectedThana[0] = "";
                        $scope.selectedKPI[0] = "";
                    }
                }, function (response) {
                    $scope.loadingThana = false;
                })
            }
            $scope.loadKpi = function () {
                if ($scope.modalOpen) {
                    $scope.loadingKPI[1] = true;
                    if (!$scope.selectedThana[1]) {
                        $scope.selectedKPI[1] = "";
                        $scope.allKPI[1] = []
                        $scope.loadingKPI[1] = false;
                        return;
                    }
                }
                else {
                    $scope.loadingKPI[0] = true;
                    if (!$scope.selectedThana[0]) {
                        $scope.selectedKPI[0] = "";
                        $scope.allKPI[0] = []
                        $scope.loadingKPI[0] = false;
                        return;
                    }
                }
                $http({
                    url: "{{URL::route('kpi_name')}}",
                    method: 'get',
                    params: {id: $scope.modalOpen ? $scope.selectedThana[1] : $scope.selectedThana[0]}
                }).then(function (response) {

                    if ($scope.modalOpen) {
                        $scope.allKPI[1] = response.data;
                        $scope.loadingKPI[1] = false;
                        $scope.selectedKPI[1] = "";
                    }
                    else {
                        $scope.allKPI[0] = response.data;
                        $scope.loadingKPI[0] = false;
                        $scope.selectedKPI[0] = "";
                    }
                }, function (response) {
                    $scope.loadingKPI = false;
                })
            }
            $scope.loadAnsar = function () {
                $scope.loadingAnsar = true;
                if (!$scope.selectedKPI[0]) {
                    $scope.selectAll = false;
                    $scope.selectedAnsar = [];
                    $scope.selectAnsar = [];
                    $scope.ansars = []
                    $scope.loadingAnsar = false;
                    return;
                }
                $http({
                    url: "{{URL::route('get_embodied_ansar')}}",
                    method: 'get',
                    params: {kpi_id: $scope.selectedKPI[0]}
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
            }
            $scope.$watch(function (scope) {
                return scope.modalOpen;
            }, function (n, o) {
                if ($scope.allDistrict[0] == null || $scope.allDistrict[1] == null) $scope.loadDistrict();
            })
            $scope.changeSelectAnsar = function (i) {
                var index = $scope.selectedAnsar.indexOf($scope.ansars[i]);
                if ($scope.selectAnsar[i]) {
                    if (index == -1) {
                        switch($scope.ansars[i].rank){
                            case 1:
                                $scope.tsAnsar += 1;
                                break
                            case 2:
                                $scope.tsAPC += 1;
                                break
                            case 3:
                                $scope.tsPC += 1;
                                break

                        }
                        $scope.selectedAnsar.push($scope.ansars[i])
                    }
                }
                else {
                    switch($scope.selectedAnsar[index].rank){
                        case 1:
                            $scope.tsAnsar -= 1;
                            break
                        case 2:
                            $scope.tsAPC -= 1;
                            break
                        case 3:
                            $scope.tsPC -= 1;
                            break

                    }
                    $scope.selectedAnsar.splice(index, 1);
                }
                $scope.selectAll = $scope.selectedAnsar.length == $scope.ansars.length;
            }
            $scope.$watch('selectAnsar', function (n, o) {
                n.forEach(function (e, i, a) {
                    $scope.changeSelectAnsar(i);
                })
            })
            $scope.changeSelectAll = function () {
                $scope.selectAnsar = Array.apply(null, new Array($scope.ansars.length)).map(Boolean.prototype.valueOf, $scope.selectAll);
            }
            $scope.confirmTransferAnsar = function () {
                var ansar_id = [];
                $scope.modalOpen = false;
                $scope.selectedAnsar.forEach(function (a) {
                    ansar_id.push({ansar_id: a.ansar_id, joining_date: a.transfered_date});
                })
                console.log(ansar_id)
                $scope.allLoading = true;
                var data = {
                    memorandum_id: $scope.memorandumId,
                    transfer_date: $scope.joinDate,
                    kpi_id: $scope.selectedKPI,
                    transferred_ansar: ansar_id,
                    unit: $scope.selectedDistrict[1]
                }
                $http({
                    url: '{{URL::route('complete_transfer_process')}}',
                    data: angular.toJson(data),
                    method: 'post'
                }).then(function (response) {
                    $scope.allLoading = false
                    console.log(response.data)
                    $scope.result = response.data;

                    if ($scope.result.data.success.count > 0) $scope.loadAnsar();


                }, function (response) {
                    $scope.result = {
                        status: false,
                        message: 'A Server error occur<br> ERROR CODE : ' + response.status
                    }
                })
            }
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }
            $scope.formatDate = function (d) {
                return moment(d).format("DD-MMM-YYYY")
            }

        })
        GlobalApp.directive('openHideModal', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {
                    $(elem).on('click', function () {
                        $("#transfer-option").modal("toggle")
                        $("#transfer-option").on('show.bs.modal', function () {
                            scope.result = [];
                            scope.selectedKPI[1] = "";
                            scope.selectedThana[1] = "";
                            scope.selectedDistrict[1] = ""
                            scope.memorandumId = "";
                            scope.joinDate = "";
                            $scope.showKpiStatus = false;
                            $scope.tsAnsar = 0;
                            $scope.tsPC = 0;
                            $scope.tsAPC = 0;
                        })
                        $("#transfer-option").on('hide.bs.modal', function () {
                            modalOpen = false;

                        })
                    })
                }
            }
        })
        GlobalApp.directive('notificationMessage', function () {
            return {
                restrict: 'ACE',
                link: function (scope, elem, attrs) {
                    scope.$watch('result', function (newValue, oldValue) {
                        if (Object.keys(newValue).length > 0) {
                            if (!newValue.status) {
                                $('body').notifyDialog({type: 'error', message: newValue.message}).showDialog()
                            }
                            if (newValue.data.success.count > 0) {
                                $('body').notifyDialog({
                                    type: 'success',
                                    message: "Success " + newValue.data.success.count + ", Failed " + newValue.data.error.count
                                }).showDialog()

                            }
                            else {
                                $('body').notifyDialog({
                                    type: 'error',
                                    message: "Transfer Failed. Pleas try again later"
                                }).showDialog()
                            }
                        }
                    })
                }
            }
        })
        GlobalApp.directive('checkKpi', function ($http) {
            return {
                restrict: 'AC',
                link: function (scope, elem, attrs) {
                    $(elem).on('change', function (e) {
                        var v = $(this).val()
//                            alert(v)
                        scope.loadingKPI[1] = true;
                        $http({
                            method: 'get',
                            params: {id: v},
                            url: "{{URL::route('kpi_detail')}}"
                        }).then(function (response) {
                            console.log(response.data);
                            scope.showKpiStatus = true;
                            scope.totalKpiAnsar.pc.given = response.data.detail.no_of_pc
                            scope.totalKpiAnsar.pc.current = response.data.ansar_count[2].total
                            scope.totalKpiAnsar.apc.given = response.data.detail.no_of_apc
                            scope.totalKpiAnsar.apc.current = response.data.ansar_count[1].total
                            scope.totalKpiAnsar.ansar.given = response.data.detail.no_of_ansar
                            scope.totalKpiAnsar.ansar.current = response.data.ansar_count[0].total
                            scope.loadingKPI[1] = false;

                        }, function (response) {
                            scope.loadingKPI[1] = false;
                        })
                    })

                }
            }
        })
        $(document).ready(function () {
            $("#join_date_in_tk").datePicker(false);
        })
    </script>
    <div notification-message style="min-height: 490px" ng-controller="TransferController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('transfer') !!}--}}
        {{--</div>--}}
        <div ng-show="allLoading"
             style="position:fixed;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.27);z-index: 100;top:0;left:0">
            <div style="position: relative;width: 20%;height: auto;margin: 200px auto;text-align: center;background: #FFFFFF">
                <img class="img-responsive" src="{{asset('dist/img/loading-data.gif')}}"
                     style="position: relative;margin: 0 auto">
                <h4>Transferring....</h4>
            </div>

        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row" style="padding-bottom: 10px">
                        <div class="col-md-4">
                            <label class="control-label"> Select a District&nbsp;&nbsp;&nbsp;<i
                                        class="fa fa-spinner fa-pulse" ng-show="loadingThana[0]"></i></label>
                            <select class="form-control" ng-model="selectedDistrict[0]"
                                    ng-disabled="loadingAnsar||loadingThana[0]||loadingKPI[0]"
                                    ng-change="loadThana()">
                                <option value="">--Select a District--</option>
                                <option ng-repeat="d in allDistrict[0]" value="[[d.id]]">[[d.unit_name_bng]]
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label"> Select a Thana&nbsp;&nbsp;&nbsp;<i
                                        class="fa fa-spinner fa-pulse" ng-show="loadingKPI[0]"></i></label>
                            <select class="form-control" ng-model="selectedThana[0]"
                                    ng-disabled="loadingAnsar||loadingThana[0]||loadingKPI[0]"
                                    ng-change="loadKpi()">
                                <option value="">--Select a Thana--</option>
                                <option ng-repeat="d in allThana[0]" value="[[d.id]]">[[d.thana_name_bng]]
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label"> Select a KPI&nbsp;&nbsp;&nbsp;<i
                                        class="fa fa-spinner fa-pulse" ng-show="loadingAnsar"></i></label>
                            <select class="form-control"
                                    ng-disabled="loadingAnsar||loadingThana[0]||loadingKPI[0]"
                                    ng-model="selectedKPI[0]" ng-change="loadAnsar()">
                                <option value="">--Select a KPI--</option>
                                <option ng-repeat="d in allKPI[0]" value="[[d.id]]">[[d.kpi_name]]
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="pc-table">
                            <tr>
                                <th>SL. No</th>
                                <th>ID</th>
                                <th>Designation</th>
                                <th>Name</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Kpi Name</th>
                                <th>Joining Date</th>
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
                            <tr ng-repeat="ansar in ansars" ng-if="ansars.length>0">
                                <td>[[$index+1]]</td>
                                <td>[[ansar.ansar_id]]</td>
                                <td>[[ansar.name_bng]]</td>
                                <td>[[ansar.ansar_name_bng]]</td>
                                <td>[[ansar.division_name_bng]]</td>
                                <td>[[ansar.unit_name_bng]]</td>
                                <td>[[ansar.kpi_name]]</td>
                                <td>[[formatDate(ansar.transfered_date)]]</td>
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
                        <button class="pull-right btn btn-primary" open-hide-modal
                                ng-click="modalOpen = true">
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
                                    ng-click="modalOpen = false">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="register-box" style="margin: 0;width: auto">
                                <div class="row" ng-if="showKpiStatus">
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr style="font-weight: bold;" ng-class="{'text-danger':totalKpiAnsar.pc.given<totalKpiAnsar.pc.current+tsPC,'text-success':totalKpiAnsar.pc.given>=totalKpiAnsar.pc.current+tsPC}">
                                                    <td>Total PC given : [[totalKpiAnsar.pc.given]]</td>
                                                    <td>Total PC embodied : [[totalKpiAnsar.pc.current]]</td>
                                                    <td>Total PC Transferred : [[tsPC]]</td>
                                                </tr>
                                                <tr style="font-weight: bold;" ng-class="{'text-danger':totalKpiAnsar.apc.given<totalKpiAnsar.apc.current+tsAPC,'text-success':totalKpiAnsar.apc.given>=totalKpiAnsar.apc.current+tsAPC}">
                                                    <td>Total APC given : [[totalKpiAnsar.apc.given]]</td>
                                                    <td>Total APC embodied : [[totalKpiAnsar.apc.current]]</td>
                                                    <td>Total APC Transferred : [[tsAPC]]</td>
                                                </tr>
                                                <tr style="font-weight: bold;" ng-class="{'text-danger':totalKpiAnsar.ansar.given<totalKpiAnsar.ansar.current+tsAnsar,'text-success':totalKpiAnsar.ansar.given>=totalKpiAnsar.ansar.current+tsAnsar}">
                                                    <td>Total Ansar given : [[totalKpiAnsar.ansar.given]]</td>
                                                    <td>Total Ansar embodied : [[totalKpiAnsar.ansar.current]]</td>
                                                    <td>Total Ansar Transferred : [[tsAnsar]]</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="register-box-body  margin-bottom" style="padding: 0;padding-bottom: 10px">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Transferred District
                                                    &nbsp;&nbsp;&nbsp;<i class="fa fa-spinner fa-pulse"
                                                                         ng-show="loadingThana[1]"></i></label>
                                                <select class="form-control"
                                                        ng-disabled="loadingThana[1]||loadingKPI[1]"
                                                        ng-model="selectedDistrict[1]"
                                                        ng-change="loadThana()">
                                                    <option value="">Select a district</option>
                                                    <option ng-repeat="d in allDistrict[1]"
                                                            ng-disabled="selectedDistrict[0]!=d.id" value="[[d.id]]">
                                                        [[d.unit_name_bng]]
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Transferred Thana
                                                    &nbsp;&nbsp;&nbsp;<i class="fa fa-spinner fa-pulse"
                                                                         ng-show="loadingKPI[1]"></i></label>
                                                <select class="form-control"
                                                        ng-disabled="loadingThana[1]||loadingKPI[1]"
                                                        ng-model="selectedThana[1]"
                                                        ng-change="loadKpi()">
                                                    <option value="">Select a district</option>
                                                    <option ng-repeat="d in allThana[1]" value="[[d.id]]">
                                                        [[d.thana_name_bng]]
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Transferred KPI</label>
                                                <select class="form-control"
                                                        ng-disabled="loadingThana[1]||loadingKPI[1]" check-kpi
                                                        ng-model="selectedKPI[1]">
                                                    <option value="">Select a kpi</option>
                                                    <option ng-repeat="d in allKPI[1]"
                                                            ng-disabled="selectedKPI[0]==d.id" value="[[d.id]]">
                                                        [[d.kpi_name]]
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                            ng-show="isVerifying"><i class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span>
                                                    <span class="text-danger"
                                                          ng-if="isVerified">This id already taken</span></label>
                                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                                       type="text" class="form-control" name="memorandum_id"
                                                       placeholder="Enter memorandum id">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Joining date in transfered kpi.</label>
                                                <input type="text" ng-model="joinDate" id="join_date_in_tk"
                                                       class="form-control"
                                                       name="memorandum_id">
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
                                                <th>Kpi Name</th>
                                                <th>Joining Date</th>
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
                                                <td>[[formatDate(ansar.joining_date)]]</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary pull-right" open-hide-modal
                                            ng-disabled="selectedAnsar.length<=0||!memorandumId||!joinDate||!selectedKPI[1]||isVerified||isVerifying||(tsPC>0&&totalKpiAnsar.pc.given<totalKpiAnsar.pc.current+tsPC)||(tsAPC>0&&totalKpiAnsar.apc.given<totalKpiAnsar.apc.current+tsAPC)||(tsAnsar>0&&totalKpiAnsar.ansar.given<totalKpiAnsar.ansar.current+tsAnsar)"
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