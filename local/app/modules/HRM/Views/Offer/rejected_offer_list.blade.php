@extends('template.master')
@section('title','Rejected Offer List')
@section('breadcrumb')
    {!! Breadcrumbs::render('rejected_offer_list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http,notificationService) {
            $scope.fromDate = "";
            $scope.toDate = "";
            $scope.checked = [];
            $scope.multiData = {}
            $scope.checkedAll = false;
            $scope.ansars = [];
            $scope.isLoading = false;
            $scope.isBlocking = [];
            $scope.noOfRejection="10"
            $scope.p = {status :"",statuss:[]};
            $scope.getRejectedAnsarList = function () {
                $scope.isLoading = true
                $http({
                    method: 'get',
                    params: {
                        from_date: $scope.fromDate,
                        to_date: $scope.toDate,
                        rejection_no:$scope.noOfRejection
                    },
                    url:'{{URL::to('HRM/get_rejected_ansar_list')}}'
                }).then(function (response) {
                    $scope.ansars = response.data;
                    $scope.checked = Array.apply(null,Array(response.data.length)).map(Boolean.prototype.valueOf,false);
                    $scope.isLoading = false
                    $scope.internalError = undefined;
                    $scope.error = undefined;
                }, function (response) {
                    //alert('ERROR!!!!!')
                    $scope.isLoading = false
                    if(response.status==400) {
                        $scope.error = response.data;
                    }
                    else if(response.status==500){
                        $scope.internalEerror = response.data;
                    }
                    console.log(response)
                })
            }
            $scope.blockAnsar = function () {
                var i = $scope.ansars.indexOf($scope.blockedAnsar)
                console.log($scope.blockedAnsar);
//                return;
                if(i<0) return;
                $scope.isBlocking[i] = true;
                $scope.blocking = true;
                $http({
                    method:'post',
                    url:"{{URL::route('blocklist_entry')}}",
                    data:{
                        ansar_status:$scope.p.statuss[i],
                        ansar_id:$scope.blockedAnsar.ansar_id,
                        block_date:moment().format("d-MMM-YYYY"),
                        block_comment:$scope.blockReason==undefined?'':$scope.blockReason,
                        from_id:0
                    }
                }).then(function (response) {
                    $scope.isBlocking[i] = false;
//                    $scope.ansars[i].block_list_status=1;
                    if(response.data.status){
                        notificationService.notify('success',response.data.message)
                        $scope.ansars[i].block_list_status=1;
                        $("#block-modal").modal('hide')
                    }
                    else{
                        notificationService.notify('error',response.data.message)
                    }
                    $scope.blocking = false;
                }, function (response) {
                    $scope.isBlocking[i] = false;
                    notificationService.notify('error',"An unknown error occur. Error code : "+response.status);
                    $scope.blocking = false;
                })
            }
            $scope.blockMultiAnsar = function () {
                $scope.blocking = true;
                $scope.multiData['block_comment'] = $scope.blockReason==undefined?'':$scope.blockReason
                $scope.multiData['from_id'] = 0
                $http({
                    method:'post',
                    url:"{{URL::route('multi_blocklist_entry')}}",
                    data:angular.toJson($scope.multiData)
                }).then(function (response) {
                    console.log(response.data)
                    for(var i = 0;i<$scope.checked.length;i++) {
                        if($scope.checked[i]!==false) $scope.ansars[i].block_list_status = 1;
                    }
                    if(response.data.status){
                        notificationService.notify('success',response.data.message)
                        $scope.checked = Array.apply(null, Array($scope.ansars.length)).map(Boolean.prototype.valueOf, false);
                        $("#multi-block-modal").modal('hide')
                    }
                    else{
                        notificationService.notify('error',response.data.message)
                    }
                    $scope.blocking = false;
                }, function (response) {
//                    $scope.isBlocking[i] = false;
                    notificationService.notify('error',"An unknown error occur. Error code : "+response.status);
                    $scope.blocking = false;
                })
            }
            $scope.blockModal = function (a) {
                $scope.blockedAnsar = a;
                $("#block-modal").modal('show')
            }
            $scope.blockSelected = function () {
                $scope.multiData = {};
                $scope.multiData["ansar"] = [];
                $scope.multiData["block_date"] = moment().format("d-MMM-YYYY");
                for(var i = 0;i<$scope.checked.length;i++) {
                    if($scope.checked[i]!==false) $scope.multiData["ansar"].push({ansar_id:$scope.ansars[$scope.checked[i]].ansar_id,status:$scope.p.statuss[$scope.checked[i]]})
                }
                $("#multi-block-modal").modal('show')
            }
            $scope.$watch('checked', function (n, o) {
                if (n.length <= 0) return;
                var r = n.every(function (i) {
                    return i !== false;
                })
                $scope.checkedAll = r;
            }, true)
            $scope.checkAll = function () {
                if (!$scope.checkedAll)$scope.checked = Array.apply(null, Array($scope.ansars.length)).map(Boolean.prototype.valueOf, false);
                else {
                    $scope.ansars.forEach(function (value, index) {
                        $scope.checked[index] = index;
                    })
                }
                console.log($scope.checked)
            }
        })
        $(document).ready(function (e) {
            $(".showdate").datePicker(true)
        })
    </script>
    <div ng-controller="ReportGuardSearchController">
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="alert alert-danger" ng-if="internalError!=undefined">
                        <i class="fa fa-warning"></i>&nbsp;[[internalError]]
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">From Date</label>
                                <input type="text" class="form-control showdate" ng-model="fromDate"
                                       placeholder="From Date">
                                <p class="text text-danger" ng-if="error!=undefined&&error.from_date!=undefined">[[error.from_date[0] ]]</p>
                            </div>
                        </div>
                        <div class="col-sm-1" style="    text-align: center;font-size: 1.2em;padding: 0;width: auto;">
                            <label class="control-label" style="display: block">&nbsp;</label>
                            to
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">To Date</label>
                                <input type="text" ng-model="toDate" class="form-control showdate"
                                       placeholder="To Date">
                                <p class="text text-danger" ng-if="error!=undefined&&error.to_date!=undefined">[[error.to_date[0] ]]</p>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">No. Of Rejected/Unresponded Offers</label>
                                <input type="text" ng-model="noOfRejection" ng-change="noOfRejection = noOfRejection<1?1:noOfRejection" class="form-control"
                                       placeholder="No of Rejection/Not Respond">
                                <p class="text text-danger" ng-if="error!=undefined&&error.rejection_no!=undefined">[[error.rejection_no[0] ]]</p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <button class="btn btn-primary" ng-disabled="isLoading"
                                        ng-click="getRejectedAnsarList()"><i class="fa" ng-class="{'fa-download':!isLoading,'fa-spinner fa-pulse':isLoading}"></i>&nbsp;Load
                                    Ansar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th><input type="checkbox" ng-model="checkedAll"
                                                               ng-change="checkAll()"></th>
                                <th>Sl. No</th>
                                <th>Ansar ID</th>
                                <th>Name</th>
                                <th>Rank</th>
                                <th>District</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-if="ansars.length<=0" class="warning">
                                <th colspan="8">No information found</th>
                            </tr>
                            <tr ng-if="ansars.length>0" ng-repeat="a in ansars">
                                <td><input type="checkbox" ng-disabled="isBlocking[$index]||a.block_list_status==1||a.black_list_status==1"  ng-true-value="[[$index]]" ng-false-value="false"
                                           ng-model="checked[$index]"></td>
                                <td>[[$index+1]]</td>
                                <td>[[a.ansar_id]]</td>
                                <td>[[a.ansar_name_bng]]</td>
                                <td>[[a.name_bng]]</td>
                                <td>[[a.unit_name_bng]]</td>
                                <td ng-if="1==a.block_list_status" ng-init="p.status='Blocked'">Blocked</td>
                                <td ng-if="0==a.block_list_status">
                                    <span ng-if="1==a.free_status"  ng-init="p.status=p.statuss[$index]='Free'">Free</span>
                                    <span ng-if="1==a.pannel_status"  ng-init="p.status=p.statuss[$index]='Paneled'">Panel</span>
                                    <span ng-if="1==a.offer_sms_status"  ng-init="p.status=p.statuss[$index]='Offer'">Offered</span>
                                    <span ng-if="1==a.embodied_status"  ng-init="p.status=p.statuss[$index]='Embodied'">Embodied</span>
                                    <span ng-if="1==a.freezing_status"  ng-init="p.status=p.statuss[$index]='Freeze'">Freeze</span>
                                    <span ng-if="1==a.early_retierment_statBlockedus"  ng-init="p.status=p.statuss[$index]='EarlyRet'">Early retirement</span>
                                    {{--<span ng-if="1==a.block_list_status"  ng-init="status='Blocked'"></span>--}}
                                    <span ng-if="1==a.black_list_status"  ng-init="p.status=p.statuss[$index]='Blacked'">Blacked</span>
                                    <span ng-if="1==a.rest_status"  ng-init="p.status=p.statuss[$index]='Rest'">Rest</span>
                                    <span ng-if="1==a.retierment_status"  ng-init="p.status=p.statuss[$index]='Retirement'">Retirement</span>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-xs" ng-click="blockModal(a)" ng-disabled="isBlocking[$index]||a.block_list_status==1||a.black_list_status==1">
                                        <i class="fa fa-remove"></i>&nbsp;Block
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" ng-click="blockSelected()"><i class="fa fa-remove"></i>&nbsp;&nbsp;Block Selected</button>
                </div>
            </div>
        </section>
        <div class="modal fade" id="block-modal" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Block</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Block Reason</label>
                            <input type="text" placeholder="Enter block reason" ng-model="blockReason" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="pull-right btn btn-primary" ng-click="blockAnsar()">
                            <i class="fa fa-spinner fa-pulse" ng-if="blocking"></i>&nbsp;&nbsp;Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="multi-block-modal" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Block</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Block Reason</label>
                            <input type="text" placeholder="Enter block reason" ng-model="blockReason" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="pull-right btn btn-primary" ng-click="blockMultiAnsar()">
                            <i class="fa fa-spinner fa-pulse" ng-if="blocking"></i>&nbsp;&nbsp;Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop