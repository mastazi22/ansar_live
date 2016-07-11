{{--User: Shreya--}}
{{--Date: 12/26/2015--}}
{{--Time: 10:19 AM--}}

@extends('template.master')
@section('title','Direct Panel')
@section('small_title','DG')
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_panel') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#direct_panel_date').datePicker(true);
        })
        GlobalApp.controller('DGPanelController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength =  0;
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/direct_panel_ansar_details')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    console.log($scope.ansarDetail)
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                })
            }
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength +=  1;
            }
            $scope.$watch('totalLength', function (n,o) {
                if(!$scope.loadingAnsar&&n>0){
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else{
                    if(!$scope.ansarId)$scope.ansarDetail={}
                }
            })
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
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
        })
    </script>

    <div ng-controller="DGPanelController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content" style="position: relative;">
            <notify></notify>
            <div class="box box-solid">
               <div class="box-body">
                   {!! Form::open(array('route' => 'direct_panel_entry', 'id' => 'direct_panel_entry')) !!}
                   <div class="row">
                       <div class="col-sm-4">
                           <div class="form-group">
                               <label for="ansar_id" class="control-label">Ansar ID to add to Panel</label>
                               <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                      placeholder="Enter Ansar Id" ng-model="ansarId"
                                      ng-change="makeQueue(ansarId)">
                           </div>
                           <div class="form-group">
                               <label for="memorandum_id" class="control-label">Memorandum ID<span
                                           ng-show="isVerifying"><i class="fa fa-spinner fa-pulse"></i>Verifying</span><span
                                           class="text-danger" ng-if="isVerified"> This id already taken</span></label>
                               <input ng-blur="verifyMemorandumId()" ng-model="memorandumId" type="text"
                                      class="form-control" name="memorandum_id"
                                      placeholder="Enter memorandum id">
                           </div>
                           <div class="form-group">
                               <label for="direct_panel_date" class="control-label">Panel Date</label>
                               <input type="text" name="direct_panel_date" id="direct_panel_date"
                                      class="form-control" ng-model="direct_panel_date">
                           </div>
                           <div class="form-group">
                               <label for="direct_panel_comment" class="control-label">Comment for adding to
                                   Panel</label>
                               {!! Form::textarea('direct_panel_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'direct_panel_comment', 'size' => '30x4', 'placeholder' => "Write any comment", 'ng-model' => 'direct_panel_comment')) !!}
                           </div>
                           <button id="add-panel-for-dg" class="btn btn-primary"
                                   ng-disabled="!direct_panel_date||!ansarId||!direct_panel_comment"><img
                                       ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}"
                                       width="16" style="margin-top: -2px">Add to Panel
                           </button>
                       </div>
                       <div class="col-sm-6 col-sm-offset-2"
                            style="min-height: 400px;border-left: 1px solid #CCCCCC">
                           <div id="loading-box" ng-if="loadingAnsar">
                           </div>
                           <div ng-if="ansarDetail.ansar_details.ansar_name_eng==undefined">
                               <h3 style="text-align: center">No Ansar Found</h3>
                           </div>
                           <div ng-if="ansarDetail.ansar_details.ansar_name_eng!=undefined">
                               <div class="form-group">
                                   <label class="control-label">Name</label>

                                   <p>
                                       [[ansarDetail.ansar_details.ansar_name_eng]]
                                   </p>
                               </div>
                               <div class="form-group">
                                   <label class="control-label">Rank</label>

                                   <p>
                                       [[ansarDetail.ansar_details.name_eng]]
                                   </p>
                               </div>
                               <div class="form-group">
                                   <label class="control-label">Unit</label>

                                   <p>
                                       [[ansarDetail.ansar_details.unit_name_eng]]
                                   </p>
                               </div>
                               <div class="form-group">
                                   <label class="control-label">Sex</label>

                                   <p>
                                       [[ansarDetail.ansar_details.sex]]
                                   </p>
                               </div>
                               <div class="form-group">
                                   <label class="control-label">Date of Birth</label>

                                   <p>
                                       [[ansarDetail.ansar_details.data_of_birth]]
                                   </p>
                               </div>
                               <div class="form-group">
                                   <label class="control-label status-check">Current Status</label>

                                   <p>
                                       [[ansarDetail.status]]
                                   </p>
                               </div>
                               <input type="hidden" name="ansar_status" value="[[ansarDetail.status]]">
                           </div>
                       </div>
                   </div>
                   {!! Form::close() !!}
               </div>
            </div>
        </section>
    </div>
    <script>

        $("#add-panel-for-dg").confirmDialog({
            message: 'Are you sure to add this Ansar in the Panel',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                var status = angular.element(document.getElementsByClassName('status-check').item(0)).scope().ansarDetail.status;
                //alert(status)
                if (status === "Free" || status === "Offered" || status === "Rest") {
                    $("#direct_panel_entry").submit();

                } else if ( status==="Panelled" ){
                    // element.hideConfirmDialog();
                    $('body').notifyDialog(
                            {
                                type: 'error',
                                message: 'This Ansar cannot be added in Panel because he/she is already in the Panel'
                            }
                    ).showDialog()
                }else if ( status==="Embodded" ){
                    // element.hideConfirmDialog();
                    $('body').notifyDialog(
                            {
                                type: 'error',
                                message: 'This Ansar cannot be added in Panel because he/she is Embodied'
                            }
                    ).showDialog()
                }else if ( status==="Freeze" ){
                    // element.hideConfirmDialog();
                    $('body').notifyDialog(
                            {
                                type: 'error',
                                message: 'This Ansar cannot be added in Panel because he/she is freeze'
                            }
                    ).showDialog()
                }else if ( status==="Entry" ){
                    // element.hideConfirmDialog();
                    $('body').notifyDialog(
                            {
                                type: 'error',
                                message: 'This Ansar cannot be added in Panel because he/she is just Registered'
                            }
                    ).showDialog()
                }
            },
            cancel_callback: function (element) {
            }
        })
    </script>

@endsection
