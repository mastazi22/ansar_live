{{--User: Shreya--}}
{{--Date: 2/22/2016--}}
{{--Time: 11:02 AM--}}

@extends('template.master')
@section('title','Disembodiment Date Correction')
@section('breadcrumb')
    {!! Breadcrumbs::render('disembodiment_date_correction') !!}
@endsection
@section('content')

    <script>
        $(document).ready(function () {
            $('#new_disembodiment_date').datePicker(true);
        })
        GlobalApp.controller('DisembodimentDateCorrectionController', function ($scope,$http,$sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength =  0;
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{URL::route('load_ansar_for_disembodiment_date_correction')}}',
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

    <div ng-controller="DisembodimentDateCorrectionController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('disembodiment_date_correction') !!}--}}
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
                {!! Form::open(array('route' => 'new-disembodiment-date-entry', 'id' => 'new-disembodiment-date-entry')) !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID (Comes from Rest)</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                            </div>
                            <div class="form-group">
                                <label for="new_disembodiment_date" class="control-label">New Disembodiment Date</label>
                                <input type="text" name="new_disembodiment_date" id="new_disembodiment_date" class="form-control" ng-model="new_disembodiment_date">
                            </div>
                            <button id="confirm-new-disembodiment-date" class="btn btn-primary" ng-disabled="!ansarDetail.name||!new_disembodiment_date||!ansarId"><img ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}" width="16" style="margin-top: -2px">Correct Date</button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="ansarDetail.name==undefined">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.name!=undefined">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <p>
                                        [[ansarDetail.name]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <p>
                                        [[ansarDetail.rank]]
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
                                        [[ansarDetail.dob]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Own Unit</label>
                                    <p>
                                        [[ansarDetail.unit]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Own Thana</label>
                                    <p>
                                        [[ansarDetail.thana]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Dis-Embodiment Date</label>
                                    <p>
                                        [[ansarDetail.r_date]]
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

        $("#confirm-new-disembodiment-date").confirmDialog({
            message:'Are you sure to Correct the Dis-Embodiment Date',
            ok_button_text:'Confirm',
            cancel_button_text:'Cancel',
            ok_callback: function (element) {
                $("#new-disembodiment-date-entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
@endsection