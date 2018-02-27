@extends('template.master')
@section('title','Search Applicant')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.search') !!}
@endsection
@section('content')
    <style>
        .filters {
            padding-bottom: 20px;
        }

        .filters > span {
            font-size: 1em;
            vertical-align: middle;
        }

        .filters > span > a {
            color: #ffffff;
            margin-left: 5px;
        }

        .filters > span:not(:first-child) {
            margin-left: 10px;
        }
    </style>
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce,notificationService) {
            var p = '50'
            $scope.relations = {
                '': '--সম্পর্ক নির্বাচন করুন--',
                'father': 'Father',
                'mother': 'Mother',
                'brother': 'Brother',
                'sister': 'Sister',
                'cousin': 'Cousin',
                'uncle': 'Uncle',
                'aunt': 'Aunt',
                'neighbour': 'Neighbour'
            };
            $scope.categories = [];
            $scope.q = '';
            $scope.selectMessage = '';
            $scope.educations = [];
            $scope.circulars = [];
            $scope.applicants = $sce.trustAsHtml('loading data....');
            $scope.allStatus = {'all': 'All', 'inactive': 'Inactive', 'active': 'Active'}
            $scope.circular = 'all';
            $scope.category = 'all';
            $scope.param={};
            $scope.ansarSelection = 'overall';
            $scope.selectedList = [];
            $scope.filter = {
                height: {value: false, feet: '', inch: '', comparator: '='},
                chest_normal: {value: false, data: '', comparator: '='},
                chest_extended: {value: false, data: '', comparator: '='},
                weight: {value: false, data: '', comparator: '='},
                age: {value: false, data: '', comparator: '='},
                training: {value: false},
                reference: {value: false,data:'', comparator: '='},
                gender: {value: false, data: 'Male', comparator: '='},
                education: {value: false, data: [], comparator: '='},
                applicant_quota: {value: false}
            }
            $scope.comparisonOperator = {
                'Greater then': '>',
                'Less then': '<',
                'Equal': '=',
                'Greater then equal': '>=',
                'Less then equal': '<='
            }
            var loadAll = function () {
                if($scope.param['limit']===undefined){
                    $scope.param['limit'] = '50'
                }
                $scope.circular = 'all';
                $scope.category = 'all';
                $scope.allLoading = true;
                $q.all([
                    httpService.category({status: 'active'}),
                    httpService.circular({status: 'running'}),
                    httpService.searchApplicant(undefined, {
                        category: $scope.category,
                        circular: $scope.circular,
                        limit: $scope.param['limit'],
                        filter: $scope.filter,
                        q:$scope.param.q
                    }),
                    $http.get("{{URL::to('HRM/getalleducation')}}")
                ])
                    .then(function (response) {
                        $scope.circular = 'all';
                        $scope.category = 'all';
                        $scope.categories = response[0].data;
                        $scope.circulars = response[1].data;
                        $scope.educations = response[3].data;
                        $scope.applicants = $sce.trustAsHtml(response[2].data);
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
                    httpService.circular({status: 'running', category_id: id}),
                    httpService.searchApplicant(undefined, {
                        category: $scope.category,
                        circular: $scope.circular,
                        limit: $scope.limitList,
                        filter: $scope.filter,
                        q:$scope.q
                    })
                ]).then(function (response) {
                    $scope.circular = 'all';
                    $scope.circulars = response[0].data;
                    $scope.applicants = $sce.trustAsHtml(response[1].data);
                    $scope.allLoading = false;
                    $scope.selectedList = [];
                }, function (response) {
                    $scope.circular = 'all';
                    $scope.circulars = $sce.trustAsHtml('loading error.....');
                    $scope.allLoading = false;
                    $scope.selectedList = [];
                })

            }
            $scope.loadApplicant = function (url) {
                //alert($scope.limitList)
                $scope.allLoading = true;
                httpService.searchApplicant(url, {
                    category: $scope.category,
                    circular: $scope.circular,
                    limit: $scope.param['limit']||'50',
                    filter: $scope.filter,
                    q:$scope.q
                }).then(function (response) {
                    $scope.applicants = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.applicants = $sce.trustAsHtml('loading error.....');
                    $scope.allLoading = false;
                })
            }
            $scope.selectAllApplicant = function (url) {
                //alert($scope.limitList)
                $scope.allLoading = true;
                httpService.searchApplicant(url, {
                    category: $scope.category,
                    circular: $scope.circular,
                    limit: $scope.limitList,
                    filter: $scope.filter,
                    select_all: true,
                    q:$scope.q
                }).then(function (response) {
                    console.log(response.data)
                    $scope.allLoading = false;
                    $scope.selectedList = response.data.map(function (n) {
                        return n + '';
                    });
                }, function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.removeFilter = function (key) {
                $scope.filter[key].value = false;
                $scope.loadApplicant();
                $scope.selectedList = [];
            }


            $scope.addToSelection = function (id) {
                $scope.selectedList.push(id);
            }
            $scope.removeToSelection = function (id) {
                var i = $scope.selectedList.indexOf(id)
                if (i >= 0) $scope.selectedList.splice(i, 1);
            }
            $scope.applyFilter = function () {
                $scope.selectedList = [];
                $scope.loadApplicant();
            }
            $scope.confirmSelectionOrRejection = function () {
                $("#chooser").modal('show')
            }
            $scope.selectApplicants = function (type,subType) {
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('recruitment.applicant.confirm_selection_or_rejection')}}',
                    method:'post',
                    data:{
                        applicants:$scope.selectedList,
                        type:type,
                        sub_type:subType,
                        message:$scope.selectMessage
                    }
                }).then(function (response) {
                    $scope.allLoading = false;
                    notificationService.notify(response.data.status,response.data.message)
                    $scope.selectedList = [];
                    $scope.loadApplicant();
                },function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.$watch('selectMessage',function (newVal) {
                $scope.selectMessage = newVal.length>160?newVal.substr(0,160):newVal;
            })
            loadAll();


        })
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {
                        if(newScope) newScope.$destroy();
                        newScope = scope.$new();
                        if (attr.ngBindHtml) {
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
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
                            <select name="" ng-model="circular" id="" ng-change="applyFilter()"
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
                <div ng-bind-html="applicants" compile-html>

                </div>
            </div>
        </div>
        {{--<div class="modal fade" id="filter-list">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Filter</h4>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <input type="checkbox" ng-model="filter.height.value" id="height" class="fancy-checkbox">
                            <label for="height" class="control-label">Height</label>
                            <div class="row" ng-if="filter.height.value">
                                <div class="col-sm-6">
                                    <select name="" id="" class="form-control" ng-model="filter.height.comparator">
                                        <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">
                                            [[key]]
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-6" style="padding: 0">
                                        <input class="form-control" ng-model="filter.height.feet" type="text"
                                               placeholder="Feet">
                                    </div>
                                    <div class="col-sm-6" style="padding-right: 0">
                                        <input class="form-control" ng-model="filter.height.inch" type="text"
                                               placeholder="Inch">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" ng-model="filter.weight.value" id="weight" class="fancy-checkbox">
                            <label for="weight" class="control-label">Weight</label>
                            <div class="row" ng-if="filter.weight.value">
                                <div class="col-sm-6">
                                    <select name="" id="" class="form-control" ng-model="filter.weight.comparator">
                                        <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">
                                            [[key]]
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input ng-model="filter.weight.data" class="form-control" type="text"
                                           placeholder="Weight in kg">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="chest_normal" ng-model="filter.chest_normal.value"
                                   class="fancy-checkbox">
                            <label for="chest_normal" class="control-label">Chest Normal</label>
                            <div class="row" ng-if="filter.chest_normal.value">
                                <div class="col-sm-6">
                                    <select name="" id="" class="form-control"
                                            ng-model="filter.chest_normal.comparator">
                                        <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">
                                            [[key]]
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input ng-model="filter.chest_normal.data" class="form-control" type="text"
                                           placeholder="Chest in inch">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="chest_extended" ng-model="filter.chest_extended.value"
                                   class="fancy-checkbox">
                            <label for="chest_extended" class="control-label">Chest Extended</label>
                            <div class="row" ng-if="filter.chest_extended.value">
                                <div class="col-sm-6">
                                    <select name="" id="" ng-model="filter.chest_extended.comparator"
                                            class="form-control">
                                        <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">
                                            [[key]]
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input ng-model="filter.chest_extended.data" class="form-control" type="text"
                                           placeholder="Chest in inch">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="Age" ng-model="filter.age.value" class="fancy-checkbox">
                            <label for="Age" class="control-label">Age</label>
                            <div class="row" ng-if="filter.age.value">
                                <div class="col-sm-6">
                                    <select ng-model="filter.age.comparator" name="" id="" class="form-control">
                                        <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">
                                            [[key]]
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input ng-model="filter.age.data" class="form-control" type="text"
                                           placeholder="Age in years">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="reference" ng-model="filter.reference.value"
                                   class="fancy-checkbox">
                            <label for="reference" class="control-label">With Reference</label>
                            <div class="row" ng-if="filter.reference.value">
                                <div class="col-sm-6">
                                    <select ng-model="filter.reference.data" name="" id="" class="form-control">
                                        <option ng-repeat="(k,v) in relations" value="[[k]]">[[v]]</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="training" ng-model="filter.training.value"
                                   class="fancy-checkbox">
                            <label for="training" class="control-label">With Training</label>

                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="Gender" ng-model="filter.gender.value" class="fancy-checkbox">
                            <label for="Gender" class="control-label">Gender</label>
                            <div class="row" ng-if="filter.gender.value">
                                <div class="col-sm-4">
                                    <select ng-model="filter.gender.data" name="" id="" class="form-control">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="applicant_quota" ng-model="filter.applicant_quota.value"
                                   class="fancy-checkbox">
                            <label for="applicant_quota" class="control-label">Apply Quota</label>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="education" ng-model="filter.education.value"
                                   class="fancy-checkbox">
                            <label for="education" class="control-label">Education</label>
                            <div class="row" ng-if="filter.education.value">
                                <div class="col-sm-6">
                                    <select name="" id="" class="form-control"
                                            ng-model="filter.education.comparator">
                                        <option ng-repeat="(key,value) in comparisonOperator" value="[[value]]">
                                            [[key]]
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <select  class="form-control" multiple ng-model="filter.education.data" name="" id="">
                                        <option value="">--Select a education</option>
                                        <option ng-repeat="e in educations" value="[[e.id]]">
                                            [[e.education_deg_bng]]
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary pull-right" ng-click="applyFilter()" data-dismiss="modal">Apply
                            filter
                        </button>
                    </div>
                </div>
            </div>
        </div>--}}
        <div class="modal fade" id="chooser">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Choose a option</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-8 col-centered" style="text-align: center">
                                <p>Character left: [[selectMessage.length]]/160</p>
                                <textarea style="margin-bottom: 10px" class="form-control" ng-model="selectMessage" name="" id="" cols="30" rows="5" placeholder="Type your message">

                                </textarea>
                                <button class="btn btn-primary" data-dismiss="modal" style="margin-bottom: 10px" ng-click="selectApplicants('selection',0)">Confirm selection & cancel previous selection</button>
                                <button  class="btn btn-primary" data-dismiss="modal" ng-click="selectApplicants('selection',1)">Confirm selection & add to previous selection</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
