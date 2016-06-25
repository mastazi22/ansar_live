@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller("KpiReduceController", function ($scope,$http,$timeout) {
            $scope.memorandumId = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.modalOpen = false;
            $scope.selectAll = false;
            $scope.showDialog = false;
            var userType = parseInt("{{Auth::user()->type}}");
            $scope.isDC = userType==22?true:false;
            $scope.loadDistrict = function () {
                $http({
                    url:"{{action('FormSubmitHandler@DistrictName')}}",
                    method:'get'
                }).then(function (response) {
                    if($scope.modalOpen)$scope.allDistrict[1] = response.data;
                    else $scope.allDistrict[0] = response.data;
                }, function (response) {

                })
            }
            $scope.loadThana = function(){
                if($scope.modalOpen) $scope.loadingThana[1] = true;
                else $scope.loadingThana[0] = true;
                $http({
                    url:"{{action('FormSubmitHandler@ThanaName')}}",
                    method:'get',
                    params:{id:$scope.modalOpen?$scope.selectedDistrict[1]:$scope.selectedDistrict[0]}
                }).then(function(response){
                    if($scope.modalOpen) {
                        $scope.allThana[1] = response.data;
                        $scope.loadingThana[1] = false;
                        $scope.selectedThana[1] = "";
                    }
                    else {
                        $scope.allThana[0] = response.data;
                        $scope.loadingThana[0] = false;
                        $scope.selectedThana[0] = "";
                    }
                },function(response){
                    $scope.loadingThana = false;
                })
            }
            $scope.loadKpi = function(){
                if($scope.modalOpen) $scope.loadingKPI[1] = true;
                else $scope.loadingKPI[0] = true;
                $http({
                    url:"{{action('EmbodimentController@kpiName')}}",
                    method:'get',
                    params:{id:$scope.modalOpen?$scope.selectedThana[1]:$scope.selectedThana[0]}
                }).then(function(response){

                    if($scope.modalOpen){
                        $scope.allKPI[1] = response.data;
                        $scope.loadingKPI[1] = false;
                        $scope.selectedKPI[1] = "";
                    }
                    else{
                        $scope.allKPI[0] = response.data;
                        $scope.loadingKPI[0] = false;
                        $scope.selectedKPI[0] = "";
                    }
                },function(response){
                    $scope.loadingKPI = false;
                })
            }
            $scope.loadAnsar = function(){
                $scope.loadingAnsar = true;
                $http({
                    url:"{{action('EmbodimentController@getEmbodiedAnsarOfKpi')}}",
                    method:'get',
                    params:{kpi_id:$scope.selectedKPI[0]}
                }).then(function(response){
                    $scope.ansars = response.data;
                    $scope.selectAnsar = new Array($scope.ansars.length);
                    $scope.loadingAnsar = false;
                    $scope.selectAll = false;
                },function(response){
                    $scope.loadingAnsar = false;
                })
            }
            $scope.$watch(function(scope){
                return scope.modalOpen;
            }, function (n,o) {
                if(!$scope.isDC){
                    if($scope.allDistrict[0]==null||$scope.allDistrict[1]==null) $scope.loadDistrict();
                }
                else{
                    $scope.selectedDistrict[0] = $scope.selectedDistrict[1] = parseInt("{{Auth::user()->district_id}}");
                    if($scope.allThana[0]==null||$scope.allThana[1]==null) $scope.loadThana();
                }
            })
            $scope.changeSelectAnsar = function(i){
                var index = $scope.selectedAnsar.indexOf($scope.ansars[i]);
                if($scope.selectAnsar[i]){
                    if(index==-1){
                        $scope.selectedAnsar.push($scope.ansars[i])
                    }
                }
                else{
                    $scope.selectedAnsar.splice(index,1);
                }
                $scope.selectAll = $scope.selectedAnsar.length==$scope.ansars.length;
            }
            $scope.$watch('selectAnsar',function(n,o){
                n.forEach(function(e,i,a){
                    $scope.changeSelectAnsar(i);
                })
            })
            $scope.changeSelectAll = function(){
                //alert($scope.selectAll)
                $scope.selectAnsar = Array.apply(null,new Array($scope.ansars.length)).map(Boolean.prototype.valueOf,$scope.selectAll);
            }
            $scope.confirmTransferAnsar = function () {
                var data = {
                    memorandum_id:$scope.memorandumId,
                    transfer_date:$scope.joinDate,
                    kpi_id:$scope.selectedKPI,
                    transferred_ansar:$scope.selectedAnsar
                }
                $http.post('{{action('UserController@completeTransferProcess')}}',data).then(function (response) {
                    $scope.showDialog = true;
                    $scope.requestStatus = response.data;
                    $timeout(function(){$scope.showDialog=false},3000);
                }, function (response) {

                })
            }
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id:$scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}',data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }

        })
    </script>
    <div class="content-wrapper" style="min-height: 490px" ng-controller="TransferController">
        <section class="content">
            <div class="box box-solid" style="min-height: 200px; max-height: 490px">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#pc">Transfer Ansar</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="offer-table">

                            <div class="row" style="padding-bottom: 10px">
                                <div class="col-md-4" ng-if="!isDC">
                                    <label class="control-label"> Select a district&nbsp;&nbsp;&nbsp;<i
                                                class="fa fa-spinner fa-pulse" ng-show="loadingThana[0]"></i></label>
                                    <select class="form-control" ng-model="selectedDistrict[0]" ng-change="loadThana()">
                                        <option value="">--Select a District--</option>
                                        <option ng-repeat="d in allDistrict[0]" value="[[d.id]]">[[d.unit_name_bng]]
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label"> Select a Thana&nbsp;&nbsp;&nbsp;<i
                                                class="fa fa-spinner fa-pulse" ng-show="loadingKPI[0]"></i></label>
                                    <select class="form-control" ng-model="selectedThana[0]" ng-change="loadKpi()">
                                        <option value="">--Select a Thana--</option>
                                        <option ng-repeat="d in allThana[0]" value="[[d.id]]">[[d.thana_name_bng]]
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label"> Select a Kpi&nbsp;&nbsp;&nbsp;<i
                                                class="fa fa-spinner fa-pulse" ng-show="loadingAnsar"></i></label>
                                    <select class="form-control" ng-model="selectedKPI[0]" ng-change="loadAnsar()">
                                        <option value="">--Select a Kpi--</option>
                                        <option ng-repeat="d in allKPI[0]" value="[[d.id]]">[[d.kpi_name]]
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="pc-table">
                                    <tr class="info">
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
                                                <input ng-disabled="ansars.length<=0" type="checkbox" id="all" ng-change="changeSelectAll()" ng-model="selectAll">
                                                <label for="all"></label>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr class="warning" ng-if="ansars.length<=0">
                                        <td colspan="9">No Ansar Found to Transfer</td>
                                    </tr>
                                    <tr ng-repeat="ansar in ansars" ng-if="ansars.length>0">
                                        <td>[[$index+1]]</td>
                                        <td ansar-id="[[ansar.ansar_id]]">[[ansar.ansar_id]]</td>
                                        <td>[[ansar.ansar_name_bng]]</td>
                                        <td>[[ansar.name_bng]]</td>
                                        <td>[[ansar.division_name_bng]]</td>
                                        <td>[[ansar.unit_name_bng]]</td>
                                        <td>[[ansar.kpi_name]]</td>
                                        <td>[[ansar.joining_date]]</td>
                                        <td>
                                            <div class="styled-checkbox">
                                                <input type="checkbox" id="a_[[ansar.ansar_id]]" ng-change="changeSelectAnsar($index)" ng-model="selectAnsar[$index]">
                                                <label for="a_[[ansar.ansar_id]]"></label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
                <button class="pull-right btn btn-primary" data-toggle="modal" data-target="#transfer-option" ng-click="modalOpen = true">
                    <i class="fa fa-send"></i>&nbsp;&nbsp;Transfer
                </button>
            </div>
            <div id="transfer-option" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 70% !important;margin: 0 auto !important;margin-top: 20px !important;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <strong>Transfer Option</strong>
                            <button type="button" class="close" data-dismiss="modal" ng-click="modalOpen = false">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="register-box" style="margin: 0;width: auto">
                                <div class="register-box-body  margin-bottom" style="padding: 0;padding-bottom: 10px">
                                    <div  class="fade" ng-class="{in:showDialog,'':!showDialog,'alert-success':requestStatus.status,'alert-danger':!requestStatus.status}" style="position:absolute;width: 34%;height:30px;margin:0 33%;z-index: 5;padding: 5px 10px;border-radius: 5px" >
                                        <i class="fa" ng-class="{'fa-check':requestStatus.status, 'fa-warning':!requestStatus.status}"></i>&nbsp;[[requestStatus.message]]
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4" ng-if="!isDC">
                                            <div class="form-group">
                                                <label class="control-label">Transferred District
                                                    &nbsp;&nbsp;&nbsp;<i class="fa fa-spinner fa-pulse" ng-show="loadingThana[1]"></i></label>
                                                <select class="form-control" ng-model="selectedDistrict[1]" ng-change="loadThana()">
                                                    <option value="">Select a district</option>
                                                    <option ng-repeat="d in allDistrict[1]" value="[[d.id]]">[[d.unit_name_bng]]</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Transferred Thana
                                                    &nbsp;&nbsp;&nbsp;<i class="fa fa-spinner fa-pulse" ng-show="loadingKPI[1]"></i></label>
                                                <select class="form-control" ng-model="selectedThana[1]" ng-change="loadKpi()">
                                                    <option value="">Select a district</option>
                                                    <option ng-repeat="d in allThana[1]" value="[[d.id]]">[[d.thana_name_bng]]</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Transferred KPI</label>
                                                <select class="form-control" ng-model="selectedKPI[1]">
                                                    <option value="">Select a kpi</option>
                                                    <option ng-repeat="d in allKPI[1]" value="[[d.id]]">[[d.kpi_name]]</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span ng-show="isVerifying"><i class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span class="text-danger" ng-if="isVerified">This id already taken</span></label>
                                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId" type="text" class="form-control" name="memorandum_id" placeholder="Enter memorandum id">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Joining date in transfered kpi.</label>
                                                <input type="date" ng-model="joinDate" class="form-control" name="memorandum_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive" >
                                        <table class="table table-bordered" style="max-height: 200px">
                                            <tr class="info">
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
                                                <td ansar-id="[[ansar.ansar_id]]">[[ansar.ansar_id]]</td>
                                                <td>[[ansar.ansar_name_bng]]</td>
                                                <td>[[ansar.name_bng]]</td>
                                                <td>[[ansar.division_name_bng]]</td>
                                                <td>[[ansar.unit_name_bng]]</td>
                                                <td>[[ansar.kpi_name]]</td>
                                                <td>[[ansar.joining_date]]</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary pull-right" ng-disabled="selectedAnsar.length<=0||!memorandumId||!joinDate||!selectedKPI[1]||isVerified||isVerifying" ng-click="confirmTransferAnsar()">
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