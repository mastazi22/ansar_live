{{--User: Shreya--}}
{{--Date: 10/15/2015--}}
{{--Time: 10:49 AM--}}

@extends('template.master')
@section('title','Panel')
@section('small_title')
    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#panel-modal"><span
                class="glyphicon glyphicon-save"></span> Load Ansars
    </button>
@endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('panel_information') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("PanelController", function ($scope, $http, notificationService) {

            $scope.joinDate = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.loading = {
                loading_ansar_for_panel:false,
                loading_add_to_panel:false
            };
            $scope.ansarsForPanel = [];
            $scope.formData = {merit: [], ch: []};
            $scope.panelFormData = {
                type:1
            };
            $scope.panelData = [];
            $scope.submitEntryPanelData = {};
            $scope.checkAll = false;
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.submitEntryPanelData.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }
            $scope.loadForPanel = function () {
                $scope.loading.loading_ansar_for_panel = true;
                $http({
                    url: '{{URL::route('select_status')}}',
                    method: 'get',
                    params: $scope.panelFormData
                }).then(function (response) {
                    console.log(response.data);
                    $scope.ansarLoaderror = undefined;
                    $scope.ansarsForPanel = response.data;
                    initializeArray();
                    $scope.loading.loading_ansar_for_panel = false;
                    $("#panel-modal").modal('hide');
                    $scope.panelFormData = {type:1};
                }, function (response) {
                    $scope.ansarLoaderror = response.data;
                    $scope.loading.loading_ansar_for_panel = false;
                })

            }
            $scope.$watch('formData.ch', function (newValue, oldValue) {
                if (newValue.length > 0) {
                    newValue.forEach(function (value, key, array) {
                        if (oldValue[key] != value) {
                            addAnsarForPanel(key);
                            console.log(key);
                        }

                    })
                    $scope.checkAll = newValue.every(function (value) {

                        return value == true;
                    });
                }

            }, true)
            function addAnsarForPanel(i) {
                if ($scope.formData.ch[i]) {
                    var b = $scope.ansarsForPanel[i];
                    b["merit"] = $scope.formData.merit[i];
                    $scope.panelData.push(b);
                }
                else {
                    var b = $scope.ansarsForPanel[i];
                    $scope.panelData.splice($scope.panelData.indexOf(b), 1);
                }
                console.log($scope.panelData);
            }
            function initializeArray(){
                $scope.formData.ch = Array.apply(null, Array($scope.ansarsForPanel.length)).map(Boolean.prototype.valueOf, false);
            }
            $scope.changeAll = function () {
                $scope.formData.ch = Array.apply(null, Array($scope.formData.ch.length)).map(Boolean.prototype.valueOf, $scope.checkAll);
            }
            $scope.submitPanelEntry = function () {
                console.log($scope.submitEntryPanelData);
                $scope.loading.loading_add_to_panel = true;
                $http({
                    url: '{{URL::route('save-panel-entry')}}',
                    method: 'post',
                    data: angular.toJson($scope.submitEntryPanelData)
                }).then(function (response) {
                    $scope.add_to_panel_error = undefined;
                    console.log(response.data);
                    $scope.loading.loading_add_to_panel = false;
                    $scope.result = response.data;
                    $scope.submitEntryPanelData = {};
                    $scope.alerts = [];
                    if($scope.result.status){
                        $("#confirm-panel-modal").modal('hide');
                        initializeArray();
//                        alert('asassasas')
                        notificationService.notify('success',$scope.result.message)
                        removeData();
                    }
                    else{
                        notificationService.notify('error',$scope.result.message)
//                        $scope.alerts.push({type:'error',message:$scope.result.message})
                    }
                }, function (response) {
                    notificationService.notify('error',"An unknown error occur. Error code : "+response.status)
                    $scope.loading.loading_add_to_panel = false;
                })
            }
            $scope.closeAlert = function () {
                $scope.alerts = [];
            }
            function removeData(){
                $scope.panelData.forEach(function (value,key,array) {
                    var index = $scope.ansarsForPanel.indexOf(value);
                    $scope.ansarsForPanel.splice(index,1);
                })
                $scope.panelData = [];
            }
        })
        GlobalApp.directive('openHideModal', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {
                    $(elem).on('click', function () {
                        console.log(scope.formData);
                        $("#confirm-panel-modal").modal("toggle")
                    })
                }
            }
        })
    </script>

    <div ng-controller="PanelController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('panel_information') !!}--}}
        {{--</div>--}}
        <!-- Content Header (Page header) -->

        <!-- Main content -->

        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <br>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="pc-table">

                                    <tr>
                                        <th>Ansar ID</th>
                                        <th>Ansar Name</th>
                                        <th>Ansar Rank</th>
                                        <th>Ansar Unit</th>
                                        <th>Ansar Thana</th>
                                        <th>Date of Birth</th>
                                        <th>Sex</th>
                                        <th>Merit List</th>
                                        <th>
                                            <div class="styled-checkbox">
                                                <input type="checkbox" ng-model="checkAll" id="check-all-panel"
                                                       ng-change="changeAll()">
                                                <label for="check-all-panel"></label>
                                            </div>
                                        </th>
                                        {{--<th><input type="checkbox" id="select-all-panel" name="" value=""--}}
                                        {{--style="height: 20px; width: 25px"> Select All--}}
                                        {{--</th>--}}
                                    </tr>
                                    <tr ng-if="ansarsForPanel.length>0" ng-repeat="a in ansarsForPanel">
                                        <td>[[a.ansar_id]]</td>
                                        <td>[[a.ansar_name_eng]]</td>
                                        <td>[[a.name_eng]]</td>
                                        <td>[[a.unit_name_eng]]</td>
                                        <td>[[a.thana_name_eng]]</td>
                                        <td>[[a.data_of_birth]]</td>
                                        <td>[[a.sex]]</td>
                                        <td ng-init="formData.merit[$index]=1">
                                            <input size="4x5" ng-model="formData.merit[$index]">
                                        </td>
                                        <td>
                                            <div class="styled-checkbox">
                                                <input type="checkbox" ng-model="formData.ch[$index]"
                                                       ng-change="addAnsarForPanel($index)" id="a_[[a.ansar_id]]"
                                                       name="ch[]" class="check-panel"
                                                       value="a_[[a.ansar_id]]">
                                                <label for="a_[[a.ansar_id]]"></label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="warning" ng-if="ansarsForPanel.length<=0">
                                        <td colspan="9">No Ansar found</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-info btn-sm pull-right" id="confirm-panel" open-hide-modal>Add to Panel
                    </button>
                </div>
            </div>
            <!-- /.box
            -footer -->
            <!--Modal Open-->
            <div id="panel-modal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title">Panel Options</h3>
                        </div>
                        <div class="modal-body">
                            <div class="box box-solid" style="border-top: none !important;">
                                <div class="overlay" ng-if="loading.loading_ansar_for_panel">
                                    <span class="fa">
                                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                                    </span>
                                </div>
                                <div class="box-body">
                                    <form role="form" id="load_ansar_for_panel" ng-submit="loadForPanel()">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group required">
                                                    <label class="control-label"> Select a Status</label>
                                                    <select name='come_from_where' ng-model="panelFormData.come_from_where" id='come_from_where' class="form-control" ng-change="submitEntryPanelData.come_from_where=panelFormData.come_from_where">
                                                        <option value="" disabled selected>--Select a Status--
                                                        </option>
                                                        <option value="1">Rest Status</option>
                                                        <option value="2">Free Status</option>
                                                    </select>
                                                    <p ng-if="ansarLoaderror.come_from_where!=undefined" class="text text-danger">[[ansarLoaderror.come_from_where[0] ]]</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="" class="control-label">&nbsp;</label>
                                                    <div>
                                                        <label style="padding:5px 10px">
                                                            <input type="radio" name="type" style="vertical-align: top" ng-model="panelFormData.type" ng-value="1">Multiple
                                                        </label>
                                                        <label style="padding:5px 10px">
                                                            <input type="radio" name="type" style="vertical-align: top" ng-model="panelFormData.type" ng-value="2">Single
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row" ng-if="panelFormData.type==1">
                                            <div class="col-md-3">
                                                <div class="form-group required">
                                                    <label class="control-label">From (ID)</label>
                                                    <input type="text" ng-model="panelFormData.from_id"
                                                           name="from-id" class="form-control"
                                                           placeholder="Ansar ID">
                                                    <p ng-if="ansarLoaderror.from_id!=undefined" class="text text-danger">[[ansarLoaderror.from_id[0] ]]</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 ">
                                               <div class="form-group required">
                                                   <label class="control-label">To (ID)</label>
                                                   <input type="text" ng-model="panelFormData.to_id"
                                                          name="to-id" class="form-control"
                                                          placeholder="Ansar ID">
                                                   <p ng-if="ansarLoaderror.to_id!=undefined" class="text text-danger">[[ansarLoaderror.to_id[0] ]]</p>
                                               </div>
                                            </div>
                                            <div class=" form-group col-sm-6 required">
                                                <label class="control-label">Select no. of Ansars to Load</label>
                                                <select class="form-control" ng-model="panelFormData.ansar_num"
                                                        name="ansar_num" id="count-ansar">
                                                    <option value="">--Select--</option>
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="30">30</option>
                                                    <option value="40">40</option>
                                                    <option value="50">50</option>
                                                    <option value="60">60</option>
                                                    <option value="70">70</option>
                                                    <option value="80">80</option>
                                                    <option value="90">90</option>
                                                    <option value="100">100</option>
                                                </select>
                                                <p ng-if="ansarLoaderror.ansar_num!=undefined" class="text text-danger">[[ansarLoaderror.ansar_num[0] ]]</p>
                                            </div>
                                        </div>
                                        <div class="row" ng-if="panelFormData.type==2">
                                            <div class="col-sm-6 col-centered">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Ansar ID" ng-model="panelFormData.ansar_id">
                                                    <p ng-if="ansarLoaderror.ansar_id!=undefined" class="text text-danger">[[ansarLoaderror.ansar_id[0] ]]</p>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-info pull-right" id="load-panel">
                                            <i class="fa fa-download"></i> Load
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Modal Close-->
            <!--Modal Open-->
            <div id="confirm-panel-modal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;</button>
                            <h4 class="modal-title">Confirmation for Adding Ansars to Panel</h4>
                        </div>
                        <div class="modal-body">
                            <div class="box box-solid " style="border-top: none !important;">
                                <div class="overlay" ng-if="loading.loading_add_to_panel">
                                    <span class="fa">
                                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                                    </span>
                                </div>
                                <div class="box-body">
                                    <form ng-submit='submitPanelEntry()'>

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                                    ng-show="isVerifying">
                                                        <i class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span>
                                                            <span class="text-danger" ng-if="isVerified&&!memorandumId">Memorandum no. is required.</span>
                                                            <span class="text-danger" ng-if="isVerified&&memorandumId">This id already taken.</span>
                                                        </label>
                                                        <input ng-blur="verifyMemorandumId()" ng-model="submitEntryPanelData.memorandumId" type="text" class="form-control" name="memorandum_id" placeholder="Enter Memorandum no." required>
                                                        <p ng-if="add_to_panel_error.memorandumId!=undefined" class="text text-danger">[[add_to_panel_error.memorandumId[0] ]]</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Panel Date</label>
                                                        {!! Form::text('panel_date', $value = null, $attributes = array('class' => 'form-control','date-picker', 'id' => 'panel_date', 'ng_model' => 'submitEntryPanelData.panel_date','placeholder'=>'Panel Date', 'required')) !!}
                                                        <p ng-if="add_to_panel_error.panel_date!=undefined" class="text text-danger">[[add_to_panel_error.panel_date[0] ]]</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <input type="hidden" ng-model="submitEntryPanelData.come_from_where" >
                                                <table class="table table-bordered" id="pc-table">
                                                    <tr>
                                                        <th>Ansar ID</th>
                                                        <th>Ansar Name</th>
                                                        <th>Ansar Rank</th>
                                                        <th>Ansar Unit</th>
                                                        <th>Ansar Thana</th>
                                                        <th>Date of Birth</th>
                                                        <th>Sex</th>
                                                        <th>Merit List</th>
                                                    </tr>
                                                    <tr ng-if="panelData.length>0" ng-repeat="p in panelData">
                                                        <td ng-init="submitEntryPanelData.ansar_id[$index]=p.ansar_id">
                                                            [[p.ansar_id]]
                                                            <input type="hidden" ng-model="submitEntryPanelData.ansar_id[$index]">
                                                        </td>
                                                        <td>[[p.ansar_name_eng]]</td>
                                                        <td>[[p.name_eng]]</td>
                                                        <td>[[p.unit_name_eng]]</td>
                                                        <td>[[p.thana_name_eng]]</td>
                                                        <td>[[p.data_of_birth]]</td>
                                                        <td>[[p.sex]]</td>
                                                        <td ng-init="submitEntryPanelData.merit[$index]=p.merit">
                                                            [[p.merit]]
                                                            <input type="hidden"
                                                                   ng-model="submitEntryPanelData.merit[$index]">
                                                        </td>
                                                    </tr>
                                                    <tr ng-if="panelData.length<=0" class="warning">
                                                        <td colspan="9">No Ansar Found to Withdraw</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button class="btn btn-primary pull-right" id="confirm-panel-entry"
                                                    ng-disabled="!submitEntryPanelData.panel_date||!submitEntryPanelData.memorandumId||isVerified||isVerifying">
                                                <i class="fa fa-check"></i>&nbsp;Confirm
                                            </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!--Modal Close-->
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection