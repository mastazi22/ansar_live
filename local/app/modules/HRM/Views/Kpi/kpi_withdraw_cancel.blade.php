{{--User: ShreyaS--}}
{{--Date: 3/16/2016--}}
{{--Time: 12:05 PM--}}


@extends('template.master')
@section('title','Cancel KPI Withdrawal')
@section('breadcrumb')
    {!! Breadcrumbs::render('kpi_withdraw_cancel') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('KpiWithdrawCancelController', function ($scope, $http, $sce, httpService, notificationService) {
            $scope.params = ''
            $scope.total = 0;
            $scope.formData = {};
            $scope.showLoadingScreen = true;
            $scope.numOfPage = 0;
            $scope.allLoading = false;
            $scope.divisions = [];
            $scope.districts = [];
            $scope.thanas = [];
            $scope.guards = [];
            $scope.kpis = [];
            $scope.itemPerPage = 20;
            $scope.currentPage = 0;
            $scope.pages = [];
            $scope.loadingPage = [];
            $scope.verified = [];
            $scope.withdrawId=''
            $scope.verifying = [];
            $scope.errorMessage = '';
            $scope.errorFound = 0;
            $scope.loadPagination = function () {
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    })
                    $scope.loadingPage[i] = false;
                }
                if ($scope.numOfPage > 0)$scope.loadPage($scope.pages[0]);
                else $scope.loadPage({pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'});
            }
            $scope.loadPage = function (page, $event) {
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page.pageNum;
                $scope.loadingPage[page.pageNum] = true;
                $http({
                    url: '{{URL::route('inactive_kpi_list')}}',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        division: $scope.params.range,
                        unit: $scope.params.unit,
                        thana: $scope.params.thana,
                        view: 'view'
                    }
                }).then(function (response) {
                    $scope.kpis = response.data.kpis;
                    console.log($scope.kpis)
//                    $compile($scope.ansars)
                    $scope.loadingPage[page.pageNum] = false;
                })
            }
            $scope.loadTotal = function () {
                $scope.allLoading = true;
                //alert($scope.selectedDivision)
                $http({

                    url: '{{URL::route('inactive_kpi_list')}}',
                    method: 'get',
                    params: {
                        division: $scope.params.range,
                        unit: $scope.params.unit,
                        thana: $scope.params.thana,
                        view: 'count'
                    }
                }).then(function (response) {
                    $scope.errorFound = 0;
                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                    $scope.allLoading = false;
                    //alert($scope.total)
                }, function (response) {
                    $scope.errorFound = 1;
                    $scope.total = 0;
                    $scope.kpis = [];
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='" + $('.table').find('tr').find('th').length + "'>" + response.data + "</td></tr>");
                    $scope.pages = [];
                    $scope.allLoading = false;
                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            $scope.verify = function (id, i) {
                $scope.verifying[i] = true;
                $http({
                    url: "{{URL::to('HRM/active_kpi')}}/" + id,
                    data: {verified_id: id},
                    method: 'post'
                }).then(function (response) {
                    //alert(JSON.stringify(response.data));
//                    console.log(response.data);
                    $scope.verifying[parseInt(i)] = false;
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message)
                        $scope.loadTotal();
                    }
                    else {
                        notificationService.notify('error', response.data.message)
                    }
//                    $scope.verified++;
                }, function (resonse) {
                    $scope.verifying[parseInt(i)] = false;
                    notificationService.notify('error', "An undefined error occur. Error code-" + response.status)
                })
            }
            $scope.ppp = function(id,i){
                $scope.withdrawId = id;
                $scope.kpiIndex = i;
            }
            $scope.cancelWithdraw = function (id) {
                $scope.canceling = true;
                $scope.error = undefined;
                $http({
                    url: "{{URL::to('HRM/kpi-withdraw-cancel-update')}}/" + id,
                    data: {kpi_id: id},
                    method: 'post'
                }).then(function (response) {
                    $scope.canceling = false;
                    if (response.data.status) {
                        $("#withdraw-cancel").modal('hide')
                        notificationService.notify('success', response.data.message)
                        $scope.loadTotal();
                    }
                    else {
                        notificationService.notify('error', response.data.message)
                    }
                }, function (response) {
                    $scope.canceling = false;
                    if (response.status == 422) {
                        $scope.error = response.data;
                        return;
                    }
                    notificationService.notify('error', "An undefined error occur. Error code-" + response.status)
                })
            }
            $scope.withdrawDateUpdate = function (id) {
                $scope.updating = true;
                $scope.formData.kpi_id = id;
//                console.log($scope.formData)
                $scope.error = undefined;
                $http({
                    url: "{{URL::to('HRM/withdraw-date-update')}}/" + id,
                    data: angular.toJson($scope.formData),
                    method: 'post'
                }).then(function (response) {
                    console.log(response.data);
                    $scope.updating = false;
                    if (response.data.status) {
                        $("#withdraw-date-update").modal('hide')
                        notificationService.notify('success', response.data.message)
                        $scope.loadTotal();
                    }
                    else {
                        notificationService.notify('error', response.data.message)
                    }
                }, function (response) {
                    $scope.updating = false;
                    if (response.status == 422) {
                        $scope.error = response.data;
                        return;
                    }
                    notificationService.notify('error', "An undefined error occur. Error code-" + response.status)
                })
            }
        })
    </script>
    <div ng-controller="KpiWithdrawCancelController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit','thana']"
                            type="all"
                            range-change="loadTotal()"
                            unit-change="loadTotal()"
                            thana-change="loadTotal()"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                            data = "params"
                            on-load="loadTotal()"
                    ></filter-template>
                    <h4>Total KPI: [[total.toLocaleString()]]</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>KPI Name</th>
                                <th>Division</th>
                                <th>Unit</th>
                                <th>Thana</th>
                                <th>Withdraw Status</th>
                                <th style="width:120px;">Action</th>
                            </tr>
                            <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                            <tbody>
                            <tr ng-if="kpis.length==0&&errorFound==0">
                                <td colspan="8" class="warning no-ansar">
                                    No KPI is available to show.
                                </td>
                            </tr>
                            <tr ng-if="kpis.length>0" ng-repeat="a in kpis">
                                <td>
                                    [[((currentPage)*itemPerPage)+$index+1]]
                                </td>
                                <td>
                                    [[a.kpi_name]]
                                </td>
                                <td>
                                    [[a.division]]
                                </td>
                                <td>
                                    [[a.unit]]
                                </td>
                                <td>
                                    [[a.thana]]
                                </td>
                                <td>
                                    [[a.withdraw_status==1?"Already Withdraw":a.date!=null?"Withdraw on
                                    "+(a.date|dateformat:'DD-MMM-YYYY'):"Inactive"]]
                                </td>
                                <td style="vertical-align: middle">
                                    <div class="col-xs-1">
                                        <a href="" data-toggle="modal" ng-click="ppp(a.id,$index)"
                                           data-target="#withdraw-date-update" ng-disabled="a.withdraw_status==1||a.status==0"
                                           class="btn btn-info btn-xs" title="Date Update">
                                            <i class="fa fa-calendar"></i>
                                        </a>
                                    </div>
                                    <div class="col-xs-1">
                                        <a href="" data-toggle="modal" ng-click="ppp(a.id)"
                                           data-target="#withdraw-cancel"
                                           ng-disabled="a.withdraw_status==1||a.status==0" class="btn btn-danger btn-xs"
                                           title="Withdraw Cancel">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                    </div>
                                    <div class="col-xs-1">
                                        <a href="" ng-click="verify(a.id,$index)"
                                           ng-disabled="(a.withdraw_status==0&&a.status==1&&a.date!=null)||verifying[$index]"
                                           class="btn btn-info btn-xs" title="Restore">
                                            <i class="fa fa-check" ng-if="!verifying[$index]"></i>
                                            <i class="fa fa-spinner fa-pulse" ng-if="verifying[$index]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="table_pagination" ng-if="pages.length>1">
                            <ul class="pagination">
                                <li ng-class="{disabled:currentPage == 0}">
                                    <a href="#" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                </li>
                                <li ng-class="{disabled:currentPage == 0}">
                                    <a href="#" ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                </li>
                                <li ng-repeat="page in pages|filter:filterMiddlePage"
                                    ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                    <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                    <a href="#" ng-click="loadPage(page,$event)"
                                       ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                            <span ng-show="loadingPage[page.pageNum]" style="position: relative"><i
                                                        class="fa fa-spinner fa-pulse"
                                                        style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                </li>
                                <li ng-class="{disabled:currentPage==pages.length-1}">
                                    <a href="#"
                                       ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" role="dialog" id="withdraw-cancel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Cancel Withdraw</h4>
                    </div>
                    <div class="modal-body">
                        <form ng-submit="cancelWithdraw(withdrawId)">
                            <div class="row">
                                <div class="col-md-6 col-sm-10 col-xs-12">
                                    <div class="form-group" ng-class="{'has-error':error!=undefined}">
                                        <label for="">Memorandum No.</label>
                                        <input type="text" ng-model="formData.mem_id" class="form-control" placeholder="Memorandum No.">
                                        <p ng-if="error!=undefined" class="text text-danger">[[error.mem_id]]</p>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-info" type="submit">
                                            <i class="fa fa-spinner fa-pulse" ng-if="canceling"></i>&nbsp;Cancel Withdraw
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" role="dialog" id="withdraw-date-update">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Withdraw Date Update</h4>
                    </div>
                    <div class="modal-body">
                        <form ng-submit="withdrawDateUpdate(withdrawId)">
                            <div class="row">
                                <input type="hidden" ng-model="formData.kpi_id" ng-value="withdrawId">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group" ng-class="{'has-error':error!=undefined&&error.date!=undefined}">
                                        <label for="">Withdraw Date</label>
                                        <input type="text" id="date-picker" ng-model="formData.date" ng-value="kpis[kpiIndex].date|dateformat:'DD-MMM-YYYY'" class="form-control" placeholder="Withdraw Date">
                                        <p ng-if="error!=undefined&&error.date!=undefined" class="text text-danger">[[error.date]]</p>
                                    </div>
                                    <div class="form-group" ng-class="{'has-error':error!=undefined&&error.date!=undefined}">
                                        <label for="">Memorandum No.</label>
                                        <input type="text" ng-model="formData.mem_id"  class="form-control" placeholder="Memorandum No.">
                                        <p ng-if="error!=undefined&&error.mem_id!=undefined" class="text text-danger">[[error.mem_id[0] ]]</p>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-info" type="submit">
                                            <i class="fa fa-spinner fa-pulse" ng-if="updating"></i>&nbsp;Update Withdraw Date
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#cancel-withdraw-kpi").confirmDialog({
            message: 'Are you sure to Cancel the Withdrawal of this KPI',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#kpi-withdraw-cancel-entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
        $("#date-picker").datePicker({
            defaultValue:false
        })
    </script>
@stop