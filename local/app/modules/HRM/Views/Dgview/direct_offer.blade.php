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
    </div>
    <script>
        $(document).ready(function () {
            $("#date").datePicker()
        })
    </script>
@stop