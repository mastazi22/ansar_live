{{--User: Shreya--}}
{{--Date: 12/15/2015--}}
{{--Time: 5:39 PM--}}

@extends('template.master')
@section('content')

    <script>
        $(document).ready(function () {
            $('#unblack_date').datePicker(true);
        })
        GlobalApp.controller('DGUnblackController', function ($scope,$http,$sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.totalLength =  0;
            $scope.ansar_ids = [];
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{action('DGController@loadAnsarDetailforUnblack')}}',
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

    <div class="content-wrapper" ng-controller="DGUnblackController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        {!! Form::open(array('url' => 'dg_unblacklist_entry', 'id' => 'unblack_entry_for_dg')) !!}
        <section class="content" style="position: relative;" >
            <notify></notify>
            <div class="box box-solid">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a>Remove Ansar from Blacklist</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="ansar_id" class="control-label">Ansar ID (Comes from Blacklist)</label>
                                        <input type="text" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar Id" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                                    </div>
                                    <div class="form-group">
                                        <label for="unblack_date" class="control-label">Unblacking Date</label>
                                        <input type="text" name="unblack_date" id="unblack_date" class="form-control" ng-model="unblack_date">
                                    </div>
                                    <div class="form-group">
                                        <label for="unblack_comment" class="control-label">Comment for removing Block</label>
                                        {!! Form::textarea('unblack_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'unblack_comment', 'size' => '30x4', 'placeholder' => "Write any comment", 'ng-model' => 'unblack_comment')) !!}
                                    </div>
                                    <button id="unblack-for-dg" class="btn btn-primary" ng-disabled="!unblack_date||!unblack_comment||!ansarId"><img ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}" width="16" style="margin-top: -2px">Remove Ansar from Blacklist</button>
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
                                            <label class="control-label">Blacked for where</label>
                                            <p>
                                                [[ansarDetail.black_list_from]]
                                            </p>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Date of being Blacked</label>
                                            <p>
                                                [[ansarDetail.black_listed_date]]
                                            </p>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Reason of being Blacked</label>
                                            <p>
                                                [[ansarDetail.black_list_comment]]
                                            </p>
                                        </div>
                                        <input type="hidden" name="ansar_prev_status" value="[[ansarDetail.black_list_from]]">
                                    </div>
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
        $("#unblack-for-dg").confirmDialog({
            message: 'Are u sure to remove this Ansar from the Blacklist',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#unblack_entry_for_dg").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
@endsection