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
                        <form action="{{URL::route('report.applicants.applicat_accepted_list')}}" method="post" target="_blank">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label for="" class="control-label">Job Circular</label>
                                <select name="circular" ng-model="param.circular"
                                        class="form-control">
                                    <option value="">--Select a circular--</option>
                                    <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                                </select>
                            </div>
                            <filter-template
                                    show-item="['range','unit']"
                                    type="single"
                                    data="param"
                                    start-load="range"
                                    field-name="{unit:'unit'}"
                                    unit-field-disabled="!param.circular"
                                    range-field-disabled="!param.circular"
                                    field-width="{unit:'col-sm-12',range:'col-sm-12'}"
                            >
                            </filter-template>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Download accepted applicant
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
