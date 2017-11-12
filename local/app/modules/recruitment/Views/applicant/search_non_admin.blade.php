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
            $scope.applicants = $sce.trustAsHtml('<h4 class="text-center">No Applicant available</h4>');
            $scope.allStatus = {'all': 'All', 'inactive': 'Inactive', 'active': 'Active'}
            $scope.circular = 'all';
            $scope.category = 'all';
            $scope.limitList = '50';
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
                education: {value: false, data: [], comparator: '='}
            }
            $scope.comparisonOperator = {
                'Greater then': '>',
                'Less then': '<',
                'Equal': '=',
                'Greater then equal': '>=',
                'Less then equal': '<='
            }
            var loadAll = function () {
                $scope.circular = 'all';
                $scope.category = 'all';
                $scope.allLoading = true;
                $q.all([
                    httpService.category({status: 'active'}),
                    httpService.circular({status: 'running'}),
                    $http.get("{{URL::to('HRM/getalleducation')}}")
                ])
                    .then(function (response) {
                        $scope.circular = 'all';
                        $scope.category = 'all';
                        $scope.categories = response[0].data;
                        $scope.circulars = response[1].data;
                        $scope.educations = response[2].data;
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
                ]).then(function (response) {
                    $scope.circular = 'all';
                    $scope.circulars = response[0].data;
                    $scope.applicants = $sce.trustAsHtml('');
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
            $scope.removeFilter = function (key) {
                $scope.filter[key].value = false;
                $scope.loadApplicant();
                $scope.selectedList = [];
            }
            $scope.applicantsDetail = [];

            $scope.addToSelection = function (id) {
                if($scope.selectedList.indexOf(id)>=0){
                    notificationService.notify('error','Applicant already added to selection')
                    return ;
                }
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('recruitment.applicant.selected_applicant')}}',
                    data:{applicant_id:id},
                    method:'post'
                }).then(function (response) {
                    $scope.allLoading = false;
                    if(response.data) {
                        $scope.applicantsDetail.push(response.data);
                        $scope.selectedList.push(id);
                        $scope.applicants = $sce.trustAsHtml('<h4 class="text-center">No Applicant available</h4>');
                        $scope.q = '';
                    }else{
                        notificationService.notify('error','Invalid applicant')
                    }
                },function (response) {
                    notificationService.notify('error','An error occur while adding. please try again later')
                })
            }
            $scope.removeToSelection = function (id) {
                var i = $scope.selectedList.indexOf(id)
                if (i >= 0) {
                    $scope.selectedList.splice(i, 1);
                    $scope.applicantsDetail.splice(i,1)
                }
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
                    $scope.applicantsDetail = [];
                },function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.rejectApplicants = function (id) {
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('recruitment.applicant.confirm_selection_or_rejection')}}',
                    method:'post',
                    data:{
                        applicants:[id],
                        type:'rejection',
                        sub_type:1,
                        message:$scope.selectMessage
                    }
                }).then(function (response) {
                    $scope.applicants = $sce.trustAsHtml('<h4 class="text-center">No Applicant available</h4>');
                    $scope.allLoading = false;
                    notificationService.notify(response.data.status,response.data.message)
                    $scope.selectedList = [];
                    $scope.applicantsDetail = [];
                },function (response) {
                    $scope.allLoading = false;
                })
            }
            $scope.acceptedApplicants = function (id) {
                $('#accept-applicant').confirmDialog({
                    message: "Are u sure to accept this ansar for Battalion Ansar?",
                    ok_button_text: 'Confirm',
                    cancel_button_text: 'Cancel',
                    event: 'click',
                    ok_callback: function (element) {
                        $scope.allLoading = true;
                        $http({
                            url:'{{URL::route('recruitment.applicant.confirm_accepted')}}',
                            method:'post',
                            data:{
                                applicant_id:id
                            }
                        }).then(function (response) {
                            $scope.applicants = $sce.trustAsHtml('<h4 class="text-center">No Applicant available</h4>');
                            $scope.allLoading = false;
                            notificationService.notify(response.data.status,response.data.message)
                            $scope.selectedList = [];
                            $scope.applicantsDetail = [];
                        },function (response) {
                            $scope.allLoading = false;
                        })
                    },
                    cancel_callback: function (element) {
                    }
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
                    scope.$watch('applicants', function (n) {

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
                <div class="filters">
                    <span class="label label-success" ng-repeat="(k,v) in filter" ng-if="v.value&&v.data">
                        [[k+" "+v.comparator+" "+v.data]]<a href="#" ng-click="removeFilter(k)">&times</a>
                    </span>
                    <span class="label label-success" ng-repeat="(k,v) in filter" ng-if="v.value&&!v.data&&k=='height'">
                        [[k+" "+v.comparator+" feet: "+v.feet+", inch: "+v.inch]]<a href="#" ng-click="removeFilter(k)">&times</a>
                    </span>
                    <span class="label label-success" ng-repeat="(k,v) in filter" ng-if="v.value&&!v.data&&k!='height'">
                        [[k]]<a href="#" ng-click="removeFilter(k)">&times</a>
                    </span>
                </div>
                <h3 class="text-center">Search applicant by National ID</h3>
                <div class="input-group" style="margin-top: 10px">
                    <input ng-keyup="$event.keyCode==13?loadApplicant():''" class="form-control" ng-model="q" type="text" placeholder="Search by national id">
                    <span class="input-group-btn">
                    <button class="btn btn-primary" ng-click="loadApplicant()">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
                </div>
                <h3 class="text-center">Applicant detail</h3>
                <div ng-bind-html="applicants" compile-html>

                </div>
                <div style="margin-top: 20px;text-align: center" ng-if="selectedList.length>0">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#chooser">
                        Confirm Selection
                    </button>
                </div>
            </div>
        </div>
        <div class="modal fade" id="chooser">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Confirm selection</h4>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
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
                                <tr ng-repeat="a in applicantsDetail">
                                    <td>[[$index+1]]</td>
                                    <td>[[a.applicant_name_bng]]</td>
                                    <td>[[a.gender]]</td>
                                    <td>[[a.date_of_birth]]</td>
                                    <td>[[a.division_name_bng]]</td>
                                    <td>[[a.unit_name_bng]]</td>
                                    <td>[[a.thana_name_bng]]</td>
                                    <td>[[a.height_feet]] feet [[a.height_inch]] inch</td>
                                    <td>[[a.chest_normal+'-'+a.chest_extended]] inch</td>
                                    <td>[[a.weight]] kg</td>
                                    <td>
                                        <button class="btn btn-danger btn-xs" ng-click="removeToSelection(a.applicant_id)">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                                <tr ng-if="selectedList.length<=0">
                                    <td colspan="11" class="bg-warning">
                                        No applicant available
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 col-centered" style="text-align: center">
                                <button class="btn btn-primary" ng-disabled="selectedList.length<=0" data-dismiss="modal" style="margin-bottom: 10px" ng-click="selectApplicants('selection',0)">Confirm selection & cancel previous selection</button>
                                <button  class="btn btn-primary"  ng-disabled="selectedList.length<=0" data-dismiss="modal" ng-click="selectApplicants('selection',1)">Confirm selection & add to previous selection</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
