@extends('template.master')
@section('title','Circular Summery')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.circular.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('circularSummery',function ($scope, $http, $q) {
            $scope.categories = [];
            $scope.circulars = [];
            $scope.status = {'all':'All','inactive':'Inactive','active':'Active'}
        })
    </script>
    <section class="content">
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
                            <select name="" id="" class="form-control">
                                <option value="all">All</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
