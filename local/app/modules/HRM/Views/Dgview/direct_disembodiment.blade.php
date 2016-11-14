@extends('template.master')
@section('title','Direct Dis-Embodiment')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_disembodiment') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {

        })
        GlobalApp.controller('DirectEmbodimentController', function ($scope,$http,$sce) {
            $scope.ansarId = "";
            $scope.dis_date = "";
            $scope.selectedReason = "";
            $scope.comment = "";
            $scope.ansarDetail = {}
            $scope.disEmbodimentReason = []
            $scope.loadingReason = true;
            $scope.loadingAnsar = false;
            $scope.loadingSubmit = false;
            $scope.error = ""
            $scope.ansar_ids = [];
            $scope.totalLength =  0;
            $scope.memorandumId = ''
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.exist = false;
            $scope.submitResult = {}
            $http({
                method:'get',
                url:'{{URL::to('HRM/load_disembodiment_reason')}}'
            }).then(function (response) {
                $scope.disEmbodimentReason = response.data
                $scope.loadingReason = false;
            })
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{URL::to('HRM/direct_offer_ansar_detail')}}',
                    params:{ansar_id:id,type:'EMBODIED'}
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
            $scope.makeDisEmbodied = function () {
                $scope.loadingSubmit = true;
                var rd = new Date($scope.dis_date);
                var rds = rd.getFullYear()+"-"+(rd.getMonth()+1)+"-"+rd.getDate();
                $http({
                    url:'{{URL::to('HRM/direct_disembodiment_submit')}}',
                    method:'post',
                    data:{
                        ansar_id:$scope.ansarId,
                        dis_date:rds,
                        comment:$scope.comment,
                        reason:$scope.selectedReason
                    }
                }).then(function (response) {
                    console.log(response)
                    $scope.submitResult = response.data;
                    $scope.loadingSubmit = false;
                },function (response) {
                    console.log(response);
                    $scope.error = $sce.trustAsHtml(response.data);
                    $scope.loadingSubmit = false;
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
                    $scope.isVerified = false;
                    $scope.isVerifying = false;
                })
            }
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
                        message: 'Are you sure want to dis-embodied this ansar',
                        ok_button_text:'Dis-Embodied',
                        cancel_button_text:'No,Thanks',
                        ok_callback: function (element) {
                            if(scope.ansarDetail.asi.embodied_status==0){
                                $('body').notifyDialog({type: 'error', message: 'You can`t Dis-Embodied this ansar. Because he is not embodied.'}).showDialog()
                                return;
                            }
                            else if(scope.ansarDetail.asi.block_list_status==1){
                                $('body').notifyDialog({type: 'error', message: 'You can`t Dis-Embodied to this ansar. Because he is embodied but blocked.'}).showDialog()
                                return;
                            }
                            scope.makeDisEmbodied();
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
                                <label for="mem_id" class="control-label">Memorandum no.&nbsp;<i class="fa fa-spinner fa-pulse" ng-show="isVerifying"></i>
                                    <span class="text-danger" ng-if="isVerified">This id already taken</span>
                                </label>
                                <input type="text" name="mem_id" id="mem_id" class="form-control" placeholder="Enter Memorandum no." ng-model="memorandumId" ng-blur="verifyMemorandumId()">
                            </div>
                            <div class="form-group">
                                <label for="dis_date" class="control-label">Disembodiment Date</label>
                                <input type="text" date-picker placeholder="Dis-Embodied Date" id="dis_date" class="form-control" ng-model="dis_date">
                            </div>
                            <div class="form-group">
                                <label for="dis-reason" class="control-label">Disembodiment Reason&nbsp;
                                    <img ng-show="loadingReason" src="{{asset('dist/img/facebook.gif')}}" width="16"></label>
                                <select ng-disabled="loadingReason" id="dis-reason" class="form-control" ng-model="selectedReason" ng-change="loadThana(selectedReason)">
                                    <option value="">--@lang('title.reason')--</option>
                                    <option ng-repeat="u in disEmbodimentReason" value="[[u.id]]">[[u.reason_in_bng]]</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="comment" class="control-label">Comment for Disembodiment</label>
                                <textarea name="comment" id="comment" class="form-control" placeholder="Enter Comment" ng-model="comment"></textarea>
                            </div>
                            <button class="btn btn-primary" ng-disabled="!dis_date||!ansarId||!selectedReason||isVerified||isVerifying" confirm-dialog><img ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}" width="16" style="margin-top: -2px">Dis-Embodied Ansar</button>
                        </div>
                        <div class="col-sm-8" style="min-height: 400px;border-left: 1px solid #CCCCCC">
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
        <div ng-bind-html="error">

        </div>
    </div>
@stop