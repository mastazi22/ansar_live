@extends('template.master')
@section('title','Applicants')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.index') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            {{--<div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>--}}
            <div class="box-body">
                {{--<div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Category</label>
                            <select name="" ng-model="category" id="" class="form-control"
                                    ng-change="loadCircular(category)">
                                <option value="all">All</option>
                                <option ng-repeat="c in categories" value="{{c.id}}">{{c.category_name_eng}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="circular" id="" ng-change="loadApplicant(category,circular)"
                                    class="form-control">
                                <option value="all">All</option>
                                <option ng-repeat="c in circulars" value="{{c.id}}">{{c.circular_name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Status</label>
                            <select ng-model="status" name="" id="" class="form-control" ng-change="statusChange()">
                                <option ng-repeat="(key,value) in allStatus" value="{{key}}">{{value}}</option>
                            </select>
                        </div>
                    </div>
                </div>--}}
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Applicant Name</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Division</th>
                            <th>District</th>
                            <th>Thana</th>
                            <th>Height</th>
                            <th>Chest</th>
                            <th>Weight</th>
                            <th>Mobile no</th>
                        </tr>
                        @foreach($applicants as $a)
                        <tr>
                            <td>{{$a->applicant_name_bng}}</td>
                            <td>{{$a->gender}}</td>
                            <td>{{$a->date_of_birth}}</td>
                            <td>{{$a->division->division_name_bng}}</td>
                            <td>{{$a->district->unit_name_bng}}</td>
                            <td>{{$a->thana->thana_name_bng}}</td>
                            <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                            <td>{{$a->chest_normal.'-'.$a->chest_extended}} inch</td>
                            <td>{{$a->weight}} kg</td>
                            <td>{{$a->mobile_no_self}}</td>
                        </tr>
                        @endforeach
                        <tr ng-if="circularSummery.length<=0">
                            <td class="bg-warning" colspan="7">No data available</td>
                        </tr>
                    </table>
                </div>
                <div class="pull-right">
                    {{$applicants->render()}}
                </div>
            </div>
        </div>
    </section>
@endsection
