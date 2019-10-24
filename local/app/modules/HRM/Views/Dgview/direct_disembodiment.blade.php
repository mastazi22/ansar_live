@extends('template.master')
@section('title','Direct Dis-Embodiment')
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_disembodiment') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('DirectEmbodimentController', function ($scope, $http, $sce, notificationService) {
            $scope.ansarId = "";
            $scope.dis_date = "";
            $scope.selectedReason = "";
            $scope.comment = "";
            $scope.ansarDetail = {};
            $scope.disEmbodimentReason = [];
            $scope.loadingReason = true;
            $scope.loadingAnsar = false;
            $scope.loadingSubmit = false;
            $scope.error = "";
            $scope.ansar_ids = [];
            $scope.totalLength = 0;
            $scope.memorandumId = '';
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.exist = false;
            $scope.submitResult = {};
            $http({
                method: 'get',
                url: '{{URL::to('HRM/load_disembodiment_reason')}}'
            }).then(function (response) {
                $scope.disEmbodimentReason = response.data;
                $scope.loadingReason = false;
            });
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('direct_disembodiment_ansar_details')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data;
                    if ($scope.ansarDetail.apid != undefined) {
                        if ($scope.ansarDetail.apid.profile_pic) $scope.checkFile($scope.ansarDetail.apid.profile_pic);
                        else $scope.exist = false;
                    }
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                })
            };
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            };
            $scope.checkFile = function (url) {
                $http({
                    url: '{{URL::to('HRM/check_file')}}',
                    params: {path: url},
                    method: 'get'
                }).then(function (response) {
                    $scope.exist = response.data.status;
                }, function () {
                    $scope.exist = false;
                })
            };
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                } else {
                    if (!$scope.ansarId) $scope.ansarDetail = {}
                }
            });
            $scope.reset = function () {
                $scope.ansarDetail = {}
            }
        })
    </script>
    <div ng-controller="DirectEmbodimentController">
        <section class="content">
            <notify></notify>
            <div class="box box-solid">
                <div class="box-body">
                    {!! Form::open(['route'=>'direct_disembodiment_submit','form-submit','errors','loading'=>'loadingSubmit','on-reset'=>'reset()']) !!}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                       placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                                <p class="text text-danger" ng-if="errors!=undefined&&errors.ansar_id!=undefined">
                                    [[errors.ansar_id[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <label for="mem_id" class="control-label">Memorandum no.</label>
                                <input type="text" name="mem_id" id="mem_id" class="form-control"
                                       placeholder="Enter Memorandum no." ng-model="memorandumId">
                                <p class="text text-danger" ng-if="errors!=undefined&&errors.mem_id!=undefined">
                                    [[errors.mem_id[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <datepicker-separate-fields label="Disembodiment Date:" notify="disEmboInvalidDate"
                                                            rdata="dis_date"></datepicker-separate-fields>
                                <input type="hidden" name="dis_date" ng-value="dis_date">
                                <p class="text text-danger" ng-if="errors!=undefined&&errors.dis_date!=undefined">
                                    [[errors.dis_date[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <label for="dis-reason" class="control-label">Disembodiment Reason&nbsp;
                                    <img ng-show="loadingReason" src="{{asset('dist/img/facebook.gif')}}"
                                         width="16"></label>
                                <select ng-disabled="loadingReason" name="reason" id="dis-reason" class="form-control">
                                    <option value="">--@lang('title.reason')--</option>
                                    <option ng-repeat="u in disEmbodimentReason" value="[[u.id]]">[[u.reason_in_bng]]
                                    </option>
                                </select>

                                <p class="text text-danger" ng-if="errors!=undefined&&errors.reason!=undefined">
                                    [[errors.reason[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <label for="comment" class="control-label">Comment for Disembodiment</label>
                                <textarea name="comment" id="comment" class="form-control"
                                          placeholder="Enter Comment"></textarea>
                            </div>
                            <button class="btn btn-primary" ng-disabled="loadingSubmit || disEmboInvalidDate">
                                <i ng-show="loadingSubmit" class="fa fa-spinner fa-pulse"></i>&nbsp;Dis-Embodied Ansar
                            </button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar"></div>
                            <div ng-if="ansarDetail.ansar_details.ansar_name_eng==undefined">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.ansar_details.ansar_name_eng!=undefined">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <p>[[ansarDetail.ansar_details.ansar_name_eng]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <p>[[ansarDetail.ansar_details.name_eng]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Home District</label>
                                    <p>[[ansarDetail.ansar_details.unit_name_eng]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Gender</label>
                                    <p>[[ansarDetail.ansar_details.sex]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <p>[[ansarDetail.ansar_details.data_of_birth|dateformat:'DD-MMM-YYYY']]</p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Embodied Date</label>
                                    <p>[[ansarDetail.ansar_details.joining_date|dateformat:'DD-MMM-YYYY']]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Current KPI Name</label>
                                    <p>[[ansarDetail.ansar_details.kpi_name]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Last Disembodied Date</label>
                                    <p>
                                        [[ansarDetail.ansar_details.release_date?(ansarDetail.ansar_details.release_date|dateformat:'DD-MMM-YYYY'):'--']]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Disembodied Reason</label>
                                    <p>
                                        [[ansarDetail.ansar_details.reason_in_bng?ansarDetail.ansar_details.reason_in_bng:'--']]</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </section>
    </div>
@stop