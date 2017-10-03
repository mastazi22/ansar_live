{{--User: Shreya--}}
{{--Date: 12/14/2015--}}
{{--Time: 11:28 AM--}}

@extends('template.master')
@section('title','Add Ansar in Blacklist')
@section('breadcrumb')
    {!! Breadcrumbs::render('add_to_blacklist') !!}
@endsection
@section('content')

    <script>
        $(document).ready(function () {
            $('#black_date').datepicker({                dateFormat:'dd-M-yy'            })();
        })
        GlobalApp.controller('BlackController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('blacklist_ansar_details')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    if (response.data) {
                        $scope.ansarDetail = response.data
                        $scope.loadingAnsar = false;
                        //console.log($scope.ansarDetail);
                    } else {
                        $scope.ansarDetail = "";
                        $scope.loadingAnsar = false;
                    }
                })
            }
        })
    </script>

    <div ng-controller="BlackController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('add_to_blacklist') !!}--}}
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
                    <span class="fa fa-remove"></span> {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        {!! Form::open(array('route' => 'blacklist_entry', 'id' =>'black_entry')) !!}
        <section class="content" style="position: relative;">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="loadAnsarDetail(ansarId)">
                                {!! $errors->first('ansar_id','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                <label for="black_date" class="control-label">Blacking Date</label>
                                <input type="text" name="black_date" id="black_date" class="form-control" ng-model="black_date">
                                {!! $errors->first('black_date','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                <label for="black_comment" class="control-label">Reason</label>
                                {!! Form::textarea('black_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'black_comment', 'size' => '30x4', 'placeholder' => "Write Reason", 'ng-model' => 'black_comment')) !!}
                                {!! $errors->first('black_comment','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <button id="black-ansar" class="btn btn-primary"
                                    ng-disabled="!black_date||!ansarId||!black_comment"><img
                                        ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}"
                                        width="16" style="margin-top: -2px">Black Ansar
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

        $("#black-ansar").confirmDialog({
            message: 'Are you sure to add this Ansar in the Blacklist',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#black_entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
@endsection