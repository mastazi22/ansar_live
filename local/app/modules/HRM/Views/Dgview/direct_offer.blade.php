@extends('template.master')
@section('title','Direct Offer')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_offer') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('DirectOfferController', function ($scope,$http,notificationService) {
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
                    url:'{{URL::route('ansar_detail_info')}}',
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
            $scope.cancelOffer = function (e) {
                e.preventDefault()
                $scope.error=false;
                $scope.offerCanceling = true;
                $http({
                    method:'post',
                    url:'{{URL::to('HRM/direct_offer_cancel')}}',
                    data:{ansar_id:$scope.ansarId}
                }).then(function (response) {
                    $scope.error=false;
                    $scope.submitResult  = response.data;
                    if(response.data.status){
                        notificationService.notify('success',response.data.message)
                        $scope.ansarId = "";
                        $scope.ansarDetail = {}
                    }
                    else{
                        notificationService.notify('error',response.data.message)
                    }
                    $scope.offerCanceling = false;
                },function (response) {
                    $scope.offerCanceling = false;
                    if(response.status==500)$scope.error = true;
                    $scope.submitResult  = response.data
                })
            }
            $scope.ppp = function(){
//                alert(moment().format("DD-MMM-YYYY"))
                $("input[name='offer_date']").val(moment().format("DD-MMM-YYYY HH:mm:ss"));
            }
        })
    </script>
    <div ng-controller="DirectOfferController">
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <form action="{{URL::to('HRM/direct_offer')}}" method="post" form-submit reset-except="offer_date" on-reset="ppp()" errors="errors" loading="loadingSubmit" confirm-box="true" message="Are you sure want to offer this Ansar">
                                <div class="form-group">
                                    <label for="ansar_id" class="control-label">Ansar ID to send Offer</label>
                                    <div class="input-group">
                                        <input type="text" name="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId">
                                        <span class="input-group-btn">
                                            <button class="btn btn-secondary" ng-click="loadAnsarDetail(ansarId)" type="button">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <p class="text text-danger" ng-if="errors.ansar_id!=undefined">
                                        [[errors.ansar_id[0] ]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="district" class="control-label">Select District to send Offer&nbsp;
                                        <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}" width="16"></label>
                                    <select class="form-control" name="unit_id" ng-model="selectedDistrict" ng-disabled="loadingDistrict">
                                        <option value="">--@lang('title.unit')--</option>
                                        <option ng-repeat="d in districts"  value="[[d.id]]">[[d.unit_name_bng]]</option>
                                    </select>
                                    <p class="text text-danger" ng-if="errors.unit_id!=undefined">
                                        [[errors.unit_id[0] ]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="date" class="control-label">Offer Date</label>
                                    <input type="text" name="offer_date" id="date" class="form-control" placeholder="Offer Date" ng-model="date">
                                    <p class="text text-danger" ng-if="errors.offer_date!=undefined">
                                        [[errors.offer_date[0] ]]
                                    </p>
                                </div>
                                <button class="btn btn-primary" ng-disabled="loadingSubmit" type="submit">
                                    <i ng-show="loadingSubmit" class="fa fa-spinner fa-pulse"></i>
                                    <i ng-hide="loadingSubmit" class="fa fa-send"></i>
                                    Send Offer</button>
                                <a class="btn btn-danger" ng-disabled="offerCanceling" href="#" ng-click="cancelOffer($event)">
                                    <i ng-show="offerCanceling" class="fa fa-spinner fa-pulse"></i>
                                    <i ng-hide="offerCanceling" class="fa fa-times"></i>&nbsp;Cancel Offer
                                </a>

                            </form>
                            </div>
                        <div class="col-sm-8"
                             style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <template-list data="ansarDetail" key="ansar_history"></template-list>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $(document).ready(function () {
            $("#date").datepicker({
                dateFormat:'dd-M-yy',
                onSelect:function (dateText) {
                    var d = new Date(); // for now

                    var h = d.getHours();
                    h = (h < 10) ? ("0" + h) : h ;

                    var m = d.getMinutes();
                    m = (m < 10) ? ("0" + m) : m ;

                    var s = d.getSeconds();
                    s = (s < 10) ? ("0" + s) : s ;

                    dateText = dateText + " " + h + ":" + m + ":" + s;

                    $('#date').val(dateText);
                }
            })
			$("input[name='offer_date']").val(moment().format("DD-MMM-YYYY HH:mm:ss"));
        })
    </script>
@stop