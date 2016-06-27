@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http) {
            $scope.fromDate = "";
            $scope.toDate = "";
            $scope.ansars = [];
            $scope.isLoading = false;
            $scope.isBlocking = [];
            $scope.noOfRejection="10"
            $scope.status = "";
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
                    $scope.isLoading = false
                }, function (response) {
                    alert('ERROR!!!!!')
                    $scope.isLoading = false
                })
            }
            $scope.blockAnsar = function (id,i) {
                $scope.isBlocking[i] = true;
                $http({
                    method:'post',
                    url:"{{action('BlockBlackController@blockListEntry')}}",
                    data:{
                        ansar_status:$scope.status,
                        ansar_id:id,
                        block_date:moment().format("d-MMM-YYYY"),
                        block_comment:"",
                        from_id:0
                    }
                }).then(function (response) {
                    $scope.isBlocking[i] = false;
                    $scope.ansars[i].block_list_status=1;
                    console.log(response.data)
                }, function (response) {
                    $scope.isBlocking[i] = false;
                })
            }
        })
        $(document).ready(function (e) {
            $(".showdate").datePicker(true)
        })
    </script>
    <div class="content-wrapper" style="min-height: 490px" ng-controller="ReportGuardSearchController">
        <section class="content">
            <div class="box box-solid">
                <div class="nav-tabs-custom" style="background-color: transparent">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#">Rejected/Not Respond Offer List</a>
                        </li>
                        <li class="pull-right">

                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">From Date</label>
                                        <input type="text" class="form-control showdate" ng-model="fromDate"
                                               placeholder="From Date">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">To Date</label>
                                        <input type="text" ng-model="toDate" class="form-control showdate"
                                               placeholder="To Date">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">No Of Offer Rejected/Not Respond</label>
                                        <input type="text" ng-model="noOfRejection" ng-change="noOfRejection = noOfRejection<1?1:noOfRejection" class="form-control"
                                               placeholder="No of Rejection/Not Respond">
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
                                        <th>Sl. No</th>
                                        <th>Ansar Id</th>
                                        <th>Name</th>
                                        <th>Rank</th>
                                        <th>District</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr ng-if="ansars.length<=0">
                                        <th colspan="7">No Ansar Found</th>
                                    </tr>
                                    <tr ng-if="ansars.length>0" ng-repeat="a in ansars">
                                        <td>[[$index+1]]</td>
                                        <td>[[a.ansar_id]]</td>
                                        <td>[[a.ansar_name_bng]]</td>
                                        <td>[[a.name_bng]]</td>
                                        <td>[[a.unit_name_bng]]</td>
                                        <td ng-if="1==a.block_list_status" ng-init="status='Blocked'">Blocked</td>
                                        <td ng-if="0==a.block_list_status">
                                            <span ng-if="1==a.free_status"  ng-init="status='Free'">Free</span>
                                            <span ng-if="1==a.pannel_status"  ng-init="status='Panneled'">Panel</span>
                                            <span ng-if="1==a.offer_sms_status"  ng-init="status='Offer'">Offered</span>
                                            <span ng-if="1==a.embodied_status"  ng-init="status='Embodded'">Embodied</span>
                                            <span ng-if="1==a.freezing_status"  ng-init="status='Freeze'">Freeze</span>
                                            <span ng-if="1==a.early_retierment_statBlockedus"  ng-init="status='EarlyRet'">Early retirement</span>
                                            {{--<span ng-if="1==a.block_list_status"  ng-init="status='Blocked'"></span>--}}
                                            <span ng-if="1==a.black_list_status"  ng-init="status='Blacked'">Blacked</span>
                                            <span ng-if="1==a.rest_status"  ng-init="status='Rest'">Rest</span>
                                            <span ng-if="1==a.retierment_status"  ng-init="status='Retirement'">Retirement</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-xs" ng-click="blockAnsar(a.ansar_id,$index)" ng-disabled="isBlocking[$index]||a.block_list_status==1||a.black_list_status==1">
                                                <i class="fa" ng-class="{'fa-close':!isBlocking[$index],'fa-spinner fa-pulse':isBlocking[$index]}"></i>&nbsp;Block
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@stop