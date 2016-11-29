{{--User: Shreya--}}
{{--Date: 11/8/2015--}}
{{--Time: 11:48 AM--}}

@extends('template.master')
@section('title','Embodiment')
@section('breadcrumb')
    {!! Breadcrumbs::render('embodiment_entry') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('NewEmbodimentController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.errors = ''
            $scope.ansarDetail = {};
            $scope.units = [];
            $scope.thanas = [];
            $scope.totalLength = 0;
            $scope.ansar_ids = [];
            $scope.loadingKpi = false;
            $scope.loadingDetail = false;
            $scope.loadingAnsar = false;
            $scope.joining_date = "";
            $scope.isAnsarAvailable = false;
            $scope.hh = 0;
            var j_date = "";
            var r_date = "";
            var rd = new Date();
            $scope.msg = "";
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('check-ansar')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    $scope.loadingAnsar = false;
                    console.log($scope.ansarDetail)
                    $scope.totalLength--;
                    $scope.loadingAnsar = false;
                }, function () {
                    $scope.loadingAnsar = false;
                })
            }
            $scope.$watch('ansarId', function(n, o){
                $scope.loadAnsarDetail(n);
            })
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            }
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else {
                    if (!$scope.ansarId)$scope.ansarDetail = {}
                }
            })
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }
            $scope.dateConvert=function(date){
                return (moment(date).format('DD-MMM-Y'));
            }
        })
    </script>
    <div ng-controller="NewEmbodimentController" ng-app>
        <section class="content" style="position: relative;">
            <div class="box box-solid">
                <div class="box-body">
                    {!! Form::open(array('route' => 'new-embodiment-entry', 'name' => 'newEmbodimentForm', 'novalidate','form-submit','errors','loading','status')) !!}
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group required" ng-init="ansarId='{{Request::old('ansar_id')}}'">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                       placeholder="Enter Ansar ID" ng-model="ansarId"
                                       ng-change="makeQueue(ansarId)">
                                    <p class="text-danger" ng-if="errors.ansar_id!=undefined">[[errors.ansar_id[0] ]]</p>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Memorandum no. & Date</label>

                                <div class="row">
                                    <div class="col-md-7" style="padding-right: 0">
                                        <input ng-model="memorandumId"
                                               type="text" class="form-control" name="memorandum_id"
                                               placeholder="Enter Memorandum no." required>
                                    </div>
                                    <div class="col-md-5">
                                        <input date-picker ng-model="memDate"
                                               type="text" class="form-control" name="mem_date"
                                               placeholder="Memorandum Date" required>
                                    </div>

                                </div>
                                <p class="text-danger" ng-if="errors.memorandum_id!=undefined">[[errors.memorandum_id[0] ]]</p>
                            </div>
                            <div class="form-group required">
                                <label for="reporting_date" class="control-label">Reporting Date</label>
                                {!! Form::text('reporting_date', null, $attributes = array('class' => 'form-control', 'id' => 'reporting_date', 'ng-model' => 'reporting_date','date-picker', 'required')) !!}
                                <p class="text-danger" ng-if="errors.reporting_date!=undefined">[[errors.reporting_date[0] ]]</p>
                            </div>
                            <div class="form-group required">
                                <label for="joining_date" class="control-label">Embodiment Date</label>
                                {!! Form::text('joining_date', null, $attributes = array('class' => 'form-control', 'id' => 'joining_date','date-picker', 'ng-model' => 'joining_date','required')) !!}
                                <p class="text-danger" ng-if="errors.joining_date!=undefined">[[errors.joining_date[0] ]]</p>
                            </div>
                            <!---->
                            <!---->
                            <filter-template
                                    show-item="['unit','thana','kpi']"
                                    type="single"
                                    data="param"
                                    start-load="unit"
                                    layout-vertical="1"
                                    field-name="{unit:'division_name_eng',thana:'thana_name_eng',kpi:'kpi_id'}"
                                    error-key="{unit:'division_name_eng',thana:'thana_name_eng',kpi:'kpi_id'}"
                                    error-message="{division_name_eng:errors.division_name_eng[0],thana_name_eng:errors.thana_name_eng[0],kpi_id:errors.kpi_id[0]}"
                            >

                            </filter-template>
                            <button type="submit" class="btn btn-primary" ng-disabled="loading">
                                <i class="fa fa-spinner fa-pulse" ng-show="loading"></i>Embodied
                            </button>
                        </div>
                        <div class="col-sm-7">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="!ansarDetail.aoi.ansar_id">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.aoi.ansar_id">
                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-2">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td rowspan="4"
                                                        style="vertical-align: middle;width: 130px;height: 150px">
                                                        <img style="width: 120px;height: 150px" src="{{URL::to('image').'?file='}}[[ansarDetail.apd.profile_pic]]" alt="">
                                                    </td>
                                                    <th>Name</th>
                                                    <td>[[ansarDetail.apd.ansar_name_bng]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Rank</th>
                                                    <td>[[ansarDetail.apd.name_bng]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Mobile No.</th>
                                                    <td>[[ansarDetail.apd.mobile_no_self]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Home District</th>
                                                    <td>[[ansarDetail.apd.unit_name_bng]]</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <caption>প্যানেলভুক্তির  ও অফারের বিবরণ</caption>
                                                <tr>
                                                    <td>প্যানেলভুক্তির তারিখ</td>
                                                    <td>প্যানেল আইডি নং</td>
                                                    <td>বর্তমান অবস্থা</td>
                                                    <td>অফারের তারিখ</td>
                                                    <td>অফারের জেলা</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        [[ansarDetail.api.panel_date?dateConvert(ansarDetail.api.panel_date):"N/A"]]
                                                    </td>
                                                    <td>
                                                        [[ansarDetail.api.memorandum_id?ansarDetail.api.memorandum_id:"N/A"]]
                                                    </td>
                                                    <td ng-if="1==ansarDetail.asi.block_list_status">Blocked
                                                    </td>
                                                    <td ng-if="0==ansarDetail.asi.block_list_status">
                                                        <span ng-if="1==ansarDetail.asi.free_status">Free</span>
                                                        <span ng-if="1==ansarDetail.asi.pannel_status">Panel</span>
                                                        <span ng-if="1==ansarDetail.asi.offer_sms_status">Offered</span>
                                                        <span ng-if="1==ansarDetail.asi.embodied_status">Embodied</span>
                                                        <span ng-if="1==ansarDetail.asi.freezing_status">Freeze</span>
                                                        <span ng-if="1==ansarDetail.asi.early_retierment_statBlockedus">Early retirement</span>
                                                        <span ng-if="1==ansarDetail.asi.block_list_status"></span>
                                                        <span ng-if="1==ansarDetail.asi.black_list_status">Blacked</span>
                                                        <span ng-if="1==ansarDetail.asi.rest_status">Rest</span>
                                                        <span ng-if="1==ansarDetail.asi.retierment_status">Retirement</span>
                                                    </td>
                                                    <td>
                                                        [[ansarDetail.aoi.offerDate?dateConvert(ansarDetail.aoi.offerDate):'N/A']]
                                                    </td>
                                                    <td>
                                                        [[ansarDetail.aoi.offered_district?ansarDetail.aoi.offered_district:'N/A']]
                                                    </td>
                                                    {{--<td>[[ansarDetail.aoci.offerCancel?ansarDetail.aoci.offerCancel:'N\A']]</td>--}}
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                        <a ng-show="status" target="_blank" href="{{URL::to('HRM/print_letter')}}?id=[[memorandumId]]&unit=[[param.unit]]&view=full&type=EMBODIMENT" class="btn btn-primary" style="margin-top: 10px">Print Embodiment Letter</a>
                </div>
            </div>
        </section>
    </div>
@stop