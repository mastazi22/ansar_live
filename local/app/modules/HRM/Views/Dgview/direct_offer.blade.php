@extends('template.master')
@section('title','Direct Offer')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_offer') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('DirectOfferController', function ($scope,$http) {
            $scope.districts = [];
            $scope.ansarId = "";
            $scope.selectedDistrict = "";
            $scope.loadingDistrict = true;
            $scope.loadingAnsar = false;
            $scope.ansarDetail = {}
            $scope.submitResult = {};
            $scope.ansar_ids = [];
            $scope.totalLength =  0;
            $scope.exist = false;
            $scope.date = ''
            $scope.loadingSubmit = false;
            $http({
                method:'get',
                url:'{{URL::to('HRM/DistrictName')}}'
            }).then(function (response) {
                $scope.districts = response.data;
                $scope.loadingDistrict = false;
            })
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{URL::to('HRM/direct_panel_ansar_details')}}',
                    params:{ansar_id:id,type:'PANEL'}
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
            $scope.checkFile = function(url){
                $http({
                    url:'{{URL::to('HRM/check_file')}}',
                    params:{path:url},
                    method:'get'
                }).then(function (response) {
                    $scope.exist = response.data.status;
                }, function () {
                    $scope.exist = false;
                })
            }
            $scope.$watch('totalLength', function (n,o) {
                if(!$scope.loadingAnsar&&n>0){
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else{
                    if(!$scope.ansarId)$scope.ansarDetail={}
                }
            })
            $scope.sendOffer = function (s) {
                $scope.error=false;
                $scope.loadingSubmit = true;
                $http({
                    method:'post',
                    url:'{{URL::to('HRM/direct_offer')}}',
                    data:{ansar_id:$scope.ansarId,unit_id:$scope.selectedDistrict,type:s,offer_date:$scope.date}
                }).then(function (response) {
                    console.log(response.data)
                    $scope.error=false;
                    $scope.submitResult  = response.data;
                    $scope.loadingSubmit = false;
                    $scope.ansarId = "";
                    $scope.selectedDistrict = "";
                    $scope.ansarDetail = {}
                },function (response) {
                    console.log(response)
                    $scope.loadingSubmit = false;
                    if(response.status==500)$scope.error = true;
                    $scope.submitResult  = response.data
                })
            }
        })
    </script>
    <div ng-controller="DirectOfferController">
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <form action="{{URL::to('HRM/direct_offer')}}" method="post" form-submit errors="errors" loading="loadingSubmit" confirm-box="true" message="Are you sure want to offer this Ansar">
                                <div class="form-group">
                                    <label for="ansar_id" class="control-label">Ansar ID to send Offer</label>
                                    <input type="text" name="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                                    <p class="text text-danger" ng-if="errors.ansar_id!=undefined">
                                        [[errors.ansar_id[0] ]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="district" class="control-label">Select District to send Offer&nbsp;
                                        <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}" width="16"></label>
                                    <select class="form-control" name="unit_id" ng-model="selectedDistrict" ng-disabled="loadingDistrict">
                                        <option value="">--@lang('title.unit')--</option>
                                        <option ng-repeat="d in districts" ng-disabled="ansarDetail.apid.unit_id==d.id" value="[[d.id]]">[[d.unit_name_bng]]</option>
                                    </select>
                                    <p class="text text-danger" ng-if="errors.unit_id!=undefined">
                                        [[errors.unit_id[0] ]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="date" class="control-label">Offer Date</label>
                                    <input type="text" name="offer_date" date-picker class="form-control" placeholder="Offer Date" ng-model="date">
                                    <p class="text text-danger" ng-if="errors.offer_date!=undefined">
                                        [[errors.offer_date[0] ]]
                                    </p>
                                </div>
                                <button class="btn btn-primary" ng-disabled="loadingSubmit" type="submit">
                                    <i ng-show="loadingSubmit" class="fa fa-spinner fa-pulse"></i>
                                    Send Offer</button>

                            </form>
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
    <script>
        $(document).ready(function () {
            $("#date").datePicker()
        })
    </script>
@stop