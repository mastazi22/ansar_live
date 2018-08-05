@extends('template.master')
@section('title','Generate Salary Sheet')
@section('breadcrumb')
    {!! Breadcrumbs::render('attendance.create') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("AttendanceController", function ($scope, $http, $sce) {
            $http({
                url:"{{URL::route("SD.demandList")}}",
                method:'post'
            }).then(function (response) {
                $scope.demandList = response.data;
            },function (response) {

            })
        })


    </script>
    <section class="content" ng-controller="AttendanceController">
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
                    {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <div class="box box-solid">
            <div class="overlay hidden" >
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-header">
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        {!! Form::open(['route'=>"SD.kpi_payment.store","files"=>true]) !!}
                        {!! Form::hidden('payment_against','demand_sheet') !!}
                            <div class="form-group">
                                <label class="control-label">Select a demand sheet</label>
                                <select name="demand_or_salary_sheet_id" class="form-control" >
                                    <option value="">--Select a item--</option>
                                    <option ng-selected="d.id=='{{Request::old("demand_or_salary_sheet_id")}}'" ng-repeat="d in demandList" value="[[d.id]]">
                                        [[d.memorandum_no+" - "+d.kpi.kpi_name]]
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Paid amount</label>
                                <input type="text" class="form-control" name="paid_amount" placeholder="Paid Amount">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Bank receipt</label>
                                <input type="file" name="document" class="file" data-show-preview="false">
                            </div>
                            <div id="show-preview" class="form-group">

                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary pull-right">Submit</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .fileinput-upload-button{
            display: none;
        }
    </style>
    <script>
        $(document).ready(function () {
            $("input[name='document']").on('change',function () {
                var file =  this.files[0];
                if(!file)  $("#show-preview").html("");
                var f = new FileReader;

                f.onload = function () {
                    $("#show-preview").html("");
                    $("<img>").addClass("img-thumbnail img-responsive").attr('src',this.result).appendTo("#show-preview")
                }
                f.readAsDataURL(file);
            })
            $("body").on('click','.fileinput-remove-button',function () {
                $("#show-preview").html("");
            })
        })
    </script>
@endsection