@extends('template.master')
@section('title','Final Applicant List')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.search') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce,notificationService) {
            $scope.circulars = [];
            $scope.param = {};
            httpService.circular({status: 'running'}).then(function (response) {
                $scope.circulars = response.data;
            })
            $scope.loadApplicant = function () {
                $http({
                    method:'post',
                    url:'{{URL::route('recruitment.applicant.final_list_load')}}',
                    data:angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.applicants = response.data;
                })
            }

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
                    <div class="col-sm-6 col-centered">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular"
                                    class="form-control">
                                <option value="">--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                        <filter-template
                                show-item="['unit']"
                                type="single"
                                data="param"
                                start-load="unit"
                                unit-field-disabled="!param.circular"
                                field-width="{unit:'col-sm-12'}"
                        >
                        </filter-template>
                        <div class="form-group">
                            <button ng-click="loadApplicant()" class="btn btn-primary btn-block">
                                Load short listed applicant
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <style>
                        table{
                            border-collapse: collapse;
                        }
                        table td,table th{
                            border:1px solid #000000;
                            text-align: center;
                        }
                        /*.inner-table tr:first-child th,.inner-table tr:first-child td{
                            border-top: none !important;
                        }
                        .inner-table tr:last-child th,.inner-table tr:last-child td{
                            border-bottom: none !important;
                        }*/
                        .inner-table tr th:first-child,.inner-table tr td:first-child{
                            border-left: none !important;
                        }
                        .inner-table tr th:last-child,.inner-table tr td:last-child{
                            border-right: none !important;
                        }
                    </style>
                    <table style="min-width: 100%">
                        <caption style="text-align: center;font-size: 20px;font-weight: bold;color:#000000;">
                            আনসার  ও গ্রাম প্রতিরক্ষা বাহিনী,[[applicants.unit.unit_name_bng]]<br>
                            মৌলিক প্রশিক্ষণ -সাধারণ আনসার (পুরুষ)<br>
                            চুড়ান্তভাবে নির্বাচিত প্রশিক্ষণার্থির তালিকা
                        </caption>
                        <tr>
                            <th>ক্রমিক নং</th>
                            <th>নাম</th>
                            <th>পিতার নাম</th>
                            <th>ঠিকানা</th>
                            <th>জাতীয় পরিচয় পত্র নং</th>
                            <th>জন্ম তারিখ</th>
                            <th>উচ্চতাযুক্ত</th>
                            <th>শিক্ষাগত যোগ্যতা</th>
                            <th>মন্তব্য</th>
                        </tr>
                        <tr ng-repeat="a in applicants.applicants">
                            <td>[[$index+1]]</td>
                            <td>[[a.applicant.applicant_name_bng]]</td>
                            <td>[[a.applicant.father_name_bng]]</td>
                            <td>
                                <table class="inner-table" width="100%">
                                    <tr>
                                        <th>গ্রাম</th>
                                        <th>ডাকঘর</th>
                                        <th>উপজেলা</th>
                                        <th>জেলা</th>
                                    </tr>
                                    <tr>
                                        <td>[[a.applicant.village_name_bng]]</td>
                                        <td>[[a.applicant.post_office_name_bng]]</td>
                                        <td>[[a.applicant.union_name_bng]]</td>
                                        <td>[[a.applicant.district.unit_name_bng]]</td>
                                    </tr>
                                </table>
                            </td>


                            <td>[[a.applicant.national_id_no]]</td>
                            <td>[[a.applicant.date_of_birth]]</td>
                            <td>[[a.applicant.height_feet+' feet '+a.applicant.height_inch+' inch' ]]</td>
                            <td>
                                <table class="inner-table" width="100%">
                                    <tr>
                                        <th>শিক্ষাগত যোগ্যতা</th>
                                        <th>শিক্ষা প্রতিষ্ঠানের নাম</th>
                                        <th>পাসের সাল</th>
                                        <th>বিভাগ / শ্রেণী</th>
                                    </tr>
                                    <tr ng-repeat="e in a.applicant.appliciant_education_info">
                                        <td>[[e.education_info.education_deg_bng]]</td>
                                        <td>[[e.institute_name]]</td>
                                        <td>[[e.passing_year]]</td>
                                        <td>[[e.gade_divission]]</td>
                                    </tr>
                                </table>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection
