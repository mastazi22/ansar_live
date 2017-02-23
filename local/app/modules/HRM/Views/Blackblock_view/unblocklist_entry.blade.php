{{--User: Shreya--}}
{{--Date: 12/14/2015--}}
{{--Time: 6:17 PM--}}

@extends('template.master')
@section('title','Remove Ansar from Blocklist')
@section('breadcrumb')
    {!! Breadcrumbs::render('unblock_ansar') !!}
@endsection
@section('content')

    <script>
        $(document).ready(function () {
            $('#unblock_date').datePicker(true);
        })
        GlobalApp.controller('UnblockController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('unblocklist_ansar_details')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    $scope.loadingAnsar = false;
                })
            }
        })
    </script>

    <div ng-controller="UnblockController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('cancel_blocklist') !!}--}}
        {{--</div>--}}
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="fa fa-warning"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content" style="position: relative;">
            <notify></notify>
            <div class="box box-solid">
                {!! Form::open(array('route' => 'unblocklist_entry', 'id' => 'unblock_entry')) !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID (Comes from Blocklist)</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                       placeholder="Enter Ansar ID" ng-model="ansarId"
                                       ng-change="loadAnsarDetail(ansarId)">
                                {!! $errors->first('ansar_id','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                <label for="unblock_date" class="control-label">Unlocking Date</label>
                                <input type="text" name="unblock_date" id="unblock_date" class="form-control"
                                       ng-model="unblock_date">
                                {!! $errors->first('unblock_date','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                <label for="unblock_comment" class="control-label">Reason</label>
                                {!! Form::textarea('unblock_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'unblock_comment', 'size' => '30x4', 'placeholder' => "Write Reason", 'ng-model' => 'unblock_comment')) !!}
                            </div>
                            <button id="unblock_ansar" class="btn btn-primary"
                                    ng-disabled="!unblock_date||!ansarId"><img
                                        ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}"
                                        width="16" style="margin-top: -2px">Unblock Ansar
                            </button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2"
                             style="min-height: 400px;border-left: 1px solid #CCCCCC">
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
                                    <label class="control-label">Blocked from where</label>

                                    <p>
                                        [[ansarDetail.block_list_from]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of being Blocked</label>

                                    <p>
                                        [[ansarDetail.date_for_block]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Reason of being Blocked</label>

                                    <p>
                                        [[ansarDetail.comment_for_block]]
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
            $("#unblock_ansar").confirmDialog({
                message: 'Are you sure to remove this Ansar from the Blocklist',
                ok_button_text: 'Confirm',
                cancel_button_text: 'Cancel',
                ok_callback: function (element) {
                    $("#unblock_entry").submit()
                },
                cancel_callback: function (element) {
                }
            })
    </script>
@endsection