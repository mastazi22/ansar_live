{{--User: Shreya--}}
{{--Date: 12/19/2015--}}
{{--Time: 11:37 AM--}}

@extends('template.master')
@section('title','Service Extension')
@section('breadcrumb')
    {!! Breadcrumbs::render('service_extension') !!}
@endsection
@section('content')

    <script>
        GlobalApp.controller('ServiceExtensionController', function ($scope,$http,$sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{URL::route('load_ansar_for_service_extension')}}',
                    params:{ansar_id:id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    $scope.loadingAnsar = false;
                })
            }
            $scope.$watch('ansarId', function(n, o){
                $scope.loadAnsarDetail(n);
            })
        })
    </script>

    <div ng-controller="ServiceExtensionController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('service_extension') !!}--}}
        {{--</div>--}}
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
                {!! Form::open(array('route' => 'service_extension_entry', 'id' => 'serviceExtensionForm', 'name' => 'serviceExtensionForm', 'novalidate')) !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group required" ng-init="ansarId='{{Request::old('ansar_id')}}'">
                                <label for="ansar_id" class="control-label">Ansar ID (Embodied Ansar)</label>
                                <input type="text" value="{{Request::old('ansar_id')}}" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="loadAnsarDetail(ansarId)">
                                @if($errors->has('ansar_id'))
                                    <p class="text-danger">{{$errors->first('ansar_id')}}</p>
                                @endif
                            </div>
                            <div class="form-group required" ng-init="extended_period='{{Request::old('extended_period')}}'">
                                <label for="extended_period" class="control-label">Extended Period (In Month)</label>
                                <input type="text" value="{{Request::old('extended_period')}}" name="extended_period" id="extended_period" placeholder="Enter the number of Month of Extension" class="form-control" ng-model="extended_period" required>
                                @if($errors->has('extended_period'))
                                    <p class="text-danger">{{$errors->first('extended_period')}}</p>
                                @endif
                            </div>
                            <div class="form-group required" ng-init="service_extension_comment='{{Request::old('service_extension_comment')}}'">
                                <label for="service_extension_comment" class="control-label">Comment for Service Extension</label>
                                {!! Form::textarea('service_extension_comment', $value = Request::old('service_extension_comment'), $attributes = array('class' => 'form-control', 'id' => 'service_extension_comment', 'size' => '30x4', 'placeholder' => "Write any Comment", 'ng-model' => 'service_extension_comment', 'required')) !!}
                                @if($errors->has('service_extension_comment'))
                                    <p class="text-danger">{{$errors->first('service_extension_comment')}}</p>
                                @endif
                            </div>
                            <button id="service_extension_confirm" class="btn btn-primary"><img ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}" width="16" style="margin-top: -2px">Extend Service</button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="ansarDetail.ansar_name_eng==undefined">
                                <h3 style="text-align: center">No Ansar Found</h3>
                                <input type="hidden" name="ansarExist" value="0">
                            </div>
                            <div ng-if="ansarDetail.ansar_name_eng!=undefined">
                                <input type="hidden" name="ansarExist" value="1">
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
                                    <label class="control-label">KPI Name</label>
                                    <p>
                                        [[ansarDetail.kpi_name]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Service Ended date</label>
                                    <p>
                                        [[ansarDetail.service_ended_date]]
                                    </p>
                                </div>
                                <input type="hidden" name="ansar_prev_status" value="[[ansarDetail.black_list_from]]">
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </section>
    </div>
    <script>
        $("#service_extension_confirm").confirmDialog({
            message: 'Are u sure to extent the service days for this Ansar',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#serviceExtensionForm").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
@endsection