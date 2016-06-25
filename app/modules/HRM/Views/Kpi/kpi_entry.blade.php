@extends('template.master')
@section('content')
    <script>
        $(document).ready(function () {
            $('#activation_date').datePicker(false);
            $("#withdraw_date").datePicker(false);

        })
        GlobalApp.controller('DivisionController', function ($scope, getNameService) {
            $scope.division = [];
            $scope.district = [];
            $scope.districtLoad = false;
            $scope.thanaLoad = false;
            getNameService.getDivision().then(function (response) {
                $scope.division = response.data;
            });
            $scope.SelectedItemChanged = function () {
                $scope.districtLoad = true;
                getNameService.getDistric($scope.SelectedDivision).then(function (response) {
                    $scope.district = response.data;
                    $scope.districtLoad = false;
                })
            }
            $scope.SelectedDistrictChanged = function () {
                $scope.thanaLoad = true;
                getNameService.getThana($scope.SelectedDistrict).then(function (response) {
                    $scope.thana = response.data;
                    $scope.thanaLoad = false;
                })
            }
            @if(!is_null(Input::old('division_name_eng')))
            $scope.SelectedDivision = parseInt('{{Input::old('division_name_eng')}}');
            $scope.SelectedItemChanged();
            @endif

                @if(!is_null(Input::old('unit_name_eng')))
                $scope.SelectedDistrict = parseInt('{{Input::old('unit_name_eng')}}');
            $scope.SelectedDistrictChanged();
            @endif
            $scope.submit = function ($event) {
                // our function body

            }
        });
        GlobalApp.factory('getNameService', function ($http) {
            return {
                getDivision: function () {
                    return $http.get("{{action('FormSubmitHandler@DivisionName')}}");
                },
                getDistric: function (data) {

                    return $http.get("{{action('FormSubmitHandler@DistrictName')}}", {params: {id: data}});
                },
                getThana: function (data) {
                    return $http.get("{{action('FormSubmitHandler@ThanaName')}}", {params: {id: data}});
                }
            }

        });

    </script>
    <style>
        /*.loadinggif {*/
        /*background:url('http://www.hsi.com.hk/HSI-Net/pages/images/en/share/ajax-loader.gif') no-repeat;*/
        /*}*/
    </style>
    <div class="content-wrapper" style="position: relative; padding-bottom: 30px">
        {!! Form::open(array('url' => 'save-kpi', 'class' => 'form-horizontal', 'name' => 'kpiForm', 'id'=> 'kpi-form', 'ng-controller' => 'DivisionController', 'ng-app' => 'myValidateApp', 'novalidate')) !!}

        <div class="breadcrumbplace">
            {!! Breadcrumbs::render('kpi') !!}
        </div>
        <section class="content">

            <div class="col-lg-8 col-centered">
                <div class="box box-solid">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" id="tab_bar">
                            <li class="active"><a data-toggle="tab" href="#kpi_general" style="display: none;">General Kpi Form</a></li>
                            <li><a data-toggle="tab" href="#kpi_details" type="button" style="display: none;">Details Kpi Form</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="kpi_general" class="tab-pane fade in active">
                                <h3 style="text-align: center">General Kpi Form</h3>
                                <div class="box-body">
                                    <div class="form-group required">
                                        {!! Form::label('kpi_name', 'KPI Name:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                        <div class="col-sm-8"
                                             ng-class="{ 'has-error': kpiForm.kpi_name.$touched && kpiForm.kpi_name.$invalid }">
                                            {!! Form::text('kpi_name', $value = null, $attributes = array('class' => 'form-control', 'id' => 'kpi_name', 'placeholder' => 'Enter KPI Name', 'required', 'ng-model' => 'kpi_name')) !!}
                                            <span ng-if="kpiForm.kpi_name.$touched && kpiForm.kpi_name.$error.required"><p
                                                        class="text-danger">KPI name is required.</p></span>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        {!! Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                        <div class="col-sm-8"
                                             ng-class="{ 'has-error': kpiForm.division_name_eng.$touched && kpiForm.division_name_eng.$invalid }">
                                            {{--{!! Form::text('division_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'division_id', 'required')) !!}--}}
                                            <select name="division_name_eng" class="form-control" id="division_id"
                                                    ng-model="SelectedDivision" ng-change="SelectedItemChanged()"
                                                    ng-model="division_name_eng" required>
                                                <option value="">--Select a division--</option>
                                                <option ng-repeat="x in division" value="[[x.id]]">
                                                    [[x.division_name_eng]]
                                                </option>

                                            </select>
                                            <i class="fa fa-spinner fa-pulse" ng-show="districtLoad"></i>
                                            <span ng-if="kpiForm.division_name_eng.$touched && kpiForm.division_name_eng.$error.required"><p
                                                        class="text-danger">
                                                    KPI division is required.</p></span>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        {!! Form::label('unit_id', 'Unit:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                        <div class="col-sm-8"
                                             ng-class="{ 'has-error': kpiForm.unit_name_eng.$touched && kpiForm.unit_name_eng.$invalid }">
                                            {{--{!! Form::text('unit_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'unit_id', 'required')) !!}--}}
                                            <select name="unit_name_eng" class="form-control" id="unit_id"
                                                    ng-model="SelectedDistrict" ng-change="SelectedDistrictChanged()"
                                                    ng-model="unit_name_eng" required>
                                                <option value="">--Select a district--</option>
                                                <option ng-repeat="x in district" value="[[x.id]]">[[ x.unit_name_eng ]]
                                                </option>
                                            </select>
                                            <i class="fa fa-spinner fa-pulse" ng-show="thanaLoad"></i>
                                            <span ng-if="kpiForm.unit_name_eng.$touched && kpiForm.unit_name_eng.$error.required"><p
                                                        class="text-danger">KPI
                                                    division is required.</p></span>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        {!! Form::label('thana_id', 'Thana:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                        <div class="col-sm-8"
                                             ng-class="{ 'has-error': kpiForm.thana_name_eng.$touched && kpiForm.thana_name_eng.$invalid }">
                                            {{--{!! Form::text('thana_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'thana_id', 'required')) !!}--}}
                                            <select name="thana_name_eng" class="form-control" id="thana_id"
                                                    ng-model="ThanaModel" ng-change="SelectedThanaChanged()"
                                                    ng-model="thana_name_eng" required>
                                                <option value="">--Select a thana--</option>
                                                <option ng-repeat="x in thana" value="[[x.id]]">[[ x.thana_name_eng ]]
                                                </option>
                                            </select>
                                            <span ng-if="kpiForm.thana_name_eng.$touched && kpiForm.thana_name_eng.$error.required"><p
                                                        class="text-danger">KPI
                                                    division is required.</p></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('kpi_address', 'Address:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                        <div class="col-sm-8">
                                            {!! Form::textarea('kpi_address', $value = null, $attributes = array('class' => 'form-control', 'id' => 'kpi_address', 'size' => '30x4', 'placeholder' => "Write the address")) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('kpi_contact_no', 'Contact No and Person:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                        <div class="col-sm-8">
                                            {!! Form::textarea('kpi_contact_no', $value = null, $attributes = array('class' => 'form-control', 'id' => 'kpi_contact_no', 'size' => '30x4', 'placeholder' => "Write Contact No and Person Info")) !!}
                                        </div>
                                    </div>
                                    <button style="background: #5bc0de; border-color: #46b8da; color: #FFFFFF" class="btn btn-info pull-right" id="nexttab" type="button">Next Page</button>
                                </div>
                                {{--{!! Form::close() !!}--}}
                            </div>
                            <div id="kpi_details" class="tab-pane fade">
                                <div class="box-body">
                                    <h3 style="text-align: center">Details Kpi Form</h3>
                                    <div class="box-body">
                                        <div class="form-group required">
                                            {!! Form::label('total_ansar_request', 'Total Ansar Request:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8"
                                                 ng-class="{ 'has-error': kpiForm.total_ansar_request.$touched && kpiForm.total_ansar_request.$invalid }">
                                                {!! Form::text('total_ansar_request', $value = null, $attributes = array('class' => 'form-control', 'id' => 'total_ansar_request', 'placeholder' => 'Enter Total Ansar Request Number', 'required', 'ng-model' => 'total_ansar_request')) !!}
                                                <span ng-if="kpiForm.total_ansar_request.$touched && kpiForm.total_ansar_request.$error.required"><p
                                                            class="text-danger">Total Ansar Request field is
                                                        required.</p></span>
                                            </div>
                                        </div>
                                        <div class="form-group required">
                                            {!! Form::label('total_ansar_given', 'Total Ansar Given:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8"
                                                 ng-class="{ 'has-error': kpiForm.total_ansar_given.$touched && kpiForm.total_ansar_given.$invalid }">
                                                {!! Form::text('total_ansar_given', $value = null, $attributes = array('class' => 'form-control', 'id' => 'total_ansar_given', 'placeholder' => 'Enter Total Ansar given Number', 'required', 'ng-model' => 'total_ansar_given')) !!}
                                                <span ng-if="kpiForm.total_ansar_given.$touched && kpiForm.total_ansar_given.$error.required"><p
                                                            class="text-danger">Total Ansar Given field is required.</p></span>
                                            </div>
                                        </div>
                                        <div class="form-group required">
                                            {!! Form::label('with_weapon', 'Ansar With Weapon:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8"
                                                 ng-class="{ 'has-error': kpiForm.with_weapon.$touched && kpiForm.with_weapon.$invalid }">
                                                {{--{!! Form::text('thana_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'thana_id', 'required')) !!}--}}
                                                <select class="form-control" id="with_weapon" name="with_weapon"
                                                        ng-model="with_weapon" required>
                                                    <option value="">--Select Yes or No--</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                                <span ng-if="kpiForm.with_weapon.$touched && kpiForm.with_weapon.$error.required"><p
                                                            class="text-danger">Ansar With Weapon field is required.</p></span>
                                            </div>
                                        </div>
                                        <div class="form-group required">
                                            {!! Form::label('weapon_count', 'Weapon Number:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8"
                                                 ng-class="{ 'has-error': kpiForm.weapon_count.$touched && kpiForm.weapon_count.$invalid }">
                                                {!! Form::text('weapon_count', $value = null, $attributes = array('class' => 'form-control', 'id' => 'weapon_count', 'placeholder' => 'Enter Weapon Number.e.g., For no weapon enter 0', 'required', 'ng-model' => 'weapon_count')) !!}
                                                <span ng-if="kpiForm.weapon_count.$touched && kpiForm.weapon_count.$error.required"><p
                                                            class="text-danger">Weapon Number field is
                                                        required.</p></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('bullet_no', 'Number of Bullets:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('bullet_no', $value = null, $attributes = array('class' => 'form-control', 'id' => 'bullet_no', 'placeholder' => 'Enter Number of Bullets')) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('weapon_description', 'Weapon Description:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('weapon_description', $value = null, $attributes = array('class' => 'form-control', 'id' => 'weapon_description', 'size' => '30x4', 'placeholder' => "Write Description", 'ng-model' => 'weapon_description')) !!}
                                            </div>
                                        </div>
                                        <div class="form-group required">
                                            {!! Form::label('activation_date', 'Activation Date:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8"
                                                 ng-class="{ 'has-error': kpiForm.activation_date.$touched && kpiForm.activation_date.$invalid }">
                                                {!! Form::text('activation_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'activation_date', 'required', 'ng-model' => 'activation_date')) !!}
                                                <span ng-if="kpiForm.activation_date.$touched && kpiForm.activation_date.$error.required"><p
                                                            class="text-danger">Activation Date field is
                                                        required.</p></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('withdraw_date', 'Withdraw Date:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('withdraw_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'withdraw_date')) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('no_of_pc', 'No of PC:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('no_of_pc', $value = null, $attributes = array('class' => 'form-control', 'id' => 'no_of_pc', 'placeholder' => 'Enter Number of PC')) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('no_of_apc', 'No of APC:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('no_of_apc', $value = null, $attributes = array('class' => 'form-control', 'id' => 'no_of_apc', 'placeholder' => 'Enter Number of APC')) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('no_of_ansar', 'No of Ansar:', $attributes = array('class' => 'col-sm-4 control-label')) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('no_of_ansar', $value = null, $attributes = array('class' => 'form-control', 'id' => 'no_of_ansar', 'placeholder' => 'Enter Number of Ansar')) !!}
                                            </div>
                                        </div>
                                        <button style="background: #5bc0de; border-color: #46b8da; color: #FFFFFF" class="btn btn-info" id="prevtab" type="button">Previous Page</button>
                                        <button type="submit" id="next-button" class="btn btn-info pull-right"
                                                ng-disabled="kpiForm.kpi_name.$error.required||kpiForm.division_name_eng.$error.required||kpiForm.unit_name_eng.$error.required||kpiForm.thana_name_eng.$error.required||kpiForm.total_ansar_request.$error.required||kpiForm.total_ansar_given.$error.required||kpiForm.with_weapon.$error.required||kpiForm.activation_date.$error.required">
                                            Save KPI Information
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div class="btn-group">--}}
                    {{--<button style="background: #5bc0de; border-color: #46b8da; color: #FFFFFF" class="btn" id="prevtab" type="button">Prev</button>--}}
                    {{--<button style="background: #5bc0de; border-color: #46b8da; color: #FFFFFF" class="btn" id="nexttab" type="button">Next</button>--}}
                {{--</div>--}}
                {{--<button type="submit" id="next-button" class="btn btn-info pull-right"--}}
                        {{--ng-disabled="kpiForm.kpi_name.$error.required||kpiForm.division_name_eng.$error.required||kpiForm.unit_name_eng.$error.required||kpiForm.thana_name_eng.$error.required||kpiForm.total_ansar_request.$error.required||kpiForm.total_ansar_given.$error.required||kpiForm.with_weapon.$error.required||kpiForm.activation_date.$error.required">--}}
                    {{--Save KPI Information--}}
                {{--</button>--}}
                {!! Form::close() !!}
            </div>
        </section>
    </div>
<script>
    var $tabs = $('.nav-tabs-custom li');

    $('#prevtab').on('click', function() {
        $tabs.filter('.active').prev('li').find('a[data-toggle="tab"]').tab('show');
    });

    $('#nexttab').on('click', function() {
        $tabs.filter('.active').next('li').find('a[data-toggle="tab"]').tab('show');
    });
</script>
@stop