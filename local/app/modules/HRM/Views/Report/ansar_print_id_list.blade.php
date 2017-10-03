@extends('template.master')
@section('title','Printed ID Card List')
@section('breadcrumb')
    {!! Breadcrumbs::render('id_card') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#from-date').datepicker({                dateFormat:'dd-M-yy'            })();
            $("#to-date").datepicker({                dateFormat:'dd-M-yy'            })();

        })
        GlobalApp.controller('AnsarIdCard', function ($scope,$http) {
            $scope.ansars = [];
            $scope.loading = [];
            $scope.toDate = ""
            $scope.fromDate = ""
            $scope.isLoading = false;
            $scope.loadAnsar = function () {
               // alert(document.getElementById('from-date').value+" "+t)
                $scope.isLoading = true;
                $http({
                    url:'{{URL::to('HRM/get_print_id_list')}}',
                    method:'get',
                    params:{f_date:$scope.fromDate,t_date:$scope.toDate}
                }).then(function (response) {
                    console.log(response.data);
                    $scope.error = undefined;
                    $scope.internalEerror = undefined;
                    $scope.ansars = response.data.ansars;
                    $scope.isLoading = false;
                }, function (response) {
                    $scope.isLoading = false;
                    if(response.status==400) {
                        $scope.error = response.data;
                    }
                    else if(response.status==500){
                        $scope.internalEerror = "Internal server error(500)";
                    }
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
                    if(response.status==400) {
                        $scope.internalEerror = response.data;
                    }
                    else if(response.status==500){
                        $scope.internalEerror = "Internal server error(500)";
                    }
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
                    if(response.status==400) {
                        $scope.internalEerror = response.data;
                    }
                    else if(response.status==500){
                        $scope.internalEerror = "Internal server error(500)";
                    }
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
                <div class="box-body">
                    <div class="alert alert-danger" ng-if="internalError!=undefined">
                        <i class="fa fa-warning"></i>&nbsp;[[internalError]]
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">
                                    From Date
                                </label>
                                <input type="text" ng-model="fromDate" id="from-date" class="form-control" placeholder="From Date">
                                <p class="text text-danger" ng-if="error!=undefined&&error.f_date!=undefined">[[error.f_date[0] ]]</p>
                            </div>

                        </div>
                        <div class="col-sm-1" style="    text-align: center;font-size: 1.2em;padding: 0;width: auto;">
                            <label class="control-label" style="display: block">&nbsp;</label>
                            to
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">
                                    To Date
                                </label>
                                <input type="text" ng-model="toDate"  id="to-date" class="form-control" placeholder="To Date">
                                <p class="text text-danger" ng-if="error!=undefined&&error.t_date!=undefined">[[error.t_date[0] ]]</p>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label" style="display: block">&nbsp;</label>
                            <button class="btn btn-primary" ng-disabled="isLoading"  ng-click="loadAnsar()">
                                <i ng-if="isLoading" class="fa fa-spinner fa-pulse"></i>&nbsp;View Printed ID Card List</button>
                        </div>
                    </div>
                    <div class="form-group">

                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <caption>
                                <table-search q="q" results="results" ></table-search>
                            </caption>
                            <tr>
                                <th>SL. No</th>
                                <th>Ansar ID</th>
                                <th>Issue Date</th>
                                <th>Expire Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-show="ansars.length<=0||results==undefined||results.length<=0">
                                <td colspan="6" class="warning">No information found</td>
                            </tr>
                            <tr ng-show="ansars.length>0" ng-repeat="a in ansars|filter:q as results">
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
        </section>
    </div>
@stop