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
        GlobalApp.controller("PanelController", function ($scope, $http, $sce, $compile) {

            $scope.joinDate = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.loading = false;
            $scope.ansarsForPanel = [];
            $scope.formData = {merit: [], ch: []};
            $scope.panelFormData = {};
            $scope.panelData = [];
            $scope.submitEntryPanelData = {

            };
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
                $http({
                    url: '{{URL::route('select_status')}}',
                    method: 'get',
                    params: $scope.panelFormData
                }).then(function (response) {
                    console.log(response.data);
                    $scope.ansarsForPanel = response.data;
                    $scope.formData.ch = Array.apply(null, Array($scope.ansarsForPanel.length)).map(Boolean.prototype.valueOf, false);
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

            $scope.changeAll = function () {
                $scope.formData.ch = Array.apply(null, Array($scope.formData.ch.length)).map(Boolean.prototype.valueOf, $scope.checkAll);
            }
            $scope.submitPanelEntry = function () {
                console.log($scope.submitEntryPanelData);
                $http({
                    url: '{{URL::route('save-panel-entry')}}',
                    method: 'post',
                    data: angular.toJson($scope.submitEntryPanelData)
                }).then(function (response) {
                    console.log(response.data);
                }, function (response) {

                })
            }
        })
        GlobalApp.directive('openHideModal', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {
                    $(elem).on('click', function () {
                        console.log(scope.formData);
//                        //alert("hh")
//                        scope.memorandumId = "";
//                        scope.panel_date = "";
//                        scope.$digest()
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
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        {!! csrf_field() !!}
        <section class="content">

            <div class="box box-solid">
                <div class="overlay" ng-if="loading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
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
                    <div class="box-info modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title">Panel Options</h3>
                        </div>
                        <div class="modal-body">
                            <div class="offer-loading" ng-show="showLoadingScreen">
                                <i class="fa fa-spinner fa-pulse fa-2x" style="position: relative;left:48%;top:40%"></i>
                            </div>
                            <div class="box" style="border-top: none;">
                                <div class="box-body">
                                    <form role="form" id="load_ansar_for_panel" ng-submit="loadForPanel()">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group required">
                                                            <label class="control-label"> Select a Status</label>
                                                            <select name='come_from_where' ng-model="panelFormData.come_from_where" id='come_from_where' class="form-control" ng-change="submitEntryPanelData.come_from_where=panelFormData.come_from_where">
                                                                <option value="" disabled selected>--Select a Status--
                                                                </option>
                                                                <option value="1">Rest Status</option>
                                                                <option value="2">Free Status</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group required">
                                                    <ul style="list-style: none; margin-left: 0 !important;padding: 0"
                                                        class="row custom-selected">
                                                        <li class="form-group col-md-5 custom-selected" id="from-id">
                                                            <label class="control-label">From (ID)</label>
                                                            <input type="text" ng-model="panelFormData.from_id"
                                                                   name="from-id" class="form-control"
                                                                   placeholder="Ansar ID">
                                                        </li>
                                                        <li class="col-sm-1"
                                                            style="text-align: center;font-size: 1.2em;padding: 0;width: auto;">
                                                            <label style="display: block">&nbsp;</label>
                                                            to
                                                        </li>
                                                        <li class="form-group required col-md-5" id="to_id">
                                                            <label class="control-label">To (ID)</label>
                                                            <input type="text" ng-model="panelFormData.to_id"
                                                                   name="to-id" class="form-control"
                                                                   placeholder="Ansar ID">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
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
                <div class="modal-dialog" style="width: 80%;overflow: auto;">
                    <div class="box-info modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;</button>
                            <h3 class="modal-title">Confirmation for Adding Ansars to Panel</h3>
                        </div>
                        <form ng-submit='submitPanelEntry()'>
                            <div class="modal-body">
                                <div class="register-box" style="width: auto;margin: 0">
                                    <div class="register-box-body  margin-bottom">

                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                                ng-show="isVerifying">
                                                        <i class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span>
                                                        <span class="text-danger" ng-if="isVerified&&!memorandumId">Memorandum ID is required.</span>
                                                        <span class="text-danger" ng-if="isVerified&&memorandumId">This id already taken.</span>
                                                    </label>
                                                    <input ng-blur="verifyMemorandumId()" ng-model="submitEntryPanelData.memorandumId" type="text" class="form-control" name="memorandum_id" placeholder="Enter Memorandum no." required>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label">Panel Date <span class="text-danger"
                                                                                                  ng-show="panelForm.panel_date.$touched && panelForm.panel_date.$error.required"> Date is required.</span></label>
                                                    &nbsp;&nbsp;&nbsp;</label>
                                                    {!! Form::text('panel_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'panel_date', 'ng_model' => 'submitEntryPanelData.panel_date', 'required')) !!}
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
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--Modal Close-->
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
    <script>
        $('#panel_date').datePicker(true);
    </script>
@endsection