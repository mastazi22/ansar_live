@extends('template.master')
@section('title','Direct Transfer')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_transfer') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('DirectTransferController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.j_date = "";
            $scope.ansarDetail = {}
            $scope.ansar_ids=[];
            $scope.totalLength =  $scope.ansar_ids.length;
            $scope.loadingAnsar = false;
            $scope.loadingSubmit = "";
            $scope.ansarExists = true;
            $scope.memorandumId = ''
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.exist = false;
            $scope.submitResult = {};
            var queue = [];
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/single_embodied_ansar_detail')}}/'+id,
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                })
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
                $scope.ansarDetail = {}
                $scope.reset = {thana:true,kpi:true};
            }
        })
    </script>
    <div ng-controller="DirectTransferController">
        <section class="content" style="position: relative;">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            {!! Form::open(['route'=>'direct_transfer_submit','form-submit','errors','loading'=>'loadingSubmit','on-reset'=>'reset()']) !!}
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID to Transfer</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                                <p class="text text-danger" ng-if="errors.ansar_id!=undefined">[[errors.ansar_id[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <label for="mem_id" class="control-label">Memorandum no.</label>
                                <input type="text" name="mem_id" id="mem_id" class="form-control" placeholder="Enter Memorandum no." ng-model="memorandumId" ng-blur="verifyMemorandumId()">
                                <p class="text text-danger" ng-if="errors.mem_id!=undefined">[[errors.mem_id[0] ]]</p>
                            </div>
                            <filter-template
                                    show-item="['unit','thana','kpi']"
                                    type="single"
                                    data="param"
                                    start-load="unit"
                                    layout-vertical="1"
                                    reset="reset"
                                    kpi-disabled="ansarDetail.kpi_id"
                                    field-name="{unit:'unit',thana:'thana',kpi:'t_kpi_id'}"
                                    error-key="{unit:'unit',thana:'thana',kpi:'t_kpi_id'}"
                                    error-message="{unit:errors.unit[0],thana:errors.thana[0],t_kpi_id:errors.t_kpi_id[0]}"
                            >
                            </filter-template>

                            <div class="form-group">
                                <label for="j_date" class="control-label">Embodiment Date</label>
                                <input type="text" name="transfer_date" placeholder="Embodiment Date" date-picker id="j_date" class="form-control" ng-model="j_date">
                                <p class="text text-danger" ng-if="errors.transfer_date!=undefined">[[errors.transfer_date[0] ]]</p>
                            </div>
                            <button class="btn btn-primary" ng-disabled="loadingSubmit||ansarDetail.kpi_id==undefined">
                                <i ng-show="loadingSubmit" class="fa fa-spinner fa-pulse"></i>&nbsp;Transfer Ansar
                            </button>
                            {!! Form::close() !!}
                        </div>
                        <div class="col-sm-8"
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
                                    <label class="control-label">Sex</label>
                                    <p>
                                        [[ansarDetail.sex]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Name</label>
                                    <p>
                                        [[ansarDetail.kpi_name]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Unit</label>
                                    <p>
                                        [[ansarDetail.unit_name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Embodiment Date</label>
                                    <p>
                                        [[ansarDetail.join_date|dateformat:'DD-MMM-YYYY']]
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div ng-bind-html="error">

            </div>
        </section>
        <script>


        </script>
    </div>
@stop