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
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce,notificationService,$rootScope) {
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
            $scope.circular = '';
            $scope.category = '';
            $scope.limitList = '50';
            $scope.ansarSelection = 'overall';
            $scope.selectedList = [];
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
                    q:$scope.q,
                    already_selected:$scope.selectedList
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
                        type:'selection',
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
                        type:'rejection'
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
            var v = '<div class="text-center" style="margin-top: 20px"><i class="fa fa-spinner fa-pulse"></i></div>'
            $scope.acceptedApplicants = function (id) {
                $('#accept-applicant').confirmDialog({
                    message: "Are u sure to accept this ansar for Battalion Ansar?",
                    ok_button_text: 'Confirm',
                    cancel_button_text: 'Cancel',
                    event: 'click',
                    ok_callback: function (element) {
                        $scope.allLoading = true;
                        $http({
                            url:'{{URL::route('recruitment.applicant.confirm_accepted_if_bn_candidate')}}',
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
            $scope.editApplicant = function (url) {
                $("#edit-form").modal('show');
                $rootScope.detail = $sce.trustAsHtml(v);
                $http.get(url).then(function (response) {
                    $rootScope.detail = $sce.trustAsHtml(response.data.view);
                    $rootScope.applicant_id = response.data.id;
                })
            }
            $scope.submitComplete = function () {
                $("#edit-form").modal('hide');
            }
            $scope.$watch('selectMessage',function (newVal) {
                $scope.selectMessage = newVal.length>160?newVal.substr(0,160):newVal;
            })
            $rootScope.$on('refreshData',function (event) {
                $scope.loadApplicant()
            })
            loadAll();



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
                    console.log(response)
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
                    $scope.formData['training_info'] = $scope.formData.training_info?'No training':$scope.formData.training_info;
                    $scope.formData.appliciant_education_info.forEach(function (d, i) {

                        $scope.formData.appliciant_education_info[i].job_education_id += '';
                    })
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
                    }
                    else {
                        notificationService.notify('error', 'An unknown error occur. Please try again later')
                    }
                })
            }
            $scope.isEditable = function (s) {

                console.log(s+" : "+($scope.isAdmin!=11&&($scope.fields==undefined||$scope.fields.indexOf(s)<0)))
                console.log($scope.fields)
                if($scope.isAdmin!=11&&($scope.fields==undefined||$scope.fields.indexOf(s)<0)) return -1;
                return 1;
            }
        });
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
                            <select name="" ng-model="category" id="" class="form-control">
                                <option value="">Select a category</option>
                                <option ng-repeat="c in categories" value="[[c.id]]">[[c.category_name_eng]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="circular" id=""
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
                <h3 class="text-center">Search applicant</h3>
                <div class="input-group" style="margin-top: 10px">
                    <input ng-disabled="!category && !circular" ng-keyup="$event.keyCode==13?loadApplicant():''" class="form-control" ng-model="q" type="text" placeholder="Search by national id,applicant id or date of birth">
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
                                {{--<button class="btn btn-primary" ng-disabled="selectedList.length<=0" data-dismiss="modal" style="margin-bottom: 10px" ng-click="selectApplicants('selection',0)">Confirm selection & cancel previous selection</button>--}}
                                <button  class="btn btn-primary"  ng-disabled="selectedList.length<=0" data-dismiss="modal" ng-click="selectApplicants('selection',1)">Confirm selection</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    </section>

@endsection
