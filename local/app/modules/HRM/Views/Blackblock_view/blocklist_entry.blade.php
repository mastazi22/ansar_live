{{--User: Shreya--}}
{{--Date: 12/14/2015--}}
{{--Time: 11:28 AM--}}

@extends('template.master')
@section('title','Ansar Block List Entry')
@section('breadcrumb')
    {!! Breadcrumbs::render('add_to_blocklist') !!}
@endsection
@section('content')

    <script>
        $(document).ready(function () {
            $('#block_date').datePicker(true);
        })
        GlobalApp.controller('BlockController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('blocklist_ansar_details')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    $scope.loadingAnsar = false;
                    console.log($scope.ansarDetail);
                })
            }
        })
    </script>

    <div ng-controller="BlockController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('add_to_blocklist') !!}--}}
        {{--</div>--}}
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
                {!! Form::open(array('route' => 'blocklist_entry', 'id' =>'block_entry')) !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                       placeholder="Enter Ansar Id" ng-model="ansarId"
                                       ng-change="loadAnsarDetail(ansarId)">
                            </div>
                            <div class="form-group">
                                <label for="block_date" class="control-label">Blocking Date</label>
                                <input type="text" name="block_date" id="block_date" class="form-control"
                                       ng-model="block_date">
                            </div>
                            <div class="form-group">
                                <label for="block_comment" class="control-label">Comment for Blocking</label>
                                {!! Form::textarea('block_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'block_comment', 'size' => '30x4', 'placeholder' => "Write any comment", 'ng-model' => 'block_comment')) !!}
                            </div>
                            <button id="block-ansar" class="btn btn-primary"
                                    ng-disabled="!block_date||!ansarId||!block_comment"><img
                                        ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}"
                                        width="16" style="margin-top: -2px">Block Ansar
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
                                    <label class="control-label">Current Status</label>

                                    <p>
                                        [[ansarDetail.status]]
                                    </p>
                                </div>
                                <input type="hidden" name="ansar_status" value="[[ansarDetail.status]]">
                                <input type="hidden" name="from_id" value="[[ansarDetail.ansar_details.id]]">
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </section>
    </div>
    <script>
        $("#block-ansar").confirmDialog({
            message: 'Are you sure to add this Ansar in the Blocklist',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#block_entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
@endsection