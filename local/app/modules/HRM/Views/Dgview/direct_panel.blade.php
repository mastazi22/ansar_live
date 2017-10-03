{{--User: Shreya--}}
{{--Date: 12/26/2015--}}
{{--Time: 10:19 AM--}}

@extends('template.master')
@section('title','Direct Panel')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_panel') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#direct_panel_date').datepicker({                dateFormat:'dd-M-yy'            })();
        })
        GlobalApp.controller('DGPanelController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength = 0;
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
                $scope.totalLength += 1;
            }
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else {
                    if (!$scope.ansarId) $scope.ansarDetail = {}
                }
            })
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
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="fa fa-remove"></span> {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <section class="content" style="position: relative;">
            <div class="box box-solid">
                <div class="box-body">

                    <div class="row">
                        <div class="col-sm-4">
                            {!! Form::model(Request::old(),array('route' => 'direct_panel_entry', 'id' => 'direct_panel_entry')) !!}
                            <div class="form-group">
                                {!! Form::label('ansar_id','Ansar ID to add to Panel') !!}
                                @if(Request::old('ansar_id'))
                                    {!! Form::text('ansar_id',null,['class'=>'form-control','placeholder'=>'Enter Ansar ID','ng-model'=>'ansarId','ng-init'=>"ansarId=".Request::old('ansar_id'),'ng-change'=>'makeQueue(ansarId)']) !!}
                                @else
                                    {!! Form::text('ansar_id',null,['class'=>'form-control','placeholder'=>'Enter Ansar ID','ng-model'=>'ansarId','ng-change'=>'makeQueue(ansarId)']) !!}
                                @endif
                                {!! $errors->first('ansar_id','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('memorandum_id','Memorandum no.') !!}
                                {!! Form::text('memorandum_id',null,['class'=>'form-control','placeholder'=>'Enter Memorandum no.']) !!}
                                {!! $errors->first('memorandum_id','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('direct_panel_date','Panel Date') !!}
                                {!! Form::text('direct_panel_date',null,['class'=>'form-control','id'=>'direct_panel_date','placeholder'=>'Enter Panel Date']) !!}
                                {!! $errors->first('direct_panel_date','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('direct_panel_comment','Comment for adding to Panel') !!}
                                {!! Form::textarea('direct_panel_comment', null, $attributes = array('class' => 'form-control', 'id' => 'direct_panel_comment', 'size' => '30x4', 'placeholder' => "Write Comment", 'ng-model' => 'direct_panel_comment')) !!}
                            </div>
                            <button id="add-panel-for-dg" class="btn btn-primary">
                                <img ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}"
                                     width="16" style="margin-top: -2px">Add to Panel
                            </button>
                            {!! Form::close() !!}
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

                </div>
            </div>
        </section>
    </div>

@endsection
