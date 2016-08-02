<!DOCTYPE html>
<html>
<head>
    @include('template.resource')
    <style>
        .table>tbody>tr>td,.table>thead>tr>td,.table>tr>td,.table>tr>th{
            background: #ffffff !important;
        }
    </style>
    <script>
        var app = angular.module('LoginApp',[],['$interpolateProvider',function($interpolateProvider){
            $interpolateProvider.startSymbol('[[');
            $interpolateProvider.endSymbol(']]');
        }])
        app.controller('loginController', ['$scope','$http',function ($scope,$http) {
            $scope.panelData = {
                pcMale:0,
                pcFemale:0,
                apcMale:0,
                apcFemale:0,
                ansarMale:0,
                ansarFemale:0,
            }
            $scope.loading = true;
            $http({
                url:'{{URL::route('central_panel_list')}}',
                method:'get'
            }).then(function (response) {
                $scope.panelData.pcMale = response.data.pm;
                $scope.panelData.pcFemale = response.data.pf;
                $scope.panelData.apcMale = response.data.apm;
                $scope.panelData.apcFemale = response.data.apf;
                $scope.panelData.ansarMale = response.data.am;
                $scope.panelData.ansarFemale = response.data.af;
                $scope.loading = false;
            }, function (response) {

            })
        }])
    </script>

</head>
<body class="login-page" ng-app="LoginApp">
<div class="login-box" style="margin: 1% auto !important;">
    <div class="login-logo">
        <a href="home.html"><b>Ansar & VDP</b>ERP</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
        @if(Session::has('error'))
            <p class="text-danger" style="text-align: center;text-transform: uppercase">{{Session::get('error')}}</p>
        @endif
        <form action="{{action('UserController@handleLogin')}}" method="post">
            {{csrf_field()}}
            <div class="form-group has-feedback">
                <input type="text" name="user_name" class="form-control" value="" placeholder="User Name"/>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-xs-4 col-xs-offset-8">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <a href="{{URL::route('forget_password_request')}}">I forgot my password</a><br>

    </div>
    <div class="box box-solid" ng-controller="loginController" style="margin-top: 8px;position: relative">
        <div class="overlay" ng-if="loading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
        </div>
        <h3 style="text-align: center;">কেন্দ্রীয় প্যানেল তালিকা</h3>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered" style="margin-bottom: 0 !important;">
                    <tr>
                        <th>লিঙ্গ</th>
                        <th style="width: 44%;">পদবী</th>
                        <th>মোটসংখ্যা</th>
                    </tr>
                    <tr>
                        <td rowspan="3">
                            পুরুষ
                        </td>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>3])}}">পিসি</a>
                        </td>
                        <td id="totalPCMale">[[panelData.pcMale]]</td>
                        {{--<td colspan="2" style="padding: 0">--}}
                            {{--<table class="table table-condensed custom-table" style="margin-bottom: 0 !important;">--}}
                                {{--<tr>--}}
                                    {{--<td>--}}
                                        {{--<a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>3])}}">পিসি</a>--}}
                                    {{--</td>--}}
                                    {{--<td id="totalPCMale">[[panelData.pcMale]]</td>--}}
                                {{--</tr>--}}
                                {{--<tr>--}}
                                    {{--<td>--}}
                                        {{--<a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>2])}}">এপিসি</a>--}}
                                    {{--</td>--}}
                                    {{--<td id="totalAPCMale">[[panelData.apcMale]]</td>--}}
                                {{--</tr>--}}
                                {{--<tr>--}}
                                    {{--<td>--}}
                                        {{--<a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>1])}}">আনসার</a>--}}
                                    {{--</td>--}}
                                    {{--<td id="totalAnsarMale">[[panelData.ansarMale]]</td>--}}
                                {{--</tr>--}}
                            {{--</table>--}}
                        {{--</td>--}}
                    </tr>
                    <tr>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>2])}}">এপিসি</a>
                        </td>
                        <td id="totalAPCMale">[[panelData.apcMale]]</td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>1])}}">আনসার</a>
                        </td>
                        <td id="totalAnsarMale">[[panelData.ansarMale]]</td>
                    </tr>
                    <tr>
                        <td rowspan="3">
                            মহিলা
                        </td>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>3])}}">পিসি </a>
                        </td>
                        <td id="totalPCFeMale">[[panelData.pcFemale]]</td>
                        {{--<td colspan="2" style="padding: 0">--}}
                            {{--<table class="table table-condensed custom-table" style="margin-bottom: 0 !important;">--}}
                                {{--<tr>--}}
                                    {{--<td>--}}
                                        {{--<a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>3])}}">পিসি </a>--}}
                                    {{--</td>--}}
                                    {{--<td id="totalPCFeMale">[[panelData.pcFemale]]</td>--}}
                                {{--</tr>--}}
                                {{--<tr>--}}
                                    {{--<td>--}}
                                        {{--<a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>2])}}">এপিসি </a>--}}
                                    {{--</td>--}}
                                    {{--<td id="totalAPCFeMale">[[panelData.apcFemale]]</td>--}}
                                {{--</tr>--}}
                                {{--<tr>--}}
                                    {{--<td>--}}
                                        {{--<a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>1])}}">আনসার</a>--}}
                                    {{--</td>--}}
                                    {{--<td id="totalAnsarFeMale">[[panelData.ansarFemale]]</td>--}}
                                {{--</tr>--}}
                            {{--</table>--}}
                        {{--</td>--}}
                    <tr>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>2])}}">এপিসি </a>
                        </td>
                        <td id="totalAPCFeMale">[[panelData.apcFemale]]</td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>1])}}">আনসার</a>
                        </td>
                        <td id="totalAnsarFeMale">[[panelData.ansarFemale]]</td>
                    </tr>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{asset('plugins/jQuery/jQuery-2.1.4.min.js')}}" type="text/javascript"></script>
<script src="{{asset('bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>

</body>
</html>
