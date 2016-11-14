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
            $scope.selectedUnit = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.ansarDetail = {}
            $scope.units = []
            $scope.thanas = []
            $scope.kpis = []
            $scope.loadingUnit = true;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.loadingAnsar = false;
            $scope.loadingSubmit = false;
            $scope.submitResult = {};
            $scope.ansar_ids=[];
            $scope.totalLength =  $scope.ansar_ids.length;
            $scope.memorandumId = ''
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.exist = false;
            $http({
                method:'get',
                url:'{{URL::to('HRM/DistrictName')}}'
            }).then(function (response) {
                $scope.units = response.data
                $scope.loadingUnit = false;
            })
            $scope.loadThana = function (d_id) {
                $scope.loadingThana = true;
                $http({
                    method:'get',
                    url:'{{URL::to('HRM/ThanaName')}}',
                    params:{id:d_id,type:'PANEL'}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "";
                    $scope.loadingThana = false;
                })
            }
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{URL::to('HRM/direct_offer_ansar_detail')}}',
                    params:{ansar_id:id,type:'PANEL'}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    if($scope.ansarDetail.apid!=undefined){
                        if($scope.ansarDetail.apid.profile_pic)$scope.checkFile($scope.ansarDetail.apid.profile_pic)
                        else $scope.exist = false;
                    }
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                })
            }
            $scope.loadKpi = function (t_id) {
                $scope.loadingKpi = true;
                $http({
                    method:'get',
                    url:'{{URL::to('HRM/KPIName')}}',
                    params:{id:t_id}
                }).then(function (response) {
                    $scope.kpis = response.data
                    $scope.selectedKpi = "";
                    $scope.loadingKpi = false;
                })
            }
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{URL::to('verify_memorandum_id')}}', data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

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
            {{--$scope.makeEmbodied = function () {--}}
                {{--$scope.loadingSubmit = true;--}}
                {{--var jd = new Date($scope.j_date)--}}
                {{--var rd = new Date($scope.r_date);--}}
                {{--var jds = jd.getFullYear()+"-"+(jd.getMonth()+1)+"-"+jd.getDate();--}}
                {{--var rds = rd.getFullYear()+"-"+(rd.getMonth()+1)+"-"+rd.getDate();--}}
                {{--$http({--}}
                    {{--url:'{{URL::to('HRM/direct_embodiment_submit')}}',--}}
                    {{--method:'post',--}}
                    {{--data:{--}}
                        {{--ansar_id:$scope.ansarId,--}}
                        {{--kpi_id:$scope.selectedKpi,--}}
                        {{--reporting_date:rds,--}}
                        {{--mem_id:$scope.memorandumId,--}}
                        {{--joining_date:jds--}}
                    {{--}--}}
                {{--}).then(function (response) {--}}
                    {{--console.log(response)--}}
                    {{--$scope.submitResult = response.data;--}}
                    {{--$scope.loadingSubmit = false;--}}
                    {{--if($scope.submitResult.status){--}}
                        {{--$scope.ansarId = "";--}}
                        {{--$scope.r_date = "";--}}
                        {{--$scope.j_date = "";--}}
                        {{--$scope.selectedUnit = "";--}}
                        {{--$scope.selectedThana = "";--}}
                        {{--$scope.selectedKpi = "";--}}
                        {{--$scope.ansarDetail = {}--}}
                    {{--}--}}
                {{--},function (response) {--}}
                    {{--console.log(response);--}}
                    {{--$scope.loadingSubmit = false;--}}
                {{--})--}}
            {{--}--}}
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
    <div ng-controller="DirectEmbodimentController">
        <section class="content">
            <notify></notify>
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <form action="{{URL::to('HRM/direct_embodiment_submit')}}" method="post" form-submit loading="loadingSubmit" errors="errors">
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
                            <div ng-if="!ansarDetail.apid">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.apid">
                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-2">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td rowspan="4"  style="vertical-align: middle;width: 130px;height: 150px">
                                                        <img  style="width: 120px;height: 150px" src="{{URL::to('image').'?file='}}[[ansarDetail.apid.profile_pic]]" alt="">
                                                    </td>
                                                    <th>Name</th>
                                                    <td>[[ansarDetail.apid.ansar_name_bng]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Rank</th>
                                                    <td>[[ansarDetail.apid.name_bng]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Mobile No.</th>
                                                    <td>[[ansarDetail.apid.mobile_no_self]]</td>
                                                </tr>
                                                <tr>
                                                    <th>Home District</th>
                                                    <td>[[ansarDetail.apid.unit_name_bng]]</td>
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
                                                    <td>অফারের বাতিলের তারিখ</td>
                                                </tr>
                                                <tr>
                                                    <td>[[ansarDetail.api.panel_date?ansarDetail.api.panel_date:"N/A"]]</td>
                                                    <td>[[ansarDetail.api.memorandum_id?ansarDetail.api.memorandum_id:"N/A"]]</td>
                                                    <td ng-if="1==ansarDetail.asi.block_list_status">Blocked</td>
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
                                                    <td>[[ansarDetail.aod.offerDate?ansarDetail.aod.offerDate:'N/A']]</td>
                                                    <td>[[ansarDetail.aod.offerUnit?ansarDetail.aod.offerUnit:'N/A']]</td>
                                                    <td>[[ansarDetail.aoci.offerCancel?ansarDetail.aoci.offerCancel:'N\A']]</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td>
                                                        <table class="table table-bordered">
                                                            <caption>অঙ্গিভুতির বিবরণ</caption>
                                                            <tr>
                                                                <td>অঙ্গিভুতির  তারিখ</td>
                                                                <td>অঙ্গিভুতির আইডি নং</td>
                                                                <td>জেলার নাম</td>
                                                                <td>অঙ্গিভুতির সংস্থা</td>
                                                            </tr>
                                                            <tr>
                                                                <td>[[ansarDetail.aei.joining_date?ansarDetail.aei.joining_date:"N/A"]]</td>
                                                                <td>[[ansarDetail.aei.memorandum_id?ansarDetail.aei.memorandum_id:"N/A"]]</td>
                                                                <td>[[ansarDetail.aei.kpi_name?ansarDetail.aei.kpi_name:"N/A"]]</td>
                                                                <td>[[ansarDetail.aei.unit_name_bng?ansarDetail.aei.unit_name_bng:"N/A"]]</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table class="table table-bordered">
                                                            <caption>অ-অঙ্গিভুতির বিবরণ</caption>
                                                            <tr>
                                                                <td>অ-অঙ্গিভুতির  তারিখ</td>
                                                                <td>অ-অঙ্গিভুতির কারন</td>
                                                            </tr>
                                                            <tr>
                                                                <td>[[ansarDetail.adei.disembodiedDate?ansarDetail.adei.disembodiedDate:"N/A"]]</td>
                                                                <td>[[ansarDetail.adei.disembodiedReason?ansarDetail.adei.disembodiedReason:"N/A"]]</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop