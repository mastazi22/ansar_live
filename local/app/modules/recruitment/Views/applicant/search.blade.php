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
        GlobalApp.controller('applicantSearch', function ($rootScope,$scope, $http, $q, httpService, $sce,notificationService) {
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
            $scope.circular = '';
            $scope.category = '';
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
                $scope.circular = '';
                $scope.category = '';
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
                        $scope.circular = '';
                        $scope.category = '';
                        $scope.categories = response[0].data;
                        $scope.circulars = response[1].data;
                        $scope.educations = response[3].data;
                        $scope.applicants = $sce.trustAsHtml(response[2].data);
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.circular = '';
                        $scope.category = '';
                        $scope.categories = [];
                        $scope.circulars = [];
                        $scope.applicants = [];
                       // console.log(response);
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
                        q:$scope.param.q
                    })
                ]).then(function (response) {
                    $scope.circular = '';
                    $scope.circulars = response[0].data;
                    $scope.applicants = $sce.trustAsHtml(response[1].data);
                    $scope.allLoading = false;
                    $scope.selectedList = [];
                }, function (response) {
                    $scope.circular = '';
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
                    q:$scope.param.q
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
                    q:$scope.param.q
                }).then(function (response) {
                    //console.log(response.data)
                    $scope.allLoading = false;
                    $scope.selectedList = response.data.map(function (n) {
                        return n + '';
                    });
                }, function (response) {
                    $scope.allLoading = false;
                })
            }
            var v = '<div class="text-center" style="margin-top: 20px"><i class="fa fa-spinner fa-pulse"></i></div>'


            $scope.editApplicant = function (url) {
                $("#edit-form").modal('show');
                $rootScope.detail = $sce.trustAsHtml(v);
                $http.get(url).then(function (response) {
                    $rootScope.detail = $sce.trustAsHtml(response.data.view);
                    $rootScope.applicant_id = response.data.id;
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
            $scope.acceptedAsSpecial = function (id) {
                $scope.allLoading = true;
                $http({
                    method:'post',
                    data:{applicant_id:id},
                    url:'{{URL::route("recruitment.applicant.confirm_accepted_special_candidate")}}'
                }).then(function (response) {
                    var res = response.data;
                    if(res.status=="success"){
                        $scope.loadApplicant();
                    }
                    notificationService.notify(res.status,res.message)
                    $scope.allLoading = false;
                },function (response) {
                    notificationService.notify("error",`unkonown error. code ${response.status}`);
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
        GlobalApp.controller('fullEntryFormController', function ($scope, $q, $http, httpService, notificationService,$rootScope) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.formData = {};
            $scope.fields = [];
            $scope.eduRows = [];
            $scope.eduEngRows = [];
            $scope.allLoading = true;
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

            $scope.profile_pic = " ";
            $scope.formSubmitResult = {};
            $scope.ppp = [];
            $scope.disableDDT = false;
            $scope.calling = function () {
                alert($scope.profile_pic);
            }
            $scope.disableDDT = true;
            $scope.loadApplicantDetail = function () {
                $q.all([
                    $http({method: 'get', url: '{{URL::to('recruitment/applicant/detail')}}/'+$rootScope.applicant_id}),
                    httpService.range(),
                    httpService.education(),
                    $http({method: 'get', url: '{{URL::route('recruitment.applicant.getfieldstore')}}'})
                ]).then(function (response) {
                    //console.log(response)
                    $scope.allLoading = false;
                    $scope.formData = response[0].data.data;
                    $scope.district = response[0].data.units;
                    $scope.thana = response[0].data.thanas;
                    $scope.division = response[1];
                    $scope.ppp = response[2];
                    $scope.fields = response[3].data['field_value'].split(',');
                    $scope.disableDDT = false;
                    $scope.formData.division_id += '';
                    $scope.formData.unit_id += '';
                    $scope.formData.thana_id += '';
                    $scope.formData.appliciant_education_info.forEach(function (d, i) {

                        $scope.formData.appliciant_education_info[i].job_education_id += '';
                    })
                    console.log($scope.formData)
                });
            }
            $scope.SelectedItemChanged = function () {
                $scope.disableDDT = true;
                httpService.unit($scope.formData.division_id).then(function (response) {
                    $scope.district = response;
                    $scope.thana = [];
                    $scope.formData.unit_id = '';
                    $scope.formData.thana_id = '';

                    $scope.disableDDT = false;
                })
            };
            $scope.SelectedDistrictChanged = function () {
                $scope.disableDDT = true;
                httpService.thana($scope.formData.division_id, $scope.formData.unit_id).then(function (response) {
                    $scope.thana = response;
                    $scope.formData.thana_id = "";
                    $scope.disableDDT = false;
                })
            };

            $scope.eduDeleteRows = function (index) {
                $scope.formData.appliciant_education_info.splice(index, 1);
            }
            $scope.addEducation = function () {
                $scope.formData.appliciant_education_info.push({
                    job_education_id: '',
                    job_applicant_id: $scope.formData.id,
                    institute_name: '',
                    gade_divission: '',
                    passing_year: ''
                })
            }
            $scope.updateData = function () {
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    data: $scope.formData,
                    url: '{{URL::route('recruitment.applicant.update')}}'
                }).then(function (response) {
                    $scope.allLoading = false;
                    notificationService.notify(response.data.status, response.data.message)
                    $("#edit-form").modal('toggle');
                    $rootScope.$emit('refreshData',{})
                }, function (response) {
                    $scope.allLoading = false;
                    if (response.status == 422) {
                        $scope.formSubmitResult['error'] = response.data;
                        notificationService.notify('error', JSON.stringify(response.data),50000)
                    }
                    else {
                        notificationService.notify('error', 'An unknown error occur. Please try again later: error code:'+response.status,50000)
                    }
                })
            }
            $scope.isEditable = function (s) {

                //console.log(s+" : "+($scope.isAdmin!=11&&($scope.fields==undefined||$scope.fields.indexOf(s)<0)))
                //console.log($scope.fields)
                if($scope.isAdmin!=11&&($scope.fields==undefined||$scope.fields.indexOf(s)<0)) return -1;
                return 1;
            }
        });
        GlobalApp.directive('compileHtmll', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    scope.$watch('detail', function (n) {

                        if (attr.ngBindHtml) {
                            $compile(elem[0].children)(scope)
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
                                <option value="">Select a category</option>
                                <option ng-repeat="c in categories" value="[[c.id]]">[[c.category_name_eng]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="circular" id="" ng-change="applyFilter()"
                                    class="form-control">
                                <option value="">Select a circular</option>
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
        <div class="modal fade" id="edit-form">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit form</h4>
                    </div>
                    <div class="modal-body" ng-bind-html="detail" compile-htmll>

                    </div>
                </div>
            </div>
        </div>
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
