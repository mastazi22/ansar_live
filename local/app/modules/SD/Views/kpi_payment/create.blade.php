@extends('template.master')
@section('title','Generate Salary Sheet')
@section('breadcrumb')
    {!! Breadcrumbs::render('attendance.create') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("AttendanceController", function ($scope, $http, $sce) {
            $scope.param = {};
            $scope.loadData = function(){
                if($scope.param.payment_against=="demand_sheet"){
                    alert(1)
                    $http({
                        url:"{{URL::route("SD.demandList")}}",
                        method:'post',
                        data:$scope.param.query
                    }).then(function (response) {
                        $scope.demandList = response.data;
                    },function (response) {

                    })
                }else if($scope.param.payment_against=="salary_sheet"){
                    $http({
                        url:"{{URL::route("SD.salary_management.salarySheetList")}}",
                        method:'post',
                        data:$scope.param.query
                    }).then(function (response) {
                        $scope.salaryList = response.data;
                    },function (response) {

                    })
                }

            }
            $scope.beforeSubmit = function () {
                $scope.allLoading = true;
            }
            $scope.afterSubmit = function () {
                $scope.allLoading = false;
                window,location.href = "{{URL::route('SD.kpi_payment.index')}}";
            }
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
            <div class="overlay"  ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-header">
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        {!! Form::open(['route'=>"SD.kpi_payment.store","files"=>true,'form-submit'=>'','before-submit'=>'beforeSubmit()','after-submit'=>'afterSubmit()']) !!}
                        <div class="form-group">
                            {!! Form::label('payment_against',"Payment against",['class'=>'control-label']) !!}
                            {!! Form::select('payment_against',[""=>"--Select a item--","demand_sheet"=>"Demand Sheet","salary_sheet"=>"Salary Sheet"],null,['class'=>"form-control",'ng-model'=>'param.payment_against']) !!}
                        </div>
                            <div ng-if="param.payment_against">
                                <filter-template
                                        show-item="['range','unit','thana','kpi']"
                                        type="single"
                                        kpi-change="loadData()"
                                        data="param.query"
                                        field-name="{range:'division_id',unit:'unit_id',thana:'thana_id',kpi:'kpi_id'}"
                                        start-load="range"
                                        on-load="loadPage()"
                                        layout-vertical="1"

                                ></filter-template>
                                    <div class="form-group" ng-if="param.payment_against=='demand_sheet'">
                                        <label class="control-label">Select a demand sheet</label>
                                        <select name="demand_or_salary_sheet_id" class="form-control" >
                                            <option value="">--Select a item--</option>
                                            <option ng-repeat="d in demandList" value="[[d.id]]">
                                                [[d.memorandum_no]]
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group" ng-if="param.payment_against=='salary_sheet'">
                                        <label class="control-label">Select salary sheet generated month</label>
                                        <select name="demand_or_salary_sheet_id" class="form-control" >
                                            <option value="">--Select a item--</option>
                                            <option ng-repeat="d in salaryList" value="[[d.id]]">
                                                [[d.generated_for_month+" - "+d.generated_type]]
                                            </option>
                                        </select>
                                    </div>
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