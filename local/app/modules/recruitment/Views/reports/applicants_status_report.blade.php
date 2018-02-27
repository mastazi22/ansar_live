@extends('template.master')
@section('title','Applicants List')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ApplicantsListController',function ($scope, $http, $sce,httpService) {
            $scope.applicants = $sce.trustAsHtml(`<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption style="font-size: 20px;color:#111111">All applicants</caption>
                            <tr>
                                <th>#</th>
                                <th>Applicant Name</th>
                                <th>Father Name</th>
                                <th>Birth Date</th>
                                <th>National ID No.</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Height</th>
                                <th>Weight</th>
                                <th>Mobile no</th>
                                <th>Status</th>
                            </tr>
                            <tr>
                                <td colspan="12" class="bg-warning">No applicant available
                                .Select <strong>Job Circular</strong> and <strong>Applicant Status</strong> to load applicant
                                </td>
                            </tr>
                        </table>
                    </div>`)
            $scope.customData = {
                'applied':'Applied',
                'selected':'Selected',
                'pending':'Pending',
                'accepted':'Accepted',
            }
            $scope.param = {};

            $scope.allLoading = false;
            httpService.circular({status: 'running'}).then(function (res) {
                $scope.circulars = res.data;
            })
            $scope.loadPage = function (url) {
                var link = url || window.location.href
                var p = link.split(/\\?page=/);
                $scope.param['page'] = p.length>1?parseInt(p[1]):1;
                $scope.allLoading = true;
                $http({
                    url:link,
                    data:$scope.param,
                    method:'post'
                }).then(function (response) {
                    $scope.applicants = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                },function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml("<h3 class='text text-center'>Error ocur while loading. try again later</h3>")
                })
            }
        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {

                        if (attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
    </script>
    <section class="content" ng-controller="ApplicantsListController">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular"
                                    class="form-control">
                                <option value="">--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="" class="control-label">Applicant Status</label>
                        <select name="" ng-model="param.status"
                                class="form-control">
                            <option value="">--Select a status--</option>
                            <option ng-repeat="(k,v) in customData" value="[[k]]">[[v]]</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="" class="control-label" style="display:block">&nbsp;</label>
                        <button class="btn btn-primary" ng-click="loadPage()" ng-disabled="!(param.circular&&param.status)">Load Data</button>
                    </div>
                    {{--<div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Ansar Selection</label>
                            <select name="" ng-model="ansarSelection" id="" ng-change="loadApplicant(category,circular)"
                                    class="form-control">
                                <option value="overall">Overall</option>
                                <option value="division">Division Wise</option>
                                <option value="unit">District Wise</option>
                            </select>
                        </div>
                    </div>--}}
                </div>
                <filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        range-change="loadPage()"
                        unit-change="loadPage()"
                        thana-change="loadPage()"
                        data="param"
                        start-load="range"
                        range-field-disabled="!(param.circular&&param.status)"
                        unit-field-disabled="!(param.circular&&param.status)"
                        thana-field-disabled="!(param.circular&&param.status)"
                        field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4',custom:'col-sm-4'}"
                >
                </filter-template>
                <div ng-bind-html="applicants" compile-html>

                </div>

            </div>
        </div>
    </section>
@endsection
