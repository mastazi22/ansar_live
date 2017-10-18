@extends('template.master')
@section('title','Search Applicant')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.search') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService) {
            $scope.categories = [];
            $scope.circulars = [];
            $scope.applicants = [];
            $scope.allStatus = {'all': 'All', 'inactive': 'Inactive', 'active': 'Active'}
            $scope.circular = 'all';
            $scope.category = 'all';
            $scope.status = 'active';
            $scope.ansarSelection = 'overall';
            $scope.filter = {
                height:false,
                chest:false,
                weight:false,
                age:false,
                training:false,
                reference:false,
                gender:false,
            }
            $scope.comparisonOperator = {'Greater then':'>','Less then':'<','Equal':'=','Greater then equal':'>=','Less then equal':'<='}
            var loadAll = function () {
                $scope.circular = 'all';
                $scope.category = 'all';
                $scope.allLoading = true;
                $q.all([
                    httpService.category({status: $scope.status}),
                    httpService.circular({status: $scope.status}),
                    httpService.searchApplicant({category:$scope.category,circular:$scope.circular})
                ])
                    .then(function (response) {
                        $scope.circular = 'all';
                        $scope.category = 'all';
                        $scope.categories = response[0].data;
                        $scope.circulars = response[1].data;
                        $scope.applicants = response[2].data;
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.circular = 'all';
                        $scope.category = 'all';
                        $scope.categories = [];
                        $scope.circulars = [];
                        $scope.applicants = [];
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
                httpService.searchApplicant({category:category,circular:circular}).then(function (response) {
                    console.log(response.data)
                },function (response) {

                })
            }
            $scope.statusChange = function () {
                loadAll();
            }
            loadAll();

        })
    </script>
    <section class="content" ng-controller="applicantSearch">
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
                <div class="form-group">
                    <input type="checkbox" ng-model="filter.height" id="height" class="fancy-checkbox">
                    <label for="height" class="control-label">Height</label>
                    <div class="row" ng-if="filter.height">
                        <div class="col-sm-4">
                            <select name="" id="" class="form-control">
                                <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">[[key]]</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <div class="col-sm-6" style="padding: 0">
                                <input class="form-control" type="text" placeholder="Feet">
                            </div>
                            <div class="col-sm-6" style="padding-right: 0">
                                <input class="form-control" type="text" placeholder="Inch">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="checkbox"  ng-model="filter.weight" id="weight" class="fancy-checkbox">
                    <label for="weight" class="control-label">Weight</label>
                    <div class="row" ng-if="filter.weight">
                        <div class="col-sm-4">
                            <select name="" id="" class="form-control">
                                <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">[[key]]</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" placeholder="Weight in kg">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="chest"  ng-model="filter.chest" class="fancy-checkbox">
                    <label for="chest" class="control-label">Chest</label>
                    <div class="row" ng-if="filter.chest">
                        <div class="col-sm-4">
                            <select name="" id="" class="form-control">
                                <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">[[key]]</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" placeholder="Chest in inch">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="Age"  ng-model="filter.age" class="fancy-checkbox">
                    <label for="Age" class="control-label">Age</label>
                    <div class="row" ng-if="filter.age">
                        <div class="col-sm-4">
                            <select name="" id="" class="form-control">
                                <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">[[key]]</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" placeholder="Age in years">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="reference"  ng-model="filter.reference" class="fancy-checkbox">
                    <label for="reference" class="control-label">With Reference</label>

                </div>
                <div class="form-group">
                    <input type="checkbox" id="training"  ng-model="filter.training" class="fancy-checkbox">
                    <label for="training" class="control-label">With Training</label>

                </div>
                <div class="form-group">
                    <input type="checkbox" id="Gender"  ng-model="filter.gender" class="fancy-checkbox">
                    <label for="Gender" class="control-label">Gender</label>
                    <div class="row" ng-if="filter.gender">
                        <div class="col-sm-4">
                            <select name="" id="" class="form-control">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl. No</th>
                            <th>Applicant Name</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Division</th>
                            <th>District</th>
                            <th>Thana</th>
                            <th>Height</th>
                            <th>Chest</th>
                            <th>Weight</th>
                            <th>Action</th>
                        </tr>
                        <tr ng-repeat="a in applicants">
                            <td>[[$index+1]]</td>
                            <td>[[a.applicant_name_bng]]</td>
                            <td>[[a.gender]]</td>
                            <td>[[a.date_of_birth|dateformat:'DD-MMM-YYYY']]</td>
                            <td>[[a.division.division_name_bng]]</td>
                            <td>[[a.district.unit_name_bng]]</td>
                            <td>[[a.thana.thana_name_bng]]</td>
                            <td>[[a.height_feet]] feet [[a.height_inch]] inch</td>
                            <td>[[a.chest]] inch</td>
                            <td>[[a.weight]] kg</td>
                            <td>
                                action
                            </td>
                        </tr>
                        <tr ng-if="applicants.length<=0">
                            <td class="bg-warning" colspan="11">No data available</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
