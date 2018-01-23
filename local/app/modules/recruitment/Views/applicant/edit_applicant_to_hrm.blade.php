@extends('template.master')
@section('title','Edit Applicants for HRM')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.search') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce,$rootScope) {
            var p = '50'
            $scope.categories = [];
            $scope.q = '';
            $scope.selectMessage = '';
            $scope.educations = [];
            $scope.circulars = [];
            $scope.applicants = $sce.trustAsHtml('loading data....');
            $scope.allStatus = {'': '--Select a status', 'applied': 'Applied', 'selected': 'Selected','accepted':'Accepted'}
            $scope.param = {};
            $scope.limitList = '50';

            httpService.circular({status: 'running'}).then(function (response) {
                $scope.circular = 'all';
                $scope.circulars = response.data;
                $scope.allLoading = false;
            }, function (response) {
                $scope.circular = 'all';
                $scope.circulars = [];
                $scope.allLoading = false;
            })
            $scope.$watch('limitList', function (n, o) {
                if (n == null) {
                    $scope.limitList = o;
                }
                else if (p != n && p != null) {
                    p = n;
                    $scope.loadApplicant();
                }
            })
            $scope.loadApplicant = function (url) {
                //alert($scope.limitList)
                $scope.allLoading = true;
                $scope.param['limit'] = $scope.limitList;
                $http({
                    url:url||'{{URL::route('recruitment.edit_for_hrm')}}',
                    method:'post',
                    data:$scope.param
                }).then(function (response) {
                    $scope.applicants = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.applicants = $sce.trustAsHtml('loading error.....');
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
            $scope.submitComplete = function () {
                $("#edit-form").modal('hide');
            }


        })
        GlobalApp.controller('fullEntryFormController', function ($scope, $q, $http, httpService, notificationService,$rootScope) {
             function renameProperty(data,oldName, newName) {
                // Do nothing if the names are the same
                if (oldName == newName) {
                    return data;
                }
                // Check for the old property name to avoid a ReferenceError in strict mode.
                if (data.hasOwnProperty(oldName)) {
                    data[newName] = data[oldName];
                    delete data[oldName];
                }
                return data;
            };
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
                    $http({method: 'get', url: '{{URL::route('recruitment.applicant.getfieldstore')}}'}),
                    httpService.rank(),
                    httpService.disease(),
                    httpService.skill(),
                    httpService.bloodGroup()
                ]).then(function (response) {
//                    console.log(response)
                    $scope.allLoading = false;
                    $scope.formData = response[0].data.data;
                    $scope.formData = renameProperty($scope.formData,'height_feet','hight_feet');
                    $scope.formData = renameProperty($scope.formData,'height_inch','hight_inch');
                    $scope.formData = renameProperty($scope.formData,'applicant_name_bng','ansar_name_bng');
                    $scope.formData = renameProperty($scope.formData,'applicant_name_eng','ansar_name_eng');
                    $scope.formData = renameProperty($scope.formData,'gender','sex');
                    $scope.formData = renameProperty($scope.formData,'date_of_birth','data_of_birth');
//                    console.log($scope.formData)
                    $scope.district = response[0].data.units;
                    $scope.thana = response[0].data.thanas;
                    $scope.division = response[1];
                    $scope.ppp = response[2];
                    $scope.fields = response[3].data['field_value'].split(',');
                    $scope.ranks = response[4];
                    $scope.diseases = response[5];
                    $scope.skills = response[6].data;
                    $scope.blood = response[7];
                    $scope.disableDDT = false;
                    $scope.formData.division_id += '';
                    $scope.formData.unit_id += '';
                    $scope.formData.thana_id += '';
                    $scope.formData.appliciant_education_info.forEach(function (d, i) {

                        $scope.formData.appliciant_education_info[i].job_education_id += '';
                    })
                    $scope.addTrainingInfo();
                    $scope.addNomineeInfo();
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
            $scope.addTrainingInfo = function () {
                if($scope.formData.applicant_training_info===undefined){
                    $scope.formData['applicant_training_info'] = []
                }
                $scope.formData.applicant_training_info.push({
                    training_designation: '',
                    training_designation_eng: '',
                    training_institute_name: '',
                    training_institute_name_eng: '',
                    training_start_date: '',
                    training_start_date_eng: '',
                    training_end_date: '',
                    training_end_date_eng: '',
                    trining_certificate_no: '',
                    trining_certificate_no_eng: '',
                })
            }
            $scope.deleteTrainingInfo = function (index) {
                if($scope.formData.applicant_training_info.length>1)$scope.formData.applicant_training_info.splice(index, 1);
            }
            $scope.addNomineeInfo = function () {
                if($scope.formData.applicant_nominee_info===undefined){
                    $scope.formData['applicant_nominee_info'] = []
                }
                $scope.formData.applicant_nominee_info.push({
                    name_of_nominee: '',
                    name_of_nominee_eng: '',
                    relation_with_nominee: '',
                    relation_with_nominee_eng: '',
                    nominee_parcentage: '',
                    nominee_parcentage_eng: '',
                    nominee_contact_no: '',
                    nominee_contact_no_eng: ''
                })
            }
            $scope.deleteNomineeInfo = function (index) {
                if($scope.formData.applicant_nominee_info.length>1)$scope.formData.applicant_nominee_info.splice(index, 1);
            }
            $scope.updateData = function () {
                console.log($scope.formData)
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    data: $scope.formData,
                    url: '{{URL::route('recruitment.store_hrm_detail')}}'
                }).then(function (response) {
                    $scope.allLoading = false;
                    console.log(response.data);
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

                if($scope.isAdmin!=11&&($scope.fields==undefined||$scope.fields.indexOf(s)<0)) return false;
                return true;
            }
        });
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
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular"
                                    class="form-control">
                                <option value="">--Select a circular</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label " style="display: block;">&nbsp;</label>
                            <button ng-disabled="!(param.circular)" class="btn btn-primary" ng-click="loadApplicant()">Load Applicant</button>
                        </div>
                    </div>
                </div>
                <filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        data="param"
                        start-load="range"
                        field-name="{unit:'unit'}"
                        range-change="loadApplicant()"
                        unit-change="loadApplicant()"
                        thana-change="loadApplicant()"
                        unit-field-disabled="!(param.circular)"
                        range-field-disabled="!(param.circular)"
                        thana-field-disabled="!(param.circular)"
                        field-width="{unit:'col-sm-4',range:'col-sm-4',thana:'col-sm-4'}"
                >
                </filter-template>
                <div ng-bind-html="applicants" compile-html>

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
