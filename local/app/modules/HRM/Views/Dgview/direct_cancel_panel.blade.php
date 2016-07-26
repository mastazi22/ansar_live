{{--User: Shreya--}}
{{--Date: 12/24/2015--}}
{{--Time: 10:44 AM--}}

@extends('template.master')
@section('title','Direct Cancel Panel')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_cancel_panel') !!}
@endsection
@section('content')

    <script>
        $(document).ready(function () {
            $('#cancel_panel_date').datePicker(true);
        })
        GlobalApp.controller('DGCancelPanelController', function ($scope,$http,$sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength =  0;
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{URL::to('HRM/cancel_panel_ansar_details')}}',
                    params:{ansar_id:id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
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
        })
    </script>

    <div ng-controller="DGCancelPanelController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content" style="position: relative;" >
            <notify></notify>
            <div class="box box-solid">
                {!! Form::open(array('url' => 'cancel_panel_entry_for_dg', 'id' => 'cancel_panel_entry_for_dg')) !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID (Comes from Panel)</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                            </div>
                            <div class="form-group">
                                <label for="cancel_panel_date" class="control-label">Cancel Panel Date</label>
                                <input type="text" name="cancel_panel_date" id="cancel_panel_date" class="form-control" ng-model="cancel_panel_date">
                            </div>
                            <div class="form-group">
                                <label for="cancel_panel_comment" class="control-label">Comment for Canceling Panel</label>
                                {!! Form::textarea('cancel_panel_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'cancel_panel_comment', 'size' => '30x4', 'placeholder' => "Write Comment", 'ng-model' => 'cancel_panel_comment')) !!}
                            </div>
                            <button id="cancel-panel-for-dg" class="btn btn-primary" ng-disabled="!ansarDetail.ansar_name_eng||!cancel_panel_date||!ansarId||!cancel_panel_comment"><img ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}" width="16" style="margin-top: -2px">Cancel Panel</button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="ansarDetail.ansar_name_eng==undefined">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.ansar_name_eng!=undefined">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <p>
                                        [[ansarDetail.ansar_name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <p>
                                        [[ansarDetail.name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Unit</label>
                                    <p>
                                        [[ansarDetail.unit_name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Sex</label>
                                    <p>
                                        [[ansarDetail.sex]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <p>
                                        [[ansarDetail.data_of_birth]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Merit List</label>
                                    <p>
                                        [[ansarDetail.ansar_merit_list]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Panel Date</label>
                                    <p>
                                        [[ansarDetail.panel_date]]
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </section>
    </div>
    <script>
        $("#cancel-panel-for-dg").confirmDialog({
            message: 'Are you sure to remove this Ansar from Panel',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#cancel_panel_entry_for_dg").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>

@endsection