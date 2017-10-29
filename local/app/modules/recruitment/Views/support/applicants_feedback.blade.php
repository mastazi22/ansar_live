@extends('template.master')
@section('title','Applicants Feedback')
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
                {{--<div class="row" style="margin-bottom: 20px">
                    <div class="col-sm-6 col-sm-offset-6">
                        <form action="{{URL::route('recruitment.applicant.list',['type'=>$type])}}" method="get">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Search here by txID or mobile no">
                                <span class="input-group-btn">
                                            <button class="btn btn-primary">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                            </div>
                        </form>
                    </div>
                </div>--}}
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Applicant Mobile no</th>
                            <th>Problem type</th>
                            <th>Payment option</th>
                            <th>Sender no</th>
                            <th>bankTxId</th>
                            <th>Action</th>
                        </tr>
                        @foreach($applicants as $a)
                        <tr>
                            <td>{{$a->mobile_no_self}}</td>
                            <td>{{$a->problem_type}}</td>
                            <td>{{$a->payment_option}}</td>
                            <td>{{$a->sender_no}}</td>
                            <td>{{$a->txid}}</td>
                            <td>
                                <a href="#" data-action="{{URL::route('supports.feedback.submit',['id'=>$a->id])}}" data-type="verify" class="btn btn-primary btn-xs ddd">Verify</a>
                                <a href="#" data-action="{{URL::route('supports.feedback.submit',['id'=>$a->id])}}" data-type="reject" class="btn btn-danger btn-xs ddd">Reject</a>
                            </td>
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
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form action="" method="post">
                {!! csrf_field() !!}
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Verification form</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type">
                    <div class="form-group">
                        <label for="" class="control-label">Message</label>
                        <textarea name="message" id="" cols="30" rows="10" class="form-control" placeholder="Enter message here">

                        </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">submit</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $(".ddd").on('click',function (e) {
                e.preventDefault();
                var type = $(this).attr('data-type')
                var action = $(this).attr('data-action')
                $("#myModal").modal('show');
                $("#myModal").find('form').attr('action',action);
                $("#myModal").find('form').find('*[name="type"]').val(type);
            })
        })
    </script>
@endsection
