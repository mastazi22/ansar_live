@extends('template.master')
@section('title','Direct Embodiment')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_embodiment') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#r_date').datePicker();
            $('#j_date').datePicker();
        })
        GlobalApp.controller('DirectEmbodimentController', function ($scope,$http) {
            $scope.ansarId = "";
            $scope.r_date = "";
            $scope.j_date = "";
            $scope.ansarDetail = {}
            $scope.reset = {thana:false,kpi:false}
            $scope.loadingAnsar = false;
            $scope.loadingSubmit = false;
            $scope.submitResult = {};
            $scope.ansar_ids=[];
            $scope.totalLength =  $scope.ansar_ids.length;
            $scope.memorandumId = ''
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.exist = false;
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{URL::route('direct_embodiment_ansar_details')}}',
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
            $scope.reset = function () {
                $scope.reset = {};
                $scope.reset = {thana:true,kpi:true}
                $scope.ansarDetail = '';
            }
        })
    </script>
    <div ng-controller="DirectEmbodimentController">
        <section class="content">
            <notify></notify>
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <form action="{{URL::to('HRM/direct_embodiment_submit')}}" method="post" form-submit loading="loadingSubmit" on-reset="reset()" errors="errors">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                                <p class="text text-danger" ng-if="errors.ansar_id!=undefined&&errors.ansar_id[0]">[[errors.ansar_id[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <label for="mem_id" class="control-label">Memorandum no.&nbsp;<i class="fa fa-spinner fa-pulse" ng-show="isVerifying"></i>
                                    <span class="text-danger" ng-if="isVerified">This id already taken</span>
                                </label>
                                <input type="text" name="mem_id" id="mem_id" class="form-control" placeholder="Enter Memorandum no." ng-model="memorandumId">
                                <p class="text text-danger" ng-if="errors.mem_id!=undefined&&errors.mem_id[0]">[[errors.mem_id[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <label for="r_date" class="control-label">Reporting Date</label>
                                <input type="text" name="reporting_date" id="r_date" class="form-control" ng-model="r_date">
                                <p class="text text-danger" ng-if="errors.reporting_date!=undefined&&errors.reporting_date[0]">[[errors.reporting_date[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <label for="j_date" class="control-label">Joining Date</label>
                                <input type="text" name="joining_date" id="j_date" class="form-control" ng-model="j_date">
                                <p class="text text-danger" ng-if="errors.joining_date!=undefined&&errors.joining_date[0]">[[errors.joining_date[0] ]]</p>
                            </div>
                                <filter-template
                                        show-item="['unit','thana','kpi']"
                                        type="single"
                                        data="param"
                                        start-load="unit"
                                        layout-vertical="1"
                                        reset="reset"
                                        field-name="{unit:'unit',thana:'thana',kpi:'kpi_id'}"
                                        error-key="{unit:'unit',thana:'thana',kpi:'kpi_id'}"
                                        error-message="{unit:errors.unit[0],thana:errors.thana[0],kpi_id:errors.kpi_id[0]}"
                                >

                                </filter-template>
                            <button class="btn btn-primary" ng-disabled="loadingSubmit"><i class="fa fa-spinner fa-pulse" ng-show="loadingSubmit" ></i>Embodied Ansar</button>
                            </form>
                        </div>
                        <div class="col-sm-8"
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
                                    <label class="control-label">Home District</label>

                                    <p>
                                        [[ansarDetail.ansar_details.unit_name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Gender</label>

                                    <p>
                                        [[ansarDetail.ansar_details.sex]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>

                                    <p>
                                        [[ansarDetail.ansar_details.data_of_birth|dateformat:'DD-MMM-YYYY']]
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Current Status</label>

                                    <p>
                                        [[ansarDetail.status]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Last Disembodied Date</label>

                                    <p>
                                        [[ansarDetail.ansar_details.release_date?(ansarDetail.ansar_details.release_date|dateformat:'DD-MMM-YYYY'):'--']]
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop