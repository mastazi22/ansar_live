@extends('template.master')
@section('title','Applicants')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.index') !!}
@endsection
@section('content')
    <section class="content">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="fa fa-remove"></span> {{Session::get('error_message')}}
                </div>
            </div>
        @endif
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
                <form action="{{URL::route('recruitment.applicant.list',['type'=>$type])}}"
                      method="get">
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-sm-4">

                            <div class="form-group">
                                {!! Form::select('circular_id',$circulars,(isset($circular_id)?$circular_id:null),['class'=>'form-control','id'=>'ccc']) !!}
                            </div>

                        </div>
                        <div class="col-sm-6 col-sm-offset-2">

                            <div class="input-group">
                                <input type="text" name="q" class="form-control"
                                       placeholder="Search here by txID or mobile no">
                                <span class="input-group-btn">
                                            <button class="btn btn-primary">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                            </div>

                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="">Applicant Name</th>
                            <th>Applicant ID</th>

                            <th>Applicant Password</th>
                            <th>txID</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Division</th>
                            <th>District</th>
                            <th>Thana</th>
                            <th>Height</th>
                            <th>Chest</th>
                            <th>Weight</th>
                            <th>Mobile no</th>
                            @if($type=='pending'||$type=='initial')
                                <th>Action</th>
                            @elseif(!$type)
                                <th>Status</th>
                            @endif
                        </tr>
                        @foreach($applicants as $a)
                            <tr>
                                <td>{{$a->applicant_name_bng}}</td>
                                <td>{{$a->applicant_id}}</td>
                                <td>{{$a->applicant_password}}</td>
                                <td>
                                    @if($a->payment)
                                        @if($a->payment->paymentHistory)
                                            @foreach($a->payment->paymentHistory as $p)
                                                {{$p->txID}}<br>
                                            @endforeach
                                        @else
                                            {{$a->payment?$a->payment->txID:'n\a'}}
                                        @endif
                                    @else
                                        n/a
                                    @endif
                                </td>
                                <td>{{$a->gender}}</td>
                                <td>{{$a->date_of_birth}}</td>
                                <td>{{$a->division?$a->division->division_name_bng:'n\a'}}</td>
                                <td>{{$a->district?$a->district->unit_name_bng:'n\a'}}</td>
                                <td>{{$a->thana?$a->thana->thana_name_bng:'n\a'}}</td>
                                <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                                <td>{{$a->chest_normal.'-'.$a->chest_extended}} inch</td>
                                <td>{{$a->weight}} kg</td>
                                <td>{{$a->mobile_no_self}}</td>
                                @if($type=='pending'||$type=='initial')
                                    <td>

                                        <a class="btn btn-sm btn-primary"
                                           href="{{URL::route('recruitment.applicant.mark_as_paid',['id'=>$a->applicant_id,'type'=>$type,'circular_id'=>$a->job_circular_id])}}">Mark
                                            as paid</a>
                                    </td>
                                @elseif(!$type)
                                    <td>{{$a->status}}</td>
                                @endif
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
    <script>
        $(document).ready(function (e) {
            $("#ccc").on('change',function (ee) {
                $("form")[0].submit();
            })
        })
    </script>
@endsection
