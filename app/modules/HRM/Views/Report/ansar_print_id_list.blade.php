@extends('template.master')
@section('content')
    <script>
        $(document).ready(function () {
            $('#from-date').datePicker();
            $("#to-date").datePicker();

        })
        GlobalApp.controller('AnsarIdCard', function ($scope,$http) {
            $scope.ansars = [];
            $scope.loading = [];
            $scope.toDate = ""
            $scope.fromDate = ""
            $scope.loadAnsar = function () {
               // alert(document.getElementById('from-date').value+" "+t)
                $http({
                    url:'{{URL::to('HRM/get_print_id_list')}}',
                    method:'get',
                    params:{f_date:$scope.fromDate,t_date:$scope.toDate}
                }).then(function (response) {
                    console.log(response.data);
                    $scope.ansars = response.data.ansars;
                }, function (response) {

                })
            }
            $scope.blockAnsarCard = function (a) {
                $scope.loading[a] = true;
                $http({
                    url:'{{URL::to('HRM/change_ansar_card_status')}}',
                    method:'post',
                    data:{action:'block',ansar_id: $scope.ansars[a].ansar_id}
                }).then(function (response) {
                    if(response.data.status==1) $scope.ansars[a].status = 0
                    $scope.loading[a] = false;
                }, function (resonse) {
                    $scope.loading[a] = false;
                })
            }
            $scope.activeAnsarCard = function (a) {
                $scope.loading[a] = true;
                $http({
                    url:'{{URL::to('HRM/change_ansar_card_status')}}',
                    method:'post',
                    data:{action:'active',ansar_id: $scope.ansars[a].ansar_id}
                }).then(function (response) {
                    if(response.data.status==1) $scope.ansars[a].status = 1
                    $scope.loading[a] = false;
                }, function (resonse) {
                    $scope.loading[a] = false;
                })
            }
        })
        GlobalApp.directive('confirmDialog', function () {
            return{
                restrict:'A',
                link: function (scope,elem,attr) {
//                    $(elem).on('click', function () {
//                        alert(JSON.stringify(attr))
//                    })
                    $(elem).confirmDialog({
                        message: 'Are you sure?',
                        ok_button_text:'Yes',
                        cancel_button_text:'No,Thanks',
                        ok_callback: function (element) {
                            var b = JSON.parse(attr.confirmDialog)
                            switch(b.type){
                                case 'block':
                                        console.log(b.a)
                                    scope.blockAnsarCard(b.a)
                                    break;
                                case 'active':
                                    scope.activeAnsarCard(b.a)
                                    break;
                            }
                            //alert(attr.confirmDialog)
                            //scope.makeEmbodied();
                        },
                        cancel_callback: function (element) {
                            alert('asadsadad')
                        }
                    })
                }
            }

        })
    </script>
    <div ng-controller="AnsarIdCard">
        <section class="content">
            <div class="box box-solid">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a>ansar List</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            From Date
                                        </label>
                                        <input type="text" ng-model="fromDate" id="from-date" class="form-control" placeholder="From Date">
                                    </div>

                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            To Date
                                        </label>
                                        <input type="text" ng-model="toDate"  id="to-date" class="form-control" placeholder="To Date">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary"  ng-click="loadAnsar()">View Printed ID Card List</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>SL. No</th>
                                    <th>Ansar ID</th>
                                    <th>Issue Date</th>
                                    <th>Expire Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                <tr ng-show="ansars.length<=0">
                                    <td colspan="6" class="warning">No List Found</td>
                                </tr>
                                <tr ng-show="ansars.length>0" ng-repeat="a in ansars">
                                    <td>[[$index+1]]</td>
                                    <td>[[a.ansar_id]]</td>
                                    <td>[[a.issue_date]]</td>
                                    <td>[[a.expire_date]]</td>
                                    <td>[[a.status==1?'Active':'Blocked']]</td>
                                    <td>
                                        <button class="btn btn-danger btn-xs" ng-if="a.status==1" confirm-dialog='{"a":[[$index]],"type":"block"}'>
                                            <span class="fa fa-ban" ng-show="!loading[$index]"></span><span class="fa fa-spinner fa-pulse" ng-show="loading[$index]"></span>Block
                                        </button>
                                        <button class="btn btn-success btn-xs" ng-if="a.status==0" confirm-dialog='{"a":[[$index]],"type":"active"}'>
                                            <span class="fa fa-check" ng-show="!loading[$index]"></span><span class="fa fa-spinner fa-pulse" ng-show="loading[$index]"></span>Active
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop