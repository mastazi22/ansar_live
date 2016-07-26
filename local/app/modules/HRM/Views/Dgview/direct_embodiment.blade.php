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
            $scope.makeEmbodied = function () {
                $scope.loadingSubmit = true;
                var jd = new Date($scope.j_date)
                var rd = new Date($scope.r_date);
                var jds = jd.getFullYear()+"-"+(jd.getMonth()+1)+"-"+jd.getDate();
                var rds = rd.getFullYear()+"-"+(rd.getMonth()+1)+"-"+rd.getDate();
                $http({
                    url:'{{URL::to('HRM/direct_embodiment_submit')}}',
                    method:'post',
                    data:{
                        ansar_id:$scope.ansarId,
                        kpi_id:$scope.selectedKpi,
                        reporting_date:rds,
                        mem_id:$scope.memorandumId,
                        joining_date:jds
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.submitResult = response.data;
                    $scope.loadingSubmit = false;
                    if($scope.submitResult.status){
                        $scope.ansarId = "";
                        $scope.r_date = "";
                        $scope.j_date = "";
                        $scope.selectedUnit = "";
                        $scope.selectedThana = "";
                        $scope.selectedKpi = "";
                        $scope.ansarDetail = {}
                    }
                },function (response) {
                    console.log(response);
                    $scope.loadingSubmit = false;
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
        GlobalApp.directive('notify', function () {
            return {
                restrict: 'E',
                link: function (scope, element, attr) {
                    scope.$watch('submitResult', function (n, o) {
                        if (Object.keys(n).length > 0) {
                            if (n.status) {
                                $('body').notifyDialog({type: 'success', message: n.message}).showDialog()
                            }
                            else {
                                $('body').notifyDialog({type: 'error', message: n.message}).showDialog()
                            }
                        }
                    })
                }

            }
        })
        GlobalApp.directive('confirmDialog', function () {
            return{
                restrict:'A',
                link: function (scope,elem,attr) {
                    $(elem).confirmDialog({
                        message: 'Are you sure want to embodied this ansar',
                        ok_button_text:'Embodied',
                        cancel_button_text:'No,Thanks',
                        ok_callback: function (element) {
                            if(scope.ansarDetail.asi.offer_sms_status==0){
                                $('body').notifyDialog({type: 'error', message: 'You can`t Embodied this ansar. Because he is not offered.'}).showDialog()
                                return;
                            }
                            else if(scope.ansarDetail.asi.block_list_status==1){
                                $('body').notifyDialog({type: 'error', message: 'You can`t Embodied this ansar. Because he is embodied but blocked.'}).showDialog()
                                return;
                            }
                            scope.makeEmbodied();
                        },
                        cancel_callback: function (element) {

                        }
                    })
                }
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
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                            </div>
                            <div class="form-group">
                                <label for="mem_id" class="control-label">Memorandum ID&nbsp;<i class="fa fa-spinner fa-pulse" ng-show="isVerifying"></i>
                                    <span class="text-danger" ng-if="isVerified">This id already taken</span>
                                </label>
                                <input type="text" name="mem_id" id="mem_id" class="form-control" placeholder="Enter Memorandum ID" ng-model="memorandumId" ng-blur="verifyMemorandumId()">
                            </div>
                            <div class="form-group">
                                <label for="r_date" class="control-label">Reporting Date</label>
                                <input type="text" id="r_date" class="form-control" ng-model="r_date">
                            </div>
                            <div class="form-group">
                                <label for="j_date" class="control-label">Joining Date</label>
                                <input type="text" name="jo_date" id="j_date" class="form-control" ng-model="j_date">
                            </div>
                            <div class="form-group">
                                <label for="e_unit" class="control-label">Select a Unit&nbsp;
                                    <img ng-show="loadingUnit" src="{{asset('dist/img/facebook.gif')}}" width="16"></label>
                                <select ng-disabled="loadingUnit" id="e_unit" class="form-control" ng-model="selectedUnit" ng-change="loadThana(selectedUnit)">
                                    <option value="">--Select a Unit--</option>
                                    <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="e_thana" class="control-label">Select a Thana&nbsp;
                                    <img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}" width="16"></label>
                                <select ng-disabled="loadingThana" id="e_thana" class="form-control" ng-model="selectedThana" ng-change="loadKpi(selectedThana)">
                                    <option value="">--Select a Thana--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="e_kpi" class="control-label">Select a KPI&nbsp;
                                    <img ng-show="loadingKpi" src="{{asset('dist/img/facebook.gif')}}" width="16"></label>
                                <select ng-disabled="loadingKpi" id="e_kpi" class="form-control" ng-model="selectedKpi">
                                    <option value="">--Select a KPI--</option>
                                    <option ng-repeat="k in kpis" value="[[k.id]]">[[k.kpi_name]]</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" ng-disabled="!j_date||!r_date||!ansarId||!selectedUnit||!selectedThana||!selectedKpi||isVerified||isVerifying" confirm-dialog><img ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}" width="16" style="margin-top: -2px">Embodied Ansar</button>
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