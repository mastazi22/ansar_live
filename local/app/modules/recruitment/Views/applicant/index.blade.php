@extends('template.master')
@section('title','Circular Summery')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('circularSummery', function ($scope, $http, $q, httpService) {
            $scope.categories = [];
            $scope.circulars = [];
            $scope.circularSummery = [];
            $scope.allStatus = {'all': 'All', 'running': 'Running', 'shutdown': 'close'}
            $scope.circular = 'all';
            $scope.category = 'all';
            $scope.status = 'running';
            var ss = {'running': 'active', 'shutdown': 'inactive'};
            var loadAll = function () {
                $scope.circular = 'all';
                @if(Request::get('category'))
                $scope.category = '{{Request::get('category')}}';
                @else
                    $scope.category = 'all';
                @endif
                $scope.allLoading = true;
                $q.all([
                    httpService.category({status: ss[$scope.status]}),
                    httpService.circular({status: $scope.status}),
                    httpService.circularSummery({
                        status: $scope.status,
                        category: $scope.category,
                        circular: $scope.circular
                    })
                ])
                    .then(function (response) {
                        $scope.circular = 'all';
                        @if(Request::get('category'))
                            $scope.category = '{{Request::get('category')}}';
                        @else
                            $scope.category = 'all';
                        @endif
                        $scope.categories = response[0].data;
                        $scope.circulars = response[1].data;
                        $scope.circularSummery = response[2].data;
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.circular = 'all';
                        $scope.category = 'all';
                        $scope.categories = [];
                        $scope.circulars = [];
                        $scope.circularSummery = [];
                        console.log(response);
                        $scope.allLoading = false;
                    })
            }
            $scope.loadCircular = function (id) {
                $scope.allLoading = true;
                $q.all([
                    httpService.circular({status: $scope.status, category_id: id}),
                    httpService.circularSummery({status: $scope.status, category: id})
                ]).then(function (response) {
                    $scope.circular = 'all';
                    $scope.circulars = response[0].data;
                    $scope.circularSummery = response[1].data;
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.circular = 'all';
                    $scope.circulars = $scope.circularSummery = [];
                    $scope.allLoading = false;
                    console.log(response);
                })

            }
            $scope.loadApplicant = function (category, circular) {
                $scope.allLoading = true;
                httpService.circularSummery({status: $scope.status, category: category, circular: circular})
                    .then(function (response) {
                        $scope.circularSummery = response.data;
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.circularSummery = [];
                        console.log(response);
                        $scope.allLoading = false;
                    })
            }
            $scope.statusChange = function () {
                loadAll();
            }
            loadAll();

        })
    </script>
    <section class="content" ng-controller="circularSummery">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Category</label>
                            <select name="" ng-model="category" id="" class="form-control"
                                    ng-change="loadCircular(category)">
                                <option value="all">All</option>
                                <option ng-repeat="c in categories" value="[[c.id]]">[[c.category_name_eng]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="circular" id="" ng-change="loadApplicant(category,circular)"
                                    class="form-control">
                                <option value="all">All</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Status</label>
                            <select ng-model="status" name="" id="" class="form-control" ng-change="statusChange()">
                                <option ng-repeat="(key,value) in allStatus" value="[[key]]">[[value]]</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl. No</th>
                            <th>Circular Name</th>
                            <th>Category Name</th>
                            <th>Total Applicant</th>
                            <th>Total Male Applicant</th>
                            <th>Total Female Applicant</th>
                            <th>Total Not Paid Applicant</th>
                            <th>Total Paid Applicant (Not Applied)</th>
                            <th>Total Applied Applicant</th>
                        </tr>
                        <tr ng-repeat="a in circularSummery">
                            <td>[[$index+1]]</td>
                            <td>[[a.circular_name]]</td>
                            <td>[[a.category.category_name_eng]]</td>
                            <td><a href="{{URL::to('recruitment/applicants/list')}}/[[a.id]]" class="btn btn-link">[[a.appliciant_count]]</a>
                            </td>
                            <td><a href="{{URL::to('recruitment/applicants/list')}}/[[a.id]]/Male" class="btn btn-link">[[a.appliciant_male_count]]</a>
                            </td>
                            <td><a href="{{URL::to('recruitment/applicants/list')}}/[[a.id]]/Female"
                                   class="btn btn-link">[[a.appliciant_female_count]]</a></td>
                            <td><a href="{{URL::to('recruitment/applicants/list')}}/[[a.id]]/initial"
                                   class="btn btn-link">[[+a.appliciant_initial_count+ +a.appliciant_not_paid_count]]</a>
                            </td>
                            <td><a href="{{URL::to('recruitment/applicants/list')}}/[[a.id]]/paid" class="btn btn-link">[[a.appliciant_paid_not_apply_count]]</a>
                            </td>
                            <td><a href="{{URL::to('recruitment/applicants/list')}}/[[a.id]]/applied"
                                   class="btn btn-link">[[a.appliciant_paid_count]]</a></td>
                        </tr>
                        <tr ng-if="circularSummery.length<=0">
                            <td class="bg-warning" colspan="10">No data available</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
