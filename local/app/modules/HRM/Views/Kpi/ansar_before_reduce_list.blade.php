{{--User: Shreya--}}
{{--Date: 11/16/2015--}}
{{--Time: 2:42 PM--}}

@extends('template.master')
@section('title','List Of Ansar Before Guard Reduce')
@section('breadcrumb')
    {!! Breadcrumbs::render('ansar_before_reduce_list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('GuardBeforeWithdrawController', function ($scope, $http, $sce) {
            $scope.params = ''
            $scope.allLoading = false;
            $scope.ansars="";
            $scope.errorFound = 0;
            $scope.errorMessage='';
            $scope.loadAnsar = function () {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('load_ansar_before_reduce')}}',
                    params: {
                        kpi_id: $scope.params.kpi,
                        division_id:$scope.params.range,
                        unit_id:$scope.params.unit,
                        thana_id:$scope.params.thana
                    }
                }).then(function (response) {
                    $scope.errorFound = 0;
                    $scope.ansars = response.data;
                    $scope.allLoading = false;
                },function(response){
                    $scope.errorFound = 1;
                    $scope.ansars = [];
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    $scope.allLoading = false;
                })
            }
        })

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
                            show-item="['range','unit','thana','kpi']"
                            type="all"
                            range-change="loadAnsar()"
                            unit-change="loadAnsar()"
                            thana-change="loadAnsar()"
                            kpi-change="loadAnsar()"
                            start-load="range"
                            on-load="loadAnsar()"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                            data = "params"
                    >

                    </filter-template>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Sl No.</th>
                                <th>Ansar ID</th>
                                <th>Ansar Name</th>
                                <th>Rank</th>
                                <th>Own District</th>
                                <th>Own Thana</th>
                                <th>Ansar Reporting Date</th>
                                <th>Ansar Joining Date</th>
                                <th>Reduce Reason</th>
                                <th>Reduce Date</th>
                            </tr>
                            <tbody ng-if="errorFound==1" ng-bind-html="errorMessage"></tbody>
                            <tr ng-show="ansars.length==0&&errorFound==0">
                                <td colspan="10" class="warning no-ansar">
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