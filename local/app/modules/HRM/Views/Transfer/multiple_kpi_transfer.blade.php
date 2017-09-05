{{--Ansar Transfer Complete--}}

@extends('template.master')
@section('title','Transfer Ansars')
@section('breadcrumb')
    {!! Breadcrumbs::render('multiple_transfer') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TransferController', function ($scope, $http,notificationService) {
            $scope.ansar_id = '';
            $scope.transfering = false;
            $scope.printLetter = false;
            $scope.memId = '';
            $scope.submitData = [];
            $scope.search = false;
            $scope.units = [];
            $scope.thanas = [];
            $scope.kpis = [];
            $scope.tAnsars = [];
            $scope.formData = {
                unit: '',
                thana: '',
                kpi: '',
                joining_date: ''
            }
            $scope.searchAnsar = function (event) {
                if (event.type == 'keypress' && event.which != 13) return;
                $scope.search = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('search_kpi_by_ansar')}}',
                    data: {ansar_id: $scope.ansar_id,unit:$scope.param.unit}
                }).then(function (response) {
                    console.log(response.data)
                    $scope.search = false;
                    $scope.data = response.data;
                }, function (response) {

                })
            }
            $scope.addToCart = function () {
                console.log($scope.formData);
                var d = angular.copy({
                    id: $scope.data.data.ansar_id,
                    name: $scope.data.data.ansar_name_eng,
                    tkn: $scope.kpiName,
                    tktn: $scope.thanaName,
                    ckn: $scope.data.data.kpi_name,
                    tkjd: $scope.formData.joining_date
                })
                var s = $scope.tAnsars.find(function (v) {
                    return v.id == d.id;
                })
                if (s) {
                    alert("This ansar already in transfer list");
                    return;
                }
                var b = angular.copy({
                    ansarId: $scope.data.data.ansar_id,
                    currentKpi: $scope.data.data.kpi_id,
                    transferKpi: $scope.formData.kpi,
                    tKpiJoinDate: $scope.formData.joining_date
                })
                $scope.submitData.push(b);
                $scope.tAnsars.push(d)
                $scope.data.data = {}
                $scope.data.status = false;
                $scope.ansar_id = '';
                $scope.formData.joining_date = ''
            }
            $scope.transferAnsar = function () {
                $scope.error = undefined;
                $scope.transfering = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('confirm_transfer')}}',
                    data: angular.toJson({ansars: $scope.submitData, memId: $scope.memId,mem_date:$scope.memDate})
                }).then(function (response) {
                    console.log(response.data)
                    var newValue = response.data;
                    if (Object.keys(newValue).length > 0) {
                        if (!newValue.status) {
                            notificationService.notify('error', newValue.message)
                        }
                        if (newValue.data.success.count > 0) {

                            for (i=0;i<newValue.data.success.count;i++){
                                notificationService.notify(
                                    'success', "Ansar("+newValue.data.success.data[i]+") successfully transfered"
                                )
                            }

                        }
                        if(newValue.data.error.count>0) {
                            for (i=0;i<newValue.data.error.count;i++){
                                notificationService.notify(
                                    'error',newValue.data.error.data[i]
                                )
                            }
                        }
                    }
                    $scope.tm = newValue.memId
                    $scope.uid = angular.copy($scope.param.unit);
//                    alert($scope.uid)
                    $scope.transfering = false;
                    $scope.printLetter = true;
                    reset();
                }, function (response) {
                    $scope.error = response.data;
                    if ($scope.error.message) {
                        notificationService.notify('error',response.data.message)
                        reset1();
                    }
                    $scope.transfering = false;
                })
            }
            $scope.remove = function (i) {
                $scope.submitData.splice(i, 1);
                $scope.tAnsars.splice(i, 1);
            }
            function reset() {
                $scope.ansar_id = '';
                $scope.data = '';
                $scope.memId = ''
//                $scope.reset = {range:true,unit:true}
                $scope.reset1 = {thana:true,kpi:true}

            }
            function reset1() {
                $scope.reset = ''
                $scope.reset1 = ''
                $scope.submitData = [];
                $scope.tAnsars = [];
                $scope.ansar_id = '';
                $scope.data = '';
                $scope.memId = ''
                $scope.reset = {range:true,unit:true}
                $scope.reset1 = {thana:true,kpi:true}
            }


        })
    </script>
    <div ng-controller="TransferController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('transfer') !!}--}}

        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="transfering">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit']"
                            type="single"
                            start-load="range"
                            reset="reset"
                            field-width="{range:'col-sm-4',unit:'col-sm-4'}"
                            data = "param"
                    ></filter-template>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <h4> Enter Ansar ID to transfer</h4>
                                <div class="input-group">
                                    <input type="text" name="ansar_id" ng-keypress="searchAnsar($event)"
                                           ng-disabled="!param.unit"
                                           ng-model="ansar_id" placeholder="Ansar ID" class="form-control">
                                    <span class="input-group-btn">
                                        <button class="btn btn-secondary" ng-click="searchAnsar($event)">
                                            <i class="fa fa-search" ng-if="!search"></i>
                                            <i class="fa fa-spinner fa-pulse" ng-if="search"></i>
                                        </button>
                                    </span>
                                </div>
                                <p class="text text-danger" ng-if="data.status==0">
                                    [[data.messages[0] ]]
                                </p>
                                <ul ng-if="data.status" style="list-style: none;margin-top: 10px">
                                    <li>
                                        <h4 style="text-decoration: underline">Ansar Name</h4>
                                        [[data.data.ansar_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline">Kpi Name</h4>
                                        [[data.data.kpi_name]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline">Kpi Unit</h4>
                                        [[data.data.unit_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline">Kpi Thana</h4>
                                        [[data.data.thana_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline">Joining Date</h4>
                                        [[data.data.joining_date|dateformat:"DD-MMM-YYYY"]]
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h4>Transfer Option</h4>
                            <filter-template
                                    show-item="['thana','kpi']"
                                    type="single"
                                    start-load="range"
                                    layout-vertical="1"
                                    load-watch="param.unit"
                                    reset="reset1"
                                    watch-change="thana"
                                    get-kpi-name="kpiName"
                                    get-thana-name="thanaName"
                                    thana-field-disabled="data==undefined||!data.status||!param.unit"
                                    kpi-field-disabled="data==undefined||!data.status||!param.unit"
                                    data = "formData"
                            ></filter-template>
                            {{--<div class="form-group">--}}
                                {{--<lable class="control-label"--}}
                                       {{--style="font-weight: bold;margin-bottom: 5px;display: block">Select Thana--}}
                                {{--</lable>--}}
                                {{--<select name="thana" ng-disabled="data==undefined||!data.status||!param.unit"--}}
                                        {{--ng-model="formData.thana" ng-change="loadGuard(formData.thana)"--}}
                                        {{--class="form-control">--}}
                                    {{--<option value="">--@lang('title.thana')--</option>--}}
                                    {{--<option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_eng]]</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<lable class="control-label" style="font-weight: bold;margin-bottom: 5px;display: block">@lang('title.kpi')</lable>--}}
                                {{--<select name="kpi" ng-disabled="data==undefined||!data.status||!param.unit"--}}
                                        {{--ng-model="formData.kpi"--}}
                                        {{--class="form-control">--}}
                                    {{--<option value="">--@lang('title.kpi')--</option>--}}
                                    {{--<option ng-repeat="k in kpis" ng-value="k.id" ng-disabled="data.data.kpi_id==k.id">--}}
                                        {{--[[k.kpi_name]]--}}
                                    {{--</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                            <div class="form-group">
                                <lable class="control-label"
                                       style="font-weight: bold;margin-bottom: 5px;display: block">Joining Date
                                </lable>
                                <input type="text" date-picker ng-disabled="data==undefined||!data.status"
                                       ng-model="formData.joining_date" placeholder="Joining Date"
                                       class="form-control" name="joining_date">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" ng-click="addToCart()"
                                        ng-disabled="data==undefined||!data.status||!formData.kpi||!formData.thana||!param.unit||!formData.joining_date">
                                    <i class="fa fa-plus"></i>&nbsp;Add to transfer list
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>#</th>
                                        <th>Ansar Id</th>
                                        <th>Name</th>
                                        <th>Current Kpi Name</th>
                                        <th>Transfer Kpi Name</th>
                                        <th>Transfer Kpi Thana</th>
                                        <th>Transfer Kpi Joining Date</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr ng-if="tAnsars.length>0" ng-repeat="t in tAnsars">
                                        <td>[[$index+1]]</td>
                                        <td>[[t.id]]</td>
                                        <td>[[t.name]]</td>
                                        <td>[[t.ckn]]</td>
                                        <td>[[t.tkn]]</td>
                                        <td>[[t.tktn]]</td>
                                        <td>[[t.tkjd]]</td>
                                        <td>
                                            <button class="btn btn-danger btn-xs" ng-click="remove($index)">
                                                <i class="fa fa-remove"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr ng-if="tAnsars.length<=0">
                                        <td colspan="5">No Ansar Available</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" style="margin-bottom: 10px">
                            <div class="form-group">
                                <label for="">Memorandum No. & Date</label>
                                <div class="row">
                                    <div class="col-md-7" style="padding-right: 0"><input type="text" name="mem_id" ng-model="memId" placeholder="Enter Memorandum no."
                                                                 class="form-control">

                                        <p ng-if="error!=undefined&&error.memId!=undefined" class="text text-danger">
                                            [[error.memId[0] ]]
                                        </p></div>
                                    <div class="col-md-5">
                                        <input date-picker ng-model="memDate"
                                               type="text" class="form-control" name="mem_date"
                                               placeholder="Memorandum Date" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label for="" style="display: block;">&nbsp;</label>
                            <button ng-disabled="!memId||submitData.length<=0" class="btn btn-primary btn-md"
                                    ng-click="transferAnsar()">Transfer
                            </button>
                            {!! Form::open(['route'=>'print_letter','target'=>'_blank','ng-if'=>'printLetter','style'=>'display:inline-block']) !!}
                            {!! Form::hidden('option','memorandumNo') !!}
                            {!! Form::hidden('id','[[tm]]') !!}
                            {!! Form::hidden('type','TRANSFER') !!}
                            @if(auth()->user()->type!=22)
                                {!! Form::hidden('unit','[[uid]]') !!}
                            @else
                                {!! Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'') !!}
                            @endif
                            <button class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;Print Transfer Letter</button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
    <script>
        $("#datepicker").datePicker();
    </script>
@stop