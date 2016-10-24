{{--Ansar Transfer Complete--}}

@extends('template.master')
@section('title','Transfer Ansars')
@section('breadcrumb')
    {!! Breadcrumbs::render('multiple_transfer') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TransferController', function ($scope, $http) {
            $scope.ansar_id = '';
            $scope.selectedUnit = ''
            $scope.transfering = false;
            $scope.printLetter = false;
            $scope.memId = '';
            $scope.submitData = [];
            $scope.search = false;
            $scope.units = [];
            $scope.thanas = [];
            $scope.kpis = [];
            $scope.tAnsars = [];
            $scope.formData = {
                unit: '',
                thana: '',
                kpi: '',
                joining_date: ''
            }
            $scope.searchAnsar = function (event) {
                if (event.type == 'keypress' && event.which != 13) return;
                $scope.search = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('search_kpi_by_ansar')}}',
                    data: {ansar_id: $scope.ansar_id,unit:$scope.selectedUnit}
                }).then(function (response) {
                    console.log(response.data)
                    $scope.search = false;
                    $scope.data = response.data;
                }, function (response) {

                })
            }
            $scope.addToCart = function () {
                //alert(JSON.stringify($scope.formData.kpi));
                var d = {
                    id: $scope.data.data.ansar_id,
                    name: $scope.data.data.ansar_name_eng,
                    tkn: $scope.kpis.find(function (v) {
                        return v.id == $scope.formData.kpi;
                    }),
                    ckn: $scope.data.data.kpi_name,
                    tkjd: $scope.formData.joining_date
                }
                var s = $scope.tAnsars.find(function (v) {
                    return v.id == d.id;
                })
                if (s) {
                    alert("This ansar already in transfer list");
                    return;
                }
                var b = {
                    ansarId: $scope.data.data.ansar_id,
                    currentKpi: $scope.data.data.kpi_id,
                    transferKpi: $scope.formData.kpi,
                    tKpiJoinDate: $scope.formData.joining_date
                }
                $scope.submitData.push(b);
                $scope.tAnsars.push(d)
                $scope.data.data = {}
                $scope.data.status = false;
                $scope.ansar_id = '';
                $scope.formData = {
                    unit: '',
                    thana: '',
                    kpi: '',
                    joining_date: ''
                }
            }
            @if(Auth::user()->type==11||Auth::user()->type==33||Auth::user()->type==66)
            $scope.loadDistrict = function () {
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.units = response.data;
                    $scope.thanas = [];
                    $scope.kpis = [];
                    $scope.formData.kpi = "";
                    $scope.formData.thana = "";
                    $scope.loadingDistrict = false;
                })
            }
            $scope.loadDistrict();
            @endif
            $scope.loadThana = function (id) {
                $scope.ansar_id = '';
                $scope.data = ''
                $scope.kpis = [];
                $scope.formData.kpi = "";
                $scope.formData.joining_date = "";
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.thanas = response.data;

                    $scope.formData.thana = "";

                    $scope.loadingThana = true;
                })
            }
            $scope.loadGuard = function (id) {
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.loadingThana = false;
                    $scope.kpis = response.data;
                    $scope.formData.kpi = "";
                    //$scope.loadingKpi=true;
                })
            }
            $scope.transferAnsar = function () {
                $scope.error = undefined;
                $scope.transfering = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('confirm_transfer')}}',
                    data: angular.toJson({ansars: $scope.submitData, memId: $scope.memId})
                }).then(function (response) {
                    $('body').notifyDialog({
                        type: 'success',
                        message: response.data.message
                    }).showDialog();
                    $scope.tm = response.data.memId
                    $scope.uid = angular.copy($scope.selectedUnit);
                    $scope.transfering = false;
                    $scope.printLetter = true;
                    reset();
                }, function (response) {
                    $scope.error = response.data;
                    if ($scope.error.message) {
                        $('body').notifyDialog({
                            type: 'error',
                            message: response.data.message
                        }).showDialog();
                        reset();
                    }
                    $scope.transfering = false;
                })
            }
            $scope.remove = function (i) {
                $scope.submitData.splice(i, 1);
                $scope.tAnsars.splice(i, 1);
            }
            function reset() {
                $scope.submitData = [];
                $scope.tAnsars = [];
                $scope.ansar_id = '';
                $scope.data = '';
                $scope.memId = ''
            }

            @if(Auth::user()->type==22)
            $scope.selectedUnit = '{{Auth::user()->district_id}}'
            $scope.loadThana('{{Auth::user()->district_id}}')
            @endif


        })
    </script>
    <div ng-controller="TransferController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('transfer') !!}--}}

        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="transfering">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if(Auth::user()->type==11||Auth::user()->type==33||Auth::user()->type==66)
                                <div class="form-group">
                                    <lable class="control-label"
                                           style="font-weight: bold;margin-bottom: 5px;display: block">Select District
                                    </lable>
                                    <select name="unit"
                                            ng-model="selectedUnit" ng-change="loadThana(selectedUnit)"
                                            class="form-control">
                                        <option value="">--Select a district--</option>
                                        <option ng-repeat="unit in units" value="[[unit.id]]">[[unit.unit_name_eng]]
                                        </option>
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <h4> Enter ansar id to transfer</h4>
                                <div class="input-group">
                                    <input type="text" name="ansar_id" ng-keypress="searchAnsar($event)"
                                           ng-disabled="!selectedUnit"
                                           ng-model="ansar_id" placeholder="Ansar id" class="form-control">
                                    <span class="input-group-btn">
                                        <button class="btn btn-secondary" ng-click="searchAnsar($event)">
                                            <i class="fa fa-search" ng-if="!search"></i>
                                            <i class="fa fa-spinner fa-pulse" ng-if="search"></i>
                                        </button>
                                    </span>
                                </div>
                                <p class="text text-danger" ng-if="data.status==0">
                                    [[data.messages[0] ]]
                                </p>
                                <ul ng-if="data.status" style="list-style: none;margin-top: 10px">
                                    <li>
                                        <h4 style="text-decoration: underline">Ansar Name</h4>
                                        [[data.data.ansar_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline">Kpi Name</h4>
                                        [[data.data.kpi_name]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline">Kpi Unit</h4>
                                        [[data.data.unit_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline">Kpi Thana</h4>
                                        [[data.data.thana_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline">Joining Date</h4>
                                        [[data.data.joining_date|dateformat:"DD-MMM-YYYY"]]
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h4>Transfer Option</h4>

                            <div class="form-group">
                                <lable class="control-label"
                                       style="font-weight: bold;margin-bottom: 5px;display: block">Select Thana
                                </lable>
                                <select name="thana" ng-disabled="data==undefined||!data.status||!selectedUnit"
                                        ng-model="formData.thana" ng-change="loadGuard(formData.thana)"
                                        class="form-control">
                                    <option value="">--Select a thana--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_eng]]</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <lable class="control-label"
                                       style="font-weight: bold;margin-bottom: 5px;display: block">Select Kpi
                                </lable>
                                <select name="kpi" ng-disabled="data==undefined||!data.status||!selectedUnit"
                                        ng-model="formData.kpi"
                                        class="form-control">
                                    <option value="">--Select a kpi--</option>
                                    <option ng-repeat="k in kpis" ng-value="k.id" ng-disabled="data.data.kpi_id==k.id">
                                        [[k.kpi_name]]
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <lable class="control-label"
                                       style="font-weight: bold;margin-bottom: 5px;display: block">Joining Date
                                </lable>
                                <input type="text" id="datepicker" ng-disabled="data==undefined||!data.status"
                                       ng-model="formData.joining_date" placeholder="Joining Date"
                                       class="form-control" name="joining_date">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" ng-click="addToCart()"
                                        ng-disabled="data==undefined||!data.status||!formData.kpi||!formData.thana||!selectedUnit||!formData.joining_date">
                                    <i class="fa fa-plus"></i>&nbsp;Add to transfer list
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>#</th>
                                        <th>Ansar Id</th>
                                        <th>Name</th>
                                        <th>Current Kpi Name</th>
                                        <th>Transfer Kpi Name</th>
                                        <th>Transfer Kpi Joining Date</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr ng-if="tAnsars.length>0" ng-repeat="t in tAnsars">
                                        <td>[[$index+1]]</td>
                                        <td>[[t.id]]</td>
                                        <td>[[t.name]]</td>
                                        <td>[[t.ckn]]</td>
                                        <td>[[t.tkn.kpi_name]]</td>
                                        <td>[[t.tkjd]]</td>
                                        <td>
                                            <button class="btn btn-danger btn-xs" ng-click="remove($index)">
                                                <i class="fa fa-remove"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr ng-if="tAnsars.length<=0">
                                        <td colspan="5">No Ansar Available</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" style="margin-bottom: 10px">
                            <input type="text" name="mem_id" ng-model="memId" placeholder="Enter memorandum no"
                                   class="form-control">

                            <p ng-if="error!=undefined&&error.memId!=undefined" class="text text-danger">
                                [[error.memId[0] ]]
                            </p>
                        </div>
                        <div class="col-md-8">
                            <button ng-disabled="!memId||submitData.length<=0" class="btn btn-primary btn-md"
                                    ng-click="transferAnsar()">Transfer
                            </button>
                            <a ng-if="printLetter" target="_blank" class="btn btn-primary" style="margin-left: 10px"
                               href="{{URL::to('HRM/print_letter')}}?id=[[tm]]&unit=[[uid]]&type=TRANSFER&view=full">Print
                                Letter</a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
    <script>
        $("#datepicker").datePicker();
    </script>
@stop