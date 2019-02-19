@extends('template.master')
@section('title','Applicant Mark Rules')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.point.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('circularPoint',function ($scope, httpService,$http,$sce) {
            $scope.jobCategories = [];
            $scope.jobCirculars = [];
            $scope.loadType = 'web';
            $scope.category_id = 'all'
            $scope.circular_id = 'all'
            $scope.pointsView = $sce.trustAsHtml('')
            $scope.loadJobCategories = function () {
                httpService.category({}).then(function (res) {
                    console.log(res)
                    $scope.jobCategories = res.data;
                })
            }
            $scope.loadJobCirculars = function () {
                httpService.circular({category_id:$scope.category_id}).then(function (res) {
                    $scope.circular_id = 'all'
                    $scope.jobCirculars = res.data;
                })
            }
            $scope.loadSearchData = function () {
                $http({
                    method:'get',
                    url:'{{URL::route('recruitment.marks_rules.index')}}',
                    params:{circular_id:$scope.circular_id}
                }).then(function (res) {
                    $scope.loadType = 'ajax';
                    $scope.pointsView = $sce.trustAsHtml(res.data);
                },function (res) {

                })
            }
            $scope.loadJobCategories();
        })
    </script>
    <section class="content" ng-controller="circularPoint">
        <div class="box box-solid">
            @if(Session::has('session_error'))
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>&nbsp;{{Session::get('session_error')}}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @elseif(Session::has('session_success'))
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>&nbsp;{{Session::get('session_success')}}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @endif

            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="control-label">Select Category</label>
                        <select class="form-control" ng-model="category_id" ng-change="loadJobCirculars()">
                            <option value="all">All</option>
                            <option ng-repeat="category in jobCategories" value="[[category.id]]">[[category.category_name_bng]]</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="control-label">Select Circular</label>
                        <select class="form-control" ng-model="circular_id" ng-change="loadSearchData()">
                            <option value="all">All</option>
                            <option ng-repeat="circular in jobCirculars" value="[[circular.id]]">[[circular.circular_name]]</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive" ng-if="loadType=='web'">
                    <table class="table table-bordered table-condensed">
                        <caption style="font-size: 20px">Mark Rules<a
                                    href="{{URL::route('recruitment.marks_rules.create')}}"
                                    class="btn btn-primary btn-xs pull-right">Add new field</a></caption>
                        <tr>
                            <th>SL. No</th>
                            <th>Circular name</th>
                            <th>Rule name</th>
                            <th>Rule for</th>
                            <th>Rules</th>
                            <th>Action</th>
                        </tr>
                        <?php $i = 1;?>
                        @forelse($points as $point)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$point->circular->circular_name}}</td>
                                <td>{{$point->rule_name}}</td>
                                <td>{{$point->point_for}}</td>
                                @if($point->rule_name==='education')
                                    <td>{!!  $point->getEducationRules()!!}</td>
                                @elseif($point->rule_name==='height')
                                    <td>{!! $point->getHeightRules() !!}</td>
                                @elseif($point->rule_name==='training')
                                    <td>{!! $point->getTrainingRules() !!}</td>
                                @elseif($point->rule_name==='experience')
                                    <td>{!! $point->getExperienceRules() !!}</td>
                                @elseif($point->rule_name==='age')
                                    <td>{!! $point->getAgeRules() !!}</td>
                                @endif
                                <td>
                                    <a class="btn btn-primary btn-xs" href="{{URL::route('recruitment.marks_rules.edit',['id'=>$point->id])}}">
                                        <i class="fa fa-edit"></i>&nbsp;Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="bg-warning">
                                    No Point Rule available.
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div class="table-responsive" ng-if="loadType=='ajax'" ng-bind-html="pointsView">

                </div>
            </div>
        </div>
    </section>
@endsection