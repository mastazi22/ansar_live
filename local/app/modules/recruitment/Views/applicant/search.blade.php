@extends('template.master')
@section('title','Search Applicant')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.search') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('circularSummery', function ($scope, $http, $q, httpService) {


        })
    </script>
    <section class="content" ng-controller="circularSummery">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                {{--<div class="row">
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
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Status</label>
                            <select ng-model="status" name="" id="" class="form-control" ng-change="statusChange()">
                                <option ng-repeat="(key,value) in allStatus" value="[[key]]">[[value]]</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl. No</th>
                            <th>Circular Name</th>
                            <th>Category Name</th>
                            <th>Total Applicant</th>
                            <th>Total Male Applicant</th>
                            <th>Total Female Applicant</th>
                            <th>Total Paid Applicant</th>
                        </tr>
                        <tr ng-repeat="a in circularSummery">
                            <td>[[$index+1]]</td>
                            <td>[[a.circular_name]]</td>
                            <td>[[a.category.category_name_eng]]</td>
                            <td>[[a.appliciant_count]]</td>
                            <td>[[a.appliciant_male_count]]</td>
                            <td>[[a.appliciant_female_count]]</td>
                            <td>[[a.appliciant_paid_count]]</td>
                        </tr>
                        <tr ng-if="circularSummery.length<=0">
                            <td class="bg-warning" colspan="7">No data available</td>
                        </tr>
                    </table>
                </div>--}}
            </div>
        </div>
    </section>
@endsection
