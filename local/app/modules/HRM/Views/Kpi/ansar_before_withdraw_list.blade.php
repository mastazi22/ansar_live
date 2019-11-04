@extends('template.master')
@section('title','List Of Ansar Before Guard Withdraw')
@section('breadcrumb')
    {!! Breadcrumbs::render('ansar_before_withdraw_list') !!}
@endsection

@section('content')
    <script>
        GlobalApp.controller('GuardBeforeWithdrawController', function ($scope, $http, $sce) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $scope.ansars = "";
            $scope.params = '';
            $scope.rank = '';
            $scope.allLoading = false;
            $scope.errorFound = 0;
            $scope.errorMessage = '';
            $scope.gCount = {};
            $scope.loadAnsar = function () {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('load_ansar_before_withdraw')}}',
                    params: {
                        kpi_id: $scope.params.kpi,
                        division_id: $scope.params.range,
                        unit_id: $scope.params.unit,
                        thana_id: $scope.params.thana,
                        gender: $scope.params.gender == undefined ? 'all' : $scope.params.gender,
                        q: $scope.q,
                        rank: $scope.rank
                    }
                }).then(function (response) {
                    $scope.errorFound = 0;
                    $scope.gCount = response.data.tCount;
                    $scope.gCount['total'] = sum(response.data.tCount);
                    $scope.ansars = response.data.list;
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.errorFound = 1;
                    $scope.ansars = [];
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='" + $('.table').find('tr').find('th').length + "'>" + response.data + "</td></tr>");
                    $scope.allLoading = false;
                })
            };
            $scope.changeRank = function (i) {
                $scope.rank = i;
                $scope.loadAnsar()
            };

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }
        });
    </script>
    <div ng-controller="GuardBeforeWithdrawController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit','thana','kpi','gender']"
                            type="all"
                            range-change="loadAnsar()"
                            unit-change="loadAnsar()"
                            thana-change="loadAnsar()"
                            kpi-change="loadAnsar()"
                            gender-change="loadAnsar()"
                            start-load="range"
                            on-load="loadAnsar()"
                            kpi-type="all"
                            field-width="{range:'col-sm-2',unit:'col-sm-2',thana:'col-sm-2',kpi:'col-sm-3',gender:'col-sm-3'}"
                            data="params"
                    ></filter-template>
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text text-bold">
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank('all')">Total
                                    Ansars ([[gCount.total!=undefined?gCount.total.toLocaleString():0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(3)">PC
                                    ([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(2)">APC
                                    ([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(1)">Ansar
                                    ([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</a>
                            </h4>
                        </div>
                        <div class="col-md-4" style="padding-top: 1%;">
                            <database-search q="q" queue="queue" on-change="loadAnsar()"></database-search>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Sl No.</th>
                                <th>Ansar ID</th>
                                <th>Ansar Name</th>
                                <th>Rank</th>
                                <th>Own District</th>
                                <th>Own Thana</th>
                                <th>Kpi Name</th>
                                <th>Ansar Reporting Date</th>
                                <th>Ansar Embodiment Date</th>
                                <th>Withdraw Reason</th>
                                <th>Withdraw Date</th>
                            </tr>
                            <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                            <tr ng-show="ansars.length==0&&errorFound==0">
                                <td colspan="11" class="warning no-ansar">
                                    No Ansar is available to show
                                </td>
                            </tr>
                            <tr ng-show="ansars.length>0" ng-repeat="a in ansars">
                                <td>
                                    [[$index+1]]
                                </td>
                                <td>
                                    [[a.id]]
                                </td>
                                <td>
                                    [[a.name]]
                                </td>
                                <td>
                                    [[a.rank]]
                                </td>
                                <td>
                                    [[a.unit]]
                                </td>
                                <td>
                                    [[a.thana]]
                                </td>
                                <td>
                                    [[a.kpi_name]]
                                </td>
                                <td>
                                    [[a.r_date|dateformat:'DD-MMM-YYYY']]
                                </td>
                                <td>
                                    [[a.j_date|dateformat:'DD-MMM-YYYY']]
                                </td>
                                <td>
                                    [[a.reason]]
                                </td>
                                <td>
                                    [[a.date|dateformat:'DD-MMM-YYYY']]
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop